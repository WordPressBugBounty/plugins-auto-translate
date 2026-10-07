<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores a short-lived, private settings draft outside canonical options.
 *
 * The browser session identifier is generated and retained by the admin client.
 * It is never stored directly: drafts retain only an HMAC fingerprint of it.
 */
class Auto_Translate_Settings_Draft {

	const DEFAULT_TTL = 1800;
	const DEFAULT_PREVIEW_TTL = 900;
	const TRANSIENT_PREFIX = 'wpat_settings_draft_';
	const OWNER_PREFIX = 'wpat_settings_draft_owner_';
	const OWNER_LOCK_PREFIX = 'wpat_settings_draft_owner_lock_';
	const LOCK_TTL = 5;

	/** @var array<string> */
	private $allowed_options;

	/** @var array<string, callable> */
	private $sanitizers;

	/** @var int */
	private $ttl;

	/** @var string */
	private $secret;

	/** @var callable|null */
	private $clock;

	/** @var array<string, string> */
	private $lock_tokens = array();

	/**
	 * @param array<string>          $allowed_options Canonical option keys which may be staged.
	 * @param array<string, callable> $sanitizers     Optional option-keyed callbacks.
	 * @param int                    $ttl             Draft lifetime in seconds.
	 * @param string                 $secret          Injectable signing secret for tests.
	 * @param callable|null          $clock           Injectable Unix-time clock for tests.
	 */
	public function __construct( $allowed_options = array(), $sanitizers = array(), $ttl = self::DEFAULT_TTL, $secret = '', $clock = null ) {
		$this->allowed_options = empty( $allowed_options ) ? self::get_default_allowed_options() : array_values( array_unique( $allowed_options ) );
		$this->sanitizers      = is_array( $sanitizers ) ? $sanitizers : array();
		$this->ttl             = max( 1, (int) $ttl );
		$this->secret          = '' !== $secret ? $secret : $this->get_secret();
		$this->clock           = is_callable( $clock ) ? $clock : null;
	}

	/**
	 * Returns the option keys registered by the free plugin's settings tabs.
	 *
	 * Lifecycle/checklist state and publication state deliberately do not belong
	 * here. They are actions with their own authorization and ordering rules.
	 *
	 * @return array<string>
	 */
	public static function get_default_allowed_options() {
		return array(
			'wpat_supported_languages', 'wpat_language_order', 'wpat_language_flags',
			'wpat_widget_type', 'wpat_button_icon', 'wpat_show_icon', 'wpat_color_1', 'wpat_color_2',
			'wpat_widget_size', 'wpat_border_radius', 'wpat_border_thickness', 'wpat_border_color',
			'wpat_font_color', 'wpat_font_family', 'wpat_dropdown_shadow', 'wpat_dropdown_border_thickness',
			'wpat_dropdown_border_color', 'wpat_dropdown_background_color', 'wpat_dropdown_hover_color',
			'wpat_dropdown_font_hover_color', 'wpat_dropdown_font_selected_color', 'wpat_dropdown_font_color',
			'wpat_dropdown_font_family', 'wpat_min_base_style', 'wpat_min_preset', 'wpat_min_style',
			'wpat_min_layout', 'wpat_min_icon', 'wpat_min_txt_display', 'wpat_min_txt_underline',
			'wpat_min_text_divider', 'wpat_min_border_thickness', 'wpat_min_border_color',
			'wpat_min_border_transparent', 'wpat_min_background_color', 'wpat_min_background_transparent',
			'wpat_min_font_color', 'wpat_min_font_family', 'wpat_min_hover_color',
			'wpat_min_hover_transparent', 'wpat_min_font_hover_color', 'wpat_min_chevron',
			'wpat_default_location', 'wpat_floating_position', 'wpat_floating_offset_x',
			'wpat_floating_offset_y', 'wpat_show_in_menu', 'wpat_menu_position', 'wpat_wrapper_selector',
			'wpat_auto_detect', 'wpat_base_language', 'wpat_language_name_display', 'wpat_custom_css',
			'wpat_min_custom_css', 'wpat_excluded_selectors', 'wpat_delete_data_on_uninstall',
		);
	}

	/**
	 * Creates an authenticated draft and returns its public record.
	 *
	 * @return array<string, mixed>|false
	 */
	public function create( $user_id, $browser_session_id, $payload = array() ) {
		if ( ! $this->is_valid_owner( $user_id, $browser_session_id ) || ! is_array( $payload ) ) {
			return false;
		}

		$payload = $this->sanitize_payload( $payload );
		if ( false === $payload ) {
			return false;
		}

		$owner_key = $this->owner_key( $user_id, $browser_session_id );
		if ( ! $this->acquire_lock( $owner_key ) ) {
			return false;
		}

		try {
			$pointer  = get_transient( self::OWNER_PREFIX . $owner_key );
			$record   = $this->get_record_for_owner( $user_id, $browser_session_id, $pointer );
			$now      = $this->now();

			if ( false !== $record ) {
				$record['payload']    = array_merge( $record['payload'], $payload );
				$record['revision']   = (int) $record['revision'] + 1;
				$record['updated_at'] = $now;
				$record['expires_at'] = $now + $this->ttl;
				$this->write_record( $record );
				set_transient( self::OWNER_PREFIX . $owner_key, $record['draft_id'], $this->ttl );

				return $this->public_record( $record );
			}

			$record = array(
				'draft_id'        => $this->generate_draft_id(),
				'user_id'         => (int) $user_id,
				'session_hash'    => $this->session_hash( $browser_session_id ),
				'payload'         => $payload,
				'revision'        => 1,
				'created_at'      => $now,
				'updated_at'      => $now,
				'expires_at'      => $now + $this->ttl,
			);

			$this->write_record( $record );
			set_transient( self::OWNER_PREFIX . $owner_key, $record['draft_id'], $this->ttl );

			return $this->public_record( $record );
		} finally {
			$this->release_lock( $owner_key );
		}
	}

	/**
	 * Merges allowed settings into the current draft and increments its revision.
	 *
	 * @return array<string, mixed>|false
	 */
	public function update( $user_id, $browser_session_id, $draft_id, $payload, $expected_revision = null ) {
		if ( ! $this->is_valid_owner( $user_id, $browser_session_id ) || ! is_array( $payload ) ) {
			return false;
		}

		$payload = $this->sanitize_payload( $payload );
		$owner_key = $this->owner_key( $user_id, $browser_session_id );
		if ( false === $payload || ! $this->acquire_lock( $owner_key ) ) {
			return false;
		}

		try {
			$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );
			if ( false === $record ) {
				return false;
			}

			// A stale request is safe to merge because requests contain only the
			// fields edited on the current tab. Never replace the complete draft
			// with that partial payload on a revision mismatch.
			$record['payload']    = array_merge( $record['payload'], $payload );
			$record['revision']   = (int) $record['revision'] + 1;
			$record['updated_at'] = $this->now();
			$record['expires_at'] = $record['updated_at'] + $this->ttl;
			$this->write_record( $record );
			set_transient( self::OWNER_PREFIX . $this->owner_key( $user_id, $browser_session_id ), $draft_id, $this->ttl );

			return $this->public_record( $record );
		} finally {
			$this->release_lock( $owner_key );
		}
	}

	/**
	 * Backwards-friendly alias for callers that describe an autosave as setting a draft.
	 *
	 * @return array<string, mixed>|false
	 */
	public function set( $user_id, $browser_session_id, $draft_id, $payload, $expected_revision = null ) {
		return $this->update( $user_id, $browser_session_id, $draft_id, $payload, $expected_revision );
	}

	/** @return array<string, mixed>|false */
	public function get( $user_id, $browser_session_id, $draft_id ) {
		$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );

		return false === $record ? false : $this->public_record( $record );
	}

	/**
	 * Retrieves lightweight state for an admin header without returning staged values.
	 *
	 * @return array<string, mixed>|false
	 */
	public function get_status( $user_id, $browser_session_id, $draft_id ) {
		$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );
		if ( false === $record ) {
			return false;
		}

		return array(
			'draft_id'   => $record['draft_id'],
			'revision'   => (int) $record['revision'],
			'expires_at' => (int) $record['expires_at'],
			'dirty'      => ! empty( $record['payload'] ),
		);
	}

	/** @return bool */
	public function discard( $user_id, $browser_session_id, $draft_id ) {
		if ( ! $this->is_valid_owner( $user_id, $browser_session_id ) ) {
			return false;
		}

		$owner_key = $this->owner_key( $user_id, $browser_session_id );
		if ( ! $this->acquire_lock( $owner_key ) ) {
			return false;
		}

		try {
			// Discard is intentionally idempotent when the client has not yet
			// received a draft ID. Resolve the owner pointer so a fast discard can
			// clean up a draft created by an in-flight autosave, while a genuinely
			// empty owner state is still a successful no-op.
			if ( '' === $draft_id ) {
				$draft_id = get_transient( self::OWNER_PREFIX . $owner_key );
				if ( ! is_string( $draft_id ) || '' === $draft_id ) {
					return true;
				}
			}

			$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );
			if ( false === $record ) {
				// Discard is idempotent: an expired or already-removed draft is
				// already in the requested state. The owner-pointer check prevents
				// an unrelated draft from being removed.
				$this->delete_owner_pointer( $user_id, $browser_session_id, $draft_id );
				return true;
			}

			$deleted = (bool) delete_transient( $this->transient_key( $draft_id ) );
			$this->delete_owner_pointer( $user_id, $browser_session_id, $draft_id );

			return $deleted;
		} finally {
			$this->release_lock( $owner_key );
		}
	}

	/**
	 * Commits the whole staged payload and then removes the private draft.
	 *
	 * @return array<string, mixed>|false Committed payload or false on authorization/revision failure.
	 */
	public function commit( $user_id, $browser_session_id, $draft_id, $expected_revision = null ) {
		if ( ! $this->is_valid_owner( $user_id, $browser_session_id ) ) {
			return false;
		}

		$owner_key = $this->owner_key( $user_id, $browser_session_id );
		if ( ! $this->acquire_lock( $owner_key ) ) {
			return false;
		}

		try {
			$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );
			if ( false === $record || ( null !== $expected_revision && (int) $expected_revision !== (int) $record['revision'] ) ) {
				return false;
			}

			foreach ( $record['payload'] as $option => $value ) {
				update_option( $option, $value );
			}

			delete_transient( $this->transient_key( $draft_id ) );
			$this->delete_owner_pointer( $user_id, $browser_session_id, $draft_id );

			return $record['payload'];
		} finally {
			$this->release_lock( $owner_key );
		}
	}

	/**
	 * Creates a signed token for a private frontend preview of this draft.
	 *
	 * The token remains valid across revisions so an already-open preview can
	 * reload after autosaves. Owner and browser-session verification remains
	 * mandatory in the frontend request path.
	 *
	 * @return string|false
	 */
	public function issue_preview_token( $user_id, $browser_session_id, $draft_id, $ttl = self::DEFAULT_PREVIEW_TTL ) {
		$record = $this->get_record_for_owner( $user_id, $browser_session_id, $draft_id );
		if ( false === $record ) {
			return false;
		}

		$expires_at = min( (int) $record['expires_at'], $this->now() + max( 1, (int) $ttl ) );
		$claims     = array(
			'v' => 1, 'd' => $record['draft_id'], 'u' => (int) $record['user_id'],
			's' => $record['session_hash'], 'e' => $expires_at,
		);
		$encoded    = $this->base64url_encode( wp_json_encode( $claims ) );

		return $encoded . '.' . hash_hmac( 'sha256', $encoded, $this->secret );
	}

	/**
	 * Validates a preview token and returns only the currently tokened draft.
	 * This method is intentionally usable on the frontend without a WP login.
	 *
	 * @return array<string, mixed>|false
	 */
	public function validate_preview_token( $token ) {
		$claims = $this->validate_preview_claims( $token );
		if ( false === $claims ) {
			return false;
		}

		$record = $this->read_record( $claims['d'] );
		if ( false === $record || (int) $record['user_id'] !== (int) $claims['u'] || ! hash_equals( $record['session_hash'], $claims['s'] ) ) {
			return false;
		}

		return $this->public_record( $record );
	}

	/**
	 * Validate a preview token for the logged-in editor and browser session that
	 * created it. This prevents an accidentally shared preview URL from exposing
	 * staged settings to another visitor or administrator.
	 *
	 * @param string $token              Signed preview token.
	 * @param int    $user_id            Current WordPress user ID.
	 * @param string $browser_session_id Browser session identifier from its HTTP-only cookie.
	 * @return array<string, mixed>|false
	 */
	public function validate_preview_token_for_owner( $token, $user_id, $browser_session_id ) {
		$claims = $this->validate_preview_claims( $token );
		if ( false === $claims || (int) $claims['u'] !== (int) $user_id ) {
			return false;
		}

		$record = $this->get_record_for_owner( $user_id, $browser_session_id, $claims['d'] );
		if ( false === $record ) {
			return false;
		}

		return $this->public_record( $record );
	}

	/** @return array<string, mixed>|false */
	private function get_record_for_owner( $user_id, $browser_session_id, $draft_id ) {
		if ( ! $this->is_valid_owner( $user_id, $browser_session_id ) ) {
			return false;
		}

		$record = $this->read_record( $draft_id );
		if ( false === $record || (int) $record['user_id'] !== (int) $user_id || ! hash_equals( $record['session_hash'], $this->session_hash( $browser_session_id ) ) ) {
			return false;
		}

		return $record;
	}

	/** @return array<string, mixed>|false */
	private function read_record( $draft_id ) {
		if ( ! $this->is_valid_draft_id( $draft_id ) ) {
			return false;
		}

		$record = get_transient( $this->transient_key( $draft_id ) );
		if ( ! is_array( $record ) || ! isset( $record['expires_at'], $record['draft_id'], $record['payload'], $record['revision'], $record['user_id'], $record['session_hash'] ) || $this->now() >= (int) $record['expires_at'] ) {
			delete_transient( $this->transient_key( $draft_id ) );
			return false;
		}

		return $record;
	}

	/** @param array<string, mixed> $record */
	private function write_record( $record ) {
		set_transient( $this->transient_key( $record['draft_id'] ), $record, max( 1, (int) $record['expires_at'] - $this->now() ) );
	}

	/**
	 * Serialize read/merge/write updates across overlapping requests.
	 *
	 * `add_option()` is backed by a unique database key in WordPress, making it
	 * suitable as a small mutex without requiring a custom table or object-cache
	 * feature. A short expiry allows recovery if a PHP worker exits unexpectedly.
	 *
	 * @param string $lock_id Owner/session lock identifier.
	 * @return bool
	 */
	private function acquire_lock( $lock_id ) {
		$key   = self::OWNER_LOCK_PREFIX . $lock_id;
		$token = $this->generate_draft_id();
		$until = $this->now() + self::LOCK_TTL;

		for ( $attempt = 0; 25 > $attempt; $attempt++ ) {
			if ( add_option(
				$key,
				array(
					'token'      => $token,
					'expires_at' => $until,
				),
				'',
				'no'
			) ) {
				$this->lock_tokens[ $key ] = $token;
				return true;
			}

			$lock = get_option( $key, array() );
			if ( is_array( $lock ) && isset( $lock['expires_at'] ) && $this->now() >= (int) $lock['expires_at'] ) {
				delete_option( $key );
			}

			usleep( 10000 );
		}

		return false;
	}

	/**
	 * Release only our own mutex so an expired/replaced lock is not disturbed.
	 *
	 * @param string $lock_id Owner/session lock identifier.
	 * @return void
	 */
	private function release_lock( $lock_id ) {
		$key  = self::OWNER_LOCK_PREFIX . $lock_id;
		$lock = get_option( $key, array() );
		$token = $this->lock_tokens[ $key ] ?? '';

		if ( is_array( $lock ) && '' !== $token && ( $lock['token'] ?? '' ) === $token ) {
			delete_option( $key );
		}

		unset( $this->lock_tokens[ $key ] );
	}

	/**
	 * Build an opaque owner/session key for pointers and mutation locks.
	 *
	 * @param int    $user_id             WordPress user ID.
	 * @param string $browser_session_id Browser session identifier.
	 * @return string
	 */
	private function owner_key( $user_id, $browser_session_id ) {
		return hash_hmac( 'sha256', (string) (int) $user_id . '|' . $browser_session_id, $this->secret );
	}

	/**
	 * Remove an owner pointer only when it still references the supplied draft.
	 *
	 * @param int    $user_id             WordPress user ID.
	 * @param string $browser_session_id Browser session identifier.
	 * @param string $draft_id            Draft identifier.
	 * @return void
	 */
	private function delete_owner_pointer( $user_id, $browser_session_id, $draft_id ) {
		$key = self::OWNER_PREFIX . $this->owner_key( $user_id, $browser_session_id );
		if ( get_transient( $key ) === $draft_id ) {
			delete_transient( $key );
		}
	}

	/** @return array<string, mixed>|false */
	private function sanitize_payload( $payload ) {
		$sanitized = array();
		foreach ( $payload as $option => $value ) {
			if ( ! is_string( $option ) || ! in_array( $option, $this->allowed_options, true ) ) {
				return false;
			}
			if ( isset( $this->sanitizers[ $option ] ) && is_callable( $this->sanitizers[ $option ] ) ) {
				$value = call_user_func( $this->sanitizers[ $option ], $value );
			}
			$sanitized[ $option ] = $value;
		}

		return $sanitized;
	}

	/** @return array<string, mixed>|false */
	private function validate_preview_claims( $token ) {
		if ( ! is_string( $token ) || false === strpos( $token, '.' ) ) {
			return false;
		}
		$parts = explode( '.', $token );
		if ( 2 !== count( $parts ) || ! hash_equals( hash_hmac( 'sha256', $parts[0], $this->secret ), $parts[1] ) ) {
			return false;
		}
		$claims = json_decode( $this->base64url_decode( $parts[0] ), true );
		if ( ! is_array( $claims ) || 1 !== (int) ( $claims['v'] ?? 0 ) || $this->now() >= (int) ( $claims['e'] ?? 0 ) || ! isset( $claims['d'], $claims['u'], $claims['s'] ) || ! $this->is_valid_draft_id( $claims['d'] ) || ! is_string( $claims['s'] ) ) {
			return false;
		}

		return $claims;
	}

	private function is_valid_owner( $user_id, $browser_session_id ) {
		return (int) $user_id > 0 && is_string( $browser_session_id ) && 16 <= strlen( $browser_session_id ) && 128 >= strlen( $browser_session_id );
	}

	private function is_valid_draft_id( $draft_id ) {
		return is_string( $draft_id ) && 1 === preg_match( '/^[a-f0-9]{48}$/', $draft_id );
	}

	private function generate_draft_id() {
		try {
			return bin2hex( random_bytes( 24 ) );
		} catch ( Exception $exception ) {
			return substr( hash( 'sha256', uniqid( (string) wp_rand(), true ) . $this->secret ), 0, 48 );
		}
	}

	private function transient_key( $draft_id ) {
		return self::TRANSIENT_PREFIX . $draft_id;
	}

	private function session_hash( $browser_session_id ) {
		return hash_hmac( 'sha256', $browser_session_id, $this->secret );
	}

	private function get_secret() {
		// This service is instantiated during plugin bootstrap as well as on
		// frontend requests. AUTH_SALT is available in both phases, whereas
		// wp_salt() is loaded later on some WordPress installations.
		if ( defined( 'AUTH_SALT' ) ) {
			return AUTH_SALT;
		}

		return function_exists( 'wp_salt' ) ? wp_salt( 'auth' ) : __FILE__;
	}

	private function now() {
		return $this->clock ? (int) call_user_func( $this->clock ) : time();
	}

	private function public_record( $record ) {
		return array(
			'draft_id'   => $record['draft_id'],
			'payload'    => $record['payload'],
			'revision'   => (int) $record['revision'],
			'expires_at' => (int) $record['expires_at'],
		);
	}

	private function base64url_encode( $value ) {
		return rtrim( strtr( base64_encode( $value ), '+/', '-_' ), '=' );
	}

	private function base64url_decode( $value ) {
		$remainder = strlen( $value ) % 4;
		if ( $remainder ) {
			$value .= str_repeat( '=', 4 - $remainder );
		}

		return (string) base64_decode( strtr( $value, '-_', '+/' ), true );
	}
}
