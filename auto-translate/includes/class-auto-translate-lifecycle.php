<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Local lifecycle tracking for preview/live mode transitions.
 *
 * @since 2.1.1
 */
class Auto_Translate_Lifecycle {

	/**
	 * Internal option name.
	 */
	const OPTION_NAME = 'wpat_lifecycle';

	/**
	 * Option schema version.
	 */
	const VERSION = 1;

	/**
	 * Option tied to mode transitions.
	 */
	const MODE_OPTION = 'wpat_go_live';

	/**
	 * Custom Appsero metadata schema version.
	 */
	const APPSERO_METADATA_SCHEMA = 1;

	/**
	 * Lifecycle action bit values.
	 */
	const ACTION_BITS = array(
		'preview_site'           => 1,
		'go_live'                => 2,
		'choose_languages'       => 4,
		'adjust_style'           => 8,
		'set_placement'          => 16,
		'save_language_settings' => 32,
		'save_placement'         => 64,
		'save_styling'           => 128,
		'save_advanced'          => 256,
	);

	/**
	 * Transition context for the current request.
	 *
	 * @var array<string, mixed>|null
	 */
	private static $transition_context = null;

	/**
	 * Track the activation default transition into preview mode.
	 *
	 * @return void
	 */
	public static function track_activation_default() {
		self::ensure_activation_state( false );
		self::record_transition( 'unknown', 'preview', 'activation_default', 0 );
	}

	/**
	 * Ensure lifecycle activation metadata exists.
	 *
	 * @param bool $is_existing_installation Whether this activation is an upgrade/existing install backfill.
	 * @return void
	 */
	public static function ensure_activation_state( $is_existing_installation ) {
		$snapshot = self::get_snapshot();

		if ( '' === $snapshot['activated_at'] ) {
			$snapshot['activated_at'] = current_time( 'mysql' );
		}

		if ( '' === $snapshot['first_seen_version'] ) {
			$snapshot['first_seen_version'] = defined( 'AUTO_TRANSLATE_VERSION' ) ? AUTO_TRANSLATE_VERSION : 'unknown';
		}

		if ( $is_existing_installation ) {
			$snapshot['is_upgraded_install'] = 1;
		}

		update_option( self::OPTION_NAME, $snapshot );
	}

	/**
	 * Record that the settings dashboard was viewed.
	 *
	 * @return void
	 */
	public static function record_dashboard_seen() {
		self::record_first_timestamp( 'dashboard_seen_at' );
	}

	/**
	 * Record a launch action.
	 *
	 * @param string $action Action key.
	 * @return void
	 */
	public static function record_action( $action ) {
		$action = self::sanitize_action( $action );
		if ( '' === $action ) {
			return;
		}

		$snapshot = self::get_snapshot();
		$key      = 'clicked_' . $action;

		if ( array_key_exists( $key, $snapshot['actions'] ) ) {
			$snapshot['actions'][ $key ] = 1;
			update_option( self::OPTION_NAME, $snapshot );
		}
	}

	/**
	 * Record a settings save from an admin tab.
	 *
	 * @param string $tab Settings tab key.
	 * @return void
	 */
	public static function record_settings_save( $tab ) {
		$tab = self::sanitize_settings_tab( $tab );
		if ( '' === $tab ) {
			return;
		}

		$snapshot = self::get_snapshot();
		$action   = 'saved_' . $tab;

		if ( array_key_exists( $action, $snapshot['actions'] ) ) {
			$snapshot['actions'][ $action ] = 1;
		}

		$snapshot['settings_saved_count']++;
		$snapshot['settings_saved_by_tab'][ $tab ]++;
		update_option( self::OPTION_NAME, $snapshot );
	}

	/**
	 * Record that the frontend selector rendered.
	 *
	 * @param string $mode Current frontend mode.
	 * @return void
	 */
	public static function record_frontend_render( $mode ) {
		$mode     = self::sanitize_mode( $mode, 'preview' );
		$snapshot = self::get_snapshot();

		$snapshot['switcher_rendered_once'] = 1;

		if ( 'live' === $mode ) {
			if ( '' === $snapshot['frontend_public_rendered_at'] ) {
				$snapshot['frontend_public_rendered_at'] = current_time( 'mysql' );
			}
		} elseif ( '' === $snapshot['frontend_preview_rendered_at'] ) {
			$snapshot['frontend_preview_rendered_at'] = current_time( 'mysql' );
		}

		update_option( self::OPTION_NAME, $snapshot );
	}

	/**
	 * Record that the browser loaded the translation script.
	 *
	 * @return void
	 */
	public static function record_translation_script_loaded() {
		$snapshot = self::get_snapshot();

		if ( 1 === $snapshot['translation_script_loaded'] ) {
			return;
		}

		$snapshot['translation_script_loaded'] = 1;
		update_option( self::OPTION_NAME, $snapshot );
	}

	/**
	 * Set the transition context for the next mode change.
	 *
	 * @param string $trigger Trigger name.
	 * @param int    $user_id User ID.
	 * @return void
	 */
	public static function set_transition_context( $trigger, $user_id ) {
		self::$transition_context = array(
			'trigger' => self::sanitize_trigger( $trigger ),
			'user_id' => absint( $user_id ),
		);
	}

	/**
	 * Clear any pending transition context.
	 *
	 * @return void
	 */
	public static function clear_transition_context() {
		self::$transition_context = null;
	}

	/**
	 * Return a sanitized lifecycle snapshot.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_snapshot() {
		return self::sanitize_snapshot(
			get_option( self::OPTION_NAME, array() ),
			self::mode_from_option_value( get_option( self::MODE_OPTION, false ), 'preview' )
		);
	}

	/**
	 * Return the current tracked mode.
	 *
	 * @return string
	 */
	public static function get_current_mode() {
		$snapshot = self::get_snapshot();

		return $snapshot['current_mode'];
	}

	/**
	 * Return custom metadata for Appsero extra fields.
	 *
	 * @return array<string, mixed>
	 */
	public static function get_appsero_metadata() {
		$snapshot = self::get_snapshot();
		$actions  = $snapshot['actions'];

		return array(
			'wpat_lifecycle_schema'              => self::APPSERO_METADATA_SCHEMA,
			'wpat_lifecycle_code'                => self::get_lifecycle_code( $actions ),
			'wpat_clicked_preview_site'          => $actions['clicked_preview_site'],
			'wpat_clicked_go_live'               => $actions['clicked_go_live'],
			'wpat_clicked_choose_languages'      => $actions['clicked_choose_languages'],
			'wpat_clicked_adjust_style'          => $actions['clicked_adjust_style'],
			'wpat_clicked_set_placement'         => $actions['clicked_set_placement'],
			'wpat_saved_language_settings'       => $actions['saved_language_settings'],
			'wpat_saved_placement'               => $actions['saved_placement'],
			'wpat_saved_styling'                 => $actions['saved_styling'],
			'wpat_saved_advanced'                => $actions['saved_advanced'],
			'wpat_settings_saved_count'          => $snapshot['settings_saved_count'],
			'wpat_dashboard_seen'                => '' === $snapshot['dashboard_seen_at'] ? 0 : 1,
			'wpat_frontend_preview_rendered'     => '' === $snapshot['frontend_preview_rendered_at'] ? 0 : 1,
			'wpat_frontend_public_rendered'      => '' === $snapshot['frontend_public_rendered_at'] ? 0 : 1,
			'wpat_launch_mode'                   => $snapshot['current_mode'],
			'wpat_launch_path'                   => self::get_launch_path( $snapshot ),
			'wpat_went_live_then_back_to_preview' => self::went_live_then_back_to_preview( $snapshot ) ? 1 : 0,
			'wpat_selected_languages_count'      => self::get_selected_languages_count(),
			'wpat_switcher_placement'            => self::get_switcher_placement(),
			'wpat_switcher_style'                => self::get_switcher_style(),
			'wpat_auto_translate_enabled'        => 'enabled' === get_option( 'wpat_auto_detect', 'enabled' ) ? 1 : 0,
			'wpat_flags_enabled'                 => self::flags_enabled() ? 1 : 0,
			'wpat_language_names_enabled'        => self::language_names_enabled() ? 1 : 0,
			'wpat_layout_mode'                   => self::get_layout_mode(),
			'wpat_seconds_since_activation'      => self::seconds_since( $snapshot['activated_at'], 0 ),
			'wpat_seconds_to_dashboard'          => self::seconds_between( $snapshot['activated_at'], $snapshot['dashboard_seen_at'] ),
			'wpat_seconds_to_go_live'            => self::seconds_between( $snapshot['activated_at'], $snapshot['go_live_enabled_at'] ),
			'wpat_first_seen_version'            => '' === $snapshot['first_seen_version'] ? 'unknown' : $snapshot['first_seen_version'],
			'wpat_current_version'               => defined( 'AUTO_TRANSLATE_VERSION' ) ? AUTO_TRANSLATE_VERSION : 'unknown',
			'wpat_detected_builders'             => self::get_detected_builders(),
			'wpat_switcher_rendered_once'        => $snapshot['switcher_rendered_once'],
			'wpat_translation_script_loaded'     => $snapshot['translation_script_loaded'],
		);
	}

	/**
	 * WordPress hook callback for `update_option_wpat_go_live`.
	 *
	 * @param mixed  $old_value Previous option value.
	 * @param mixed  $value     New option value.
	 * @param string $option    Option name.
	 * @return void
	 */
	public function handle_go_live_option_update( $old_value, $value, $option ) {
		if ( self::MODE_OPTION !== $option ) {
			return;
		}

		$context = self::$transition_context;
		self::clear_transition_context();

		$trigger = isset( $context['trigger'] ) ? (string) $context['trigger'] : 'settings_save';
		$user_id = isset( $context['user_id'] ) ? absint( $context['user_id'] ) : self::get_default_user_id_for_trigger( $trigger );

		$this->maybe_track_option_transition( $old_value, $value, $trigger, $user_id );
	}

	/**
	 * Track a mode transition from an option change if it actually changed modes.
	 *
	 * @param mixed  $old_value Previous option value.
	 * @param mixed  $new_value New option value.
	 * @param string $trigger   Transition trigger.
	 * @param int    $user_id   User ID.
	 * @return void
	 */
	public function maybe_track_option_transition( $old_value, $new_value, $trigger, $user_id ) {
		$previous_mode = self::mode_from_option_value( $old_value, 'unknown' );
		$current_mode  = self::mode_from_option_value( $new_value, 'preview' );

		if ( $previous_mode === $current_mode ) {
			return;
		}

		self::record_transition(
			$previous_mode,
			$current_mode,
			self::sanitize_trigger( $trigger ),
			absint( $user_id )
		);
	}

	/**
	 * Persist a lifecycle transition to local plugin state.
	 *
	 * @param string $previous_mode Previous mode.
	 * @param string $current_mode  Current mode.
	 * @param string $trigger       Transition trigger.
	 * @param int    $user_id       User ID.
	 * @return void
	 */
	private static function record_transition( $previous_mode, $current_mode, $trigger, $user_id ) {
		$previous_mode = self::sanitize_mode( $previous_mode, 'unknown' );
		$current_mode  = self::sanitize_mode( $current_mode, 'preview' );
		$trigger       = self::sanitize_trigger( $trigger );
		$user_id       = absint( $user_id );
		$event_name    = self::event_name_for_mode( $current_mode );
		$timestamp     = current_time( 'mysql' );
		$snapshot      = self::get_snapshot();

		if ( '' === $snapshot['first_event_at'] ) {
			$snapshot['first_event_at'] = $timestamp;
		}

		if ( '' === $snapshot['activated_at'] ) {
			$snapshot['activated_at'] = $timestamp;
		}

		if ( '' === $snapshot['first_seen_version'] ) {
			$snapshot['first_seen_version'] = defined( 'AUTO_TRANSLATE_VERSION' ) ? AUTO_TRANSLATE_VERSION : 'unknown';
		}

		$snapshot['version']      = self::VERSION;
		$snapshot['current_mode'] = $current_mode;
		$snapshot['last_event_at'] = $timestamp;
		$snapshot['event_counts']['total']++;
		$snapshot['event_counts'][ $event_name ]++;

		if ( 'live' === $current_mode ) {
			$snapshot['actions']['clicked_go_live'] = 1;
			if ( '' === $snapshot['go_live_enabled_at'] ) {
				$snapshot['go_live_enabled_at'] = $timestamp;
			}
		} elseif ( 'live' === $previous_mode && '' === $snapshot['go_live_disabled_at'] ) {
			$snapshot['go_live_disabled_at'] = $timestamp;
		}

		$snapshot['last_event'] = array(
			'name'          => $event_name,
			'recorded_at'   => $timestamp,
			'previous_mode' => $previous_mode,
			'current_mode'  => $current_mode,
			'trigger'       => $trigger,
			'option_name'   => self::MODE_OPTION,
			'user_id'       => $user_id,
		);

		update_option( self::OPTION_NAME, $snapshot );
	}

	/**
	 * Normalize the stored lifecycle snapshot.
	 *
	 * @param mixed $snapshot Raw option value.
	 * @return array<string, mixed>
	 */
	private static function sanitize_snapshot( $snapshot, $default_current_mode ) {
		$snapshot = is_array( $snapshot ) ? $snapshot : array();

		$counts = isset( $snapshot['event_counts'] ) && is_array( $snapshot['event_counts'] )
			? $snapshot['event_counts']
			: array();
		$last_event = isset( $snapshot['last_event'] ) && is_array( $snapshot['last_event'] )
			? $snapshot['last_event']
			: array();
		$actions = isset( $snapshot['actions'] ) && is_array( $snapshot['actions'] )
			? $snapshot['actions']
			: array();
		$settings_saved_by_tab = isset( $snapshot['settings_saved_by_tab'] ) && is_array( $snapshot['settings_saved_by_tab'] )
			? $snapshot['settings_saved_by_tab']
			: array();

		return array(
			'version'                     => self::VERSION,
			'activated_at'                => self::sanitize_timestamp( $snapshot['activated_at'] ?? '' ),
			'first_seen_version'          => self::sanitize_version( $snapshot['first_seen_version'] ?? '' ),
			'is_upgraded_install'         => ! empty( $snapshot['is_upgraded_install'] ) ? 1 : 0,
			'current_mode'                => self::sanitize_mode( $snapshot['current_mode'] ?? $default_current_mode, $default_current_mode ),
			'first_event_at'              => self::sanitize_timestamp( $snapshot['first_event_at'] ?? '' ),
			'last_event_at'               => self::sanitize_timestamp( $snapshot['last_event_at'] ?? '' ),
			'dashboard_seen_at'           => self::sanitize_timestamp( $snapshot['dashboard_seen_at'] ?? '' ),
			'go_live_enabled_at'          => self::sanitize_timestamp( $snapshot['go_live_enabled_at'] ?? '' ),
			'go_live_disabled_at'         => self::sanitize_timestamp( $snapshot['go_live_disabled_at'] ?? '' ),
			'frontend_preview_rendered_at' => self::sanitize_timestamp( $snapshot['frontend_preview_rendered_at'] ?? '' ),
			'frontend_public_rendered_at' => self::sanitize_timestamp( $snapshot['frontend_public_rendered_at'] ?? '' ),
			'settings_saved_count'        => absint( $snapshot['settings_saved_count'] ?? 0 ),
			'settings_saved_by_tab'       => array(
				'language_settings' => absint( $settings_saved_by_tab['language_settings'] ?? 0 ),
				'placement'         => absint( $settings_saved_by_tab['placement'] ?? 0 ),
				'styling'           => absint( $settings_saved_by_tab['styling'] ?? 0 ),
				'advanced'          => absint( $settings_saved_by_tab['advanced'] ?? 0 ),
			),
			'actions'                    => array(
				'clicked_preview_site'     => ! empty( $actions['clicked_preview_site'] ) ? 1 : 0,
				'clicked_go_live'          => ! empty( $actions['clicked_go_live'] ) ? 1 : 0,
				'clicked_choose_languages' => ! empty( $actions['clicked_choose_languages'] ) ? 1 : 0,
				'clicked_adjust_style'     => ! empty( $actions['clicked_adjust_style'] ) ? 1 : 0,
				'clicked_set_placement'    => ! empty( $actions['clicked_set_placement'] ) ? 1 : 0,
				'saved_language_settings'  => ! empty( $actions['saved_language_settings'] ) ? 1 : 0,
				'saved_placement'          => ! empty( $actions['saved_placement'] ) ? 1 : 0,
				'saved_styling'            => ! empty( $actions['saved_styling'] ) ? 1 : 0,
				'saved_advanced'           => ! empty( $actions['saved_advanced'] ) ? 1 : 0,
			),
			'switcher_rendered_once'     => ! empty( $snapshot['switcher_rendered_once'] ) ? 1 : 0,
			'translation_script_loaded'  => ! empty( $snapshot['translation_script_loaded'] ) ? 1 : 0,
			'event_counts'              => array(
				'total'                     => absint( $counts['total'] ?? 0 ),
				'wpat_preview_mode_enabled' => absint( $counts['wpat_preview_mode_enabled'] ?? 0 ),
				'wpat_go_live_enabled'      => absint( $counts['wpat_go_live_enabled'] ?? 0 ),
			),
			'last_event'                  => array(
				'name'          => self::sanitize_event_name( $last_event['name'] ?? '' ),
				'recorded_at'   => self::sanitize_timestamp( $last_event['recorded_at'] ?? '' ),
				'previous_mode' => self::sanitize_mode( $last_event['previous_mode'] ?? 'unknown', 'unknown' ),
				'current_mode'  => self::sanitize_mode( $last_event['current_mode'] ?? 'unknown', 'unknown' ),
				'trigger'       => self::sanitize_trigger( $last_event['trigger'] ?? 'settings_save' ),
				'option_name'   => self::sanitize_option_name( $last_event['option_name'] ?? self::MODE_OPTION ),
				'user_id'       => absint( $last_event['user_id'] ?? 0 ),
			),
		);
	}

	/**
	 * Resolve a live/preview mode from the go-live option value.
	 *
	 * @param mixed  $value   Option value.
	 * @param string $default Default mode.
	 * @return string
	 */
	private static function mode_from_option_value( $value, $default ) {
		if ( is_bool( $value ) ) {
			return $value ? 'live' : 'preview';
		}

		if ( is_numeric( $value ) ) {
			return (int) $value === 1 ? 'live' : 'preview';
		}

		if ( is_string( $value ) ) {
			$normalized = strtolower( trim( $value ) );

			if ( in_array( $normalized, array( '1', 'true', 'on', 'yes', 'live' ), true ) ) {
				return 'live';
			}

			if ( in_array( $normalized, array( '0', 'false', 'off', 'no', '', 'preview' ), true ) ) {
				return 'preview';
			}
		}

		return self::sanitize_mode( $default, 'unknown' );
	}

	/**
	 * Return the event name for the current mode.
	 *
	 * @param string $mode Current mode.
	 * @return string
	 */
	private static function event_name_for_mode( $mode ) {
		return 'live' === $mode ? 'wpat_go_live_enabled' : 'wpat_preview_mode_enabled';
	}

	/**
	 * Resolve the default user ID for a trigger.
	 *
	 * @param string $trigger Trigger name.
	 * @return int
	 */
	private static function get_default_user_id_for_trigger( $trigger ) {
		if ( 'activation_default' === $trigger || ! function_exists( 'get_current_user_id' ) ) {
			return 0;
		}

		return absint( get_current_user_id() );
	}

	/**
	 * Record a timestamp only once.
	 *
	 * @param string $field Snapshot field.
	 * @return void
	 */
	private static function record_first_timestamp( $field ) {
		$allowed = array( 'dashboard_seen_at' );
		if ( ! in_array( $field, $allowed, true ) ) {
			return;
		}

		$snapshot = self::get_snapshot();
		if ( '' === $snapshot[ $field ] ) {
			$snapshot[ $field ] = current_time( 'mysql' );
			update_option( self::OPTION_NAME, $snapshot );
		}
	}

	/**
	 * Compute lifecycle action bitmask.
	 *
	 * @param array<string, int> $actions Action map.
	 * @return int
	 */
	private static function get_lifecycle_code( $actions ) {
		$code = 0;

		foreach ( self::ACTION_BITS as $action => $bit ) {
			$key = false === strpos( $action, 'save_' ) ? 'clicked_' . $action : 'saved_' . substr( $action, 5 );

			if ( ! empty( $actions[ $key ] ) ) {
				$code += $bit;
			}
		}

		return $code;
	}

	/**
	 * Resolve lifecycle launch path.
	 *
	 * @param array<string, mixed> $snapshot Lifecycle snapshot.
	 * @return string
	 */
	private static function get_launch_path( $snapshot ) {
		$current_mode = (string) $snapshot['current_mode'];
		$live_count   = absint( $snapshot['event_counts']['wpat_go_live_enabled'] ?? 0 );

		if ( self::went_live_then_back_to_preview( $snapshot ) ) {
			return 'live_then_preview';
		}

		if ( 'live' === $current_mode ) {
			return ! empty( $snapshot['is_upgraded_install'] ) && 0 === $live_count ? 'upgraded_live' : 'live';
		}

		if ( 0 === self::get_lifecycle_code( $snapshot['actions'] ) && 0 === absint( $snapshot['settings_saved_count'] ) ) {
			return 'fresh_preview';
		}

		return 'preview' === $current_mode ? 'preview_never_live' : 'unknown';
	}

	/**
	 * Whether install went live and then returned to preview.
	 *
	 * @param array<string, mixed> $snapshot Lifecycle snapshot.
	 * @return bool
	 */
	private static function went_live_then_back_to_preview( $snapshot ) {
		return 'preview' === (string) $snapshot['current_mode']
			&& '' !== (string) $snapshot['go_live_enabled_at']
			&& '' !== (string) $snapshot['go_live_disabled_at'];
	}

	/**
	 * Count selected languages.
	 *
	 * @return int
	 */
	private static function get_selected_languages_count() {
		$selected = get_option( 'wpat_supported_languages', array() );

		if ( ! is_array( $selected ) ) {
			return 0;
		}

		if ( in_array( 'all', $selected, true ) && class_exists( 'Auto_Translate_Config' ) ) {
			return count( Auto_Translate_Config::get_supported_languages() );
		}

		return count( array_filter( $selected, 'is_scalar' ) );
	}

	/**
	 * Get coarse switcher placement.
	 *
	 * @return string
	 */
	private static function get_switcher_placement() {
		$floating = (bool) get_option( 'wpat_default_location', true );
		$menu     = '' !== (string) get_option( 'wpat_show_in_menu', '' );

		if ( $floating && $menu ) {
			return 'mixed';
		}

		if ( $menu ) {
			return 'menu';
		}

		if ( $floating ) {
			return self::sanitize_floating_position( get_option( 'wpat_floating_position', 'bottom_left' ) );
		}

		return 'manual';
	}

	/**
	 * Get coarse layout mode.
	 *
	 * @return string
	 */
	private static function get_layout_mode() {
		$placement = self::get_switcher_placement();

		if ( in_array( $placement, array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ), true ) ) {
			return 'floating';
		}

		return in_array( $placement, array( 'menu', 'mixed', 'manual', 'none' ), true ) ? $placement : 'unknown';
	}

	/**
	 * Get current selector style.
	 *
	 * @return string
	 */
	private static function get_switcher_style() {
		$style = sanitize_key( (string) get_option( 'wpat_min_style', 'flags' ) );

		return in_array( $style, array( 'flags', 'emoji_flags', 'flat_flags', 'icon', 'clean' ), true ) ? $style : 'unknown';
	}

	/**
	 * Whether current selector includes flags.
	 *
	 * @return bool
	 */
	private static function flags_enabled() {
		return in_array( self::get_switcher_style(), array( 'flags', 'emoji_flags', 'flat_flags' ), true );
	}

	/**
	 * Whether current selector includes language names.
	 *
	 * @return bool
	 */
	private static function language_names_enabled() {
		return in_array( (string) get_option( 'wpat_min_txt_display', 'name' ), array( 'name', 'name_code' ), true );
	}

	/**
	 * Detect coarse builder families without duplicating Appsero theme metadata.
	 *
	 * @return array<int, string>
	 */
	private static function get_detected_builders() {
		$builders = array();

		if ( defined( 'ET_CORE_VERSION' ) || defined( 'ET_BUILDER_PLUGIN_VERSION' ) || function_exists( 'et_setup_theme' ) ) {
			$builders[] = 'divi';
		}

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$builders[] = 'elementor';
		}

		if ( class_exists( 'FLBuilder' ) ) {
			$builders[] = 'beaver_builder';
		}

		if ( defined( 'CT_VERSION' ) ) {
			$builders[] = 'oxygen';
		}

		if ( defined( 'BRICKS_VERSION' ) ) {
			$builders[] = 'bricks';
		}

		if ( defined( 'WPB_VC_VERSION' ) ) {
			$builders[] = 'wpbakery';
		}

		if ( function_exists( 'wp_is_block_theme' ) && wp_is_block_theme() ) {
			$builders[] = 'site_editor';
		}

		return array_values( array_unique( $builders ) );
	}

	/**
	 * Seconds from timestamp until now.
	 *
	 * @param string $timestamp Timestamp string.
	 * @param int    $default   Default seconds.
	 * @return int
	 */
	private static function seconds_since( $timestamp, $default ) {
		$start = self::timestamp_to_epoch( $timestamp );

		if ( 0 >= $start ) {
			return $default;
		}

		return max( 0, time() - $start );
	}

	/**
	 * Seconds between two timestamps.
	 *
	 * @param string $start Start timestamp.
	 * @param string $end   End timestamp.
	 * @return int
	 */
	private static function seconds_between( $start, $end ) {
		$start_epoch = self::timestamp_to_epoch( $start );
		$end_epoch   = self::timestamp_to_epoch( $end );

		if ( 0 >= $start_epoch || 0 >= $end_epoch ) {
			return -1;
		}

		return max( 0, $end_epoch - $start_epoch );
	}

	/**
	 * Convert timestamp string to epoch.
	 *
	 * @param string $timestamp Timestamp string.
	 * @return int
	 */
	private static function timestamp_to_epoch( $timestamp ) {
		$timestamp = self::sanitize_timestamp( $timestamp );
		if ( '' === $timestamp ) {
			return 0;
		}

		$epoch = strtotime( $timestamp );

		return false === $epoch ? 0 : (int) $epoch;
	}

	/**
	 * Sanitize a mode value.
	 *
	 * @param mixed  $mode    Raw mode.
	 * @param string $default Default mode.
	 * @return string
	 */
	private static function sanitize_mode( $mode, $default ) {
		$allowed = array( 'preview', 'live', 'unknown' );
		$mode    = sanitize_key( (string) $mode );

		return in_array( $mode, $allowed, true ) ? $mode : $default;
	}

	/**
	 * Sanitize a trigger value.
	 *
	 * @param mixed $trigger Raw trigger.
	 * @return string
	 */
	private static function sanitize_trigger( $trigger ) {
		$allowed = array( 'activation_default', 'dashboard_action', 'settings_save' );
		$trigger = sanitize_key( (string) $trigger );

		return in_array( $trigger, $allowed, true ) ? $trigger : 'settings_save';
	}

	/**
	 * Sanitize launch action.
	 *
	 * @param mixed $action Raw action.
	 * @return string
	 */
	private static function sanitize_action( $action ) {
		$action  = sanitize_key( (string) $action );
		$allowed = array( 'preview_site', 'go_live', 'choose_languages', 'adjust_style', 'set_placement' );

		return in_array( $action, $allowed, true ) ? $action : '';
	}

	/**
	 * Sanitize settings tab key.
	 *
	 * @param mixed $tab Raw tab.
	 * @return string
	 */
	private static function sanitize_settings_tab( $tab ) {
		$tab = sanitize_key( (string) $tab );
		$map = array(
			'language_settings'  => 'language_settings',
			'placement_settings' => 'placement',
			'visual_settings'    => 'styling',
			'advanced_settings'  => 'advanced',
			'placement'          => 'placement',
			'styling'            => 'styling',
			'advanced'           => 'advanced',
		);

		return $map[ $tab ] ?? '';
	}

	/**
	 * Sanitize floating position.
	 *
	 * @param mixed $position Raw position.
	 * @return string
	 */
	private static function sanitize_floating_position( $position ) {
		$position = sanitize_key( (string) $position );

		return in_array( $position, array( 'top_left', 'top_right', 'bottom_left', 'bottom_right' ), true ) ? $position : 'unknown';
	}

	/**
	 * Sanitize an event name.
	 *
	 * @param mixed $event_name Raw event name.
	 * @return string
	 */
	private static function sanitize_event_name( $event_name ) {
		$allowed    = array( 'wpat_preview_mode_enabled', 'wpat_go_live_enabled' );
		$event_name = sanitize_key( (string) $event_name );

		return in_array( $event_name, $allowed, true ) ? $event_name : '';
	}

	/**
	 * Sanitize the tracked option name.
	 *
	 * @param mixed $option_name Raw option name.
	 * @return string
	 */
	private static function sanitize_option_name( $option_name ) {
		return self::MODE_OPTION === sanitize_key( (string) $option_name ) ? self::MODE_OPTION : self::MODE_OPTION;
	}

	/**
	 * Sanitize a stored timestamp.
	 *
	 * @param mixed $timestamp Raw timestamp.
	 * @return string
	 */
	private static function sanitize_timestamp( $timestamp ) {
		return is_scalar( $timestamp ) ? trim( (string) $timestamp ) : '';
	}

	/**
	 * Sanitize stored plugin version.
	 *
	 * @param mixed $version Raw version.
	 * @return string
	 */
	private static function sanitize_version( $version ) {
		return is_scalar( $version ) ? sanitize_text_field( (string) $version ) : '';
	}
}
