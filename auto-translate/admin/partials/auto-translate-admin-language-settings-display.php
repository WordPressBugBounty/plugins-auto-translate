<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://pampa.dev
 * @since      1.0.0
 *
 * @package    Auto_Translate
 * @subpackage Auto_Translate/admin/partials
 */
$wpat_data = $vars['tabs']['language_setting'];
$wpat_language_count = count( $wpat_data['supported_languages'] );
$wpat_language_columns = max( 1, (int) $wpat_data['columns'] );
$wpat_language_rows = max( 1, min( (int) $wpat_data['langs_per_column'], $wpat_language_count ) );
$wpat_language_rows_3 = max( 1, (int) ceil( $wpat_language_count / 3 ) );
$wpat_language_rows_2 = max( 1, (int) ceil( $wpat_language_count / 2 ) );
$wpat_language_rows_1 = max( 1, $wpat_language_count );
$wpat_language_flags = is_array( $wpat_data['wpat_language_flags'] ?? null ) ? $wpat_data['wpat_language_flags'] : array();
$wpat_languages_countries = is_array( $wpat_data['wpat_languages_countries'] ?? null ) ? $wpat_data['wpat_languages_countries'] : array();
$wpat_plugin_file = dirname( dirname( __DIR__ ) ) . '/auto-translate.php';
?>
<div class="wpat-settings-panel">
<div class="wpat-card wpat-card-language-base">
    <h3 class="wpat-card-title"><?php esc_html_e( 'Site language', 'auto-translate' ); ?></h3>
    <table class="form-table">
        <tr valign="top">
            <th scope="row">
                <?php esc_html_e('Base language', 'auto-translate'); ?><br/>
                <small><?php esc_html_e('What is your website\'s original language?','auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>"><select name="wpat_base_language">
                    <?php foreach ( $wpat_data['supported_languages'] as $wpat_code => $wpat_lang ) : ?>
                        <option value="<?php echo esc_attr( $wpat_code ); ?>" <?php selected( (string) $wpat_data['wpat_base_language'], (string) $wpat_code ); ?>><?php echo esc_html( $wpat_lang ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>

        <tr valign="top">
            <th scope="row">
                <span class="wpat-help-label-wrap">
                    <?php esc_html_e('Language label style', 'auto-translate'); ?>
                    <button
                        type="button"
                        class="wpat-help-trigger"
                        aria-label="<?php esc_attr_e( 'More info about language label styles', 'auto-translate' ); ?>"
                        aria-haspopup="dialog"
                        aria-controls="wpat-language-label-style-help-modal"
                    >
                        <span class="dashicons dashicons-editor-help" aria-hidden="true"></span>
                    </button>
                </span><br/>
                <small><?php esc_html_e('How language names appear in the selector dropdown.', 'auto-translate'); ?></small>
            </th>
            <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>">
                <div class="wpat-pill-group" role="radiogroup" aria-label="<?php esc_attr_e( 'Language names', 'auto-translate' ); ?>">
                    <label class="wpat-pill">
                        <input type="radio" name="wpat_language_name_display" value="native" <?php checked( (string) $wpat_data['wpat_language_name_display'], 'native' ); ?> />
                        <span><?php esc_html_e('Native names', 'auto-translate'); ?></span>
                    </label>
                    <label class="wpat-pill">
                        <input type="radio" name="wpat_language_name_display" value="english" <?php checked( (string) $wpat_data['wpat_language_name_display'], 'english' ); ?> />
                        <span><?php esc_html_e('English names', 'auto-translate'); ?></span>
                    </label>
                </div>
            </td>
        </tr>
    </table>
</div>

<div class="wpat-card wpat-card-languages">
    <h3 class="wpat-card-title">
        <?php esc_html_e('Supported languages', 'auto-translate'); ?>
        <span class="wpat-language-selected-count" id="wpat_language_selected_count">0 <?php esc_html_e( 'selected', 'auto-translate' ); ?></span>
    </h3>
    <div class="wpat-card-content">
        <div class="wpat-advanced-info suggestion" id="languages-limit" style="
        <?php if(
            is_array($wpat_data['wpat_supported_languages']) &&
            !in_array('all', $wpat_data['wpat_supported_languages']) &&
            count($wpat_data['wpat_supported_languages']) <= 10
        ): ?>
        display: none
        <?php endif; ?>
        ">
            <span class="dashicons dashicons-info"></span>
            <span><?php esc_html_e( 'For more than 10 languages we suggest enabling the \'Poupup with search\' feature under \'Styling\' -> \'Selector behavior\'', 'auto-translate' ); ?></span>
        </div>
        <div class="wpat-advanced-info wpat-advanced-idea">
            <span class="dashicons dashicons-lightbulb"></span>
            <span><?php esc_html_e( 'Tip: this language list is sortable. Drag and drop languages to control selector order.', 'auto-translate' ); ?></span>
        </div>

        <div class="wpat-language-actions">
            <input type="search" id="wpat_language_search" placeholder="<?php esc_attr_e( 'Search languages...', 'auto-translate' ); ?>" />
            <button type="button" class="button" id="wpat_language_select_all"><?php esc_html_e( 'Select all', 'auto-translate' ); ?></button>
            <button type="button" class="button" id="wpat_language_clear_all"><?php esc_html_e( 'Clear all', 'auto-translate' ); ?></button>
        </div>

        <input type="hidden" id="wpat_language_order" name="wpat_language_order" value="<?php echo esc_attr( (string) $wpat_data['wpat_language_order'] ); ?>" />
        <div class="wpat-language-list-shell">
            <label class="wpat-language-all">
                <input type="checkbox" name='wpat_supported_languages[]' value="all" <?php if (! is_array($wpat_data['wpat_supported_languages']) || in_array('all', $wpat_data['wpat_supported_languages'], true)):?> checked <?php endif; ?>>
                <?php esc_html_e('All', 'auto-translate'); ?>
            </label>
            <ul
                id="wpat_supported_languages_sortable"
                class="wpat-language-order-list"
                data-order="<?php echo esc_attr( (string) $wpat_data['wpat_language_order'] ); ?>"
                style="--wpat-language-columns: <?php echo esc_attr( $wpat_language_columns ); ?>; --wpat-language-rows: <?php echo esc_attr( $wpat_language_rows ); ?>; --wpat-language-rows-3: <?php echo esc_attr( $wpat_language_rows_3 ); ?>; --wpat-language-rows-2: <?php echo esc_attr( $wpat_language_rows_2 ); ?>; --wpat-language-rows-1: <?php echo esc_attr( $wpat_language_rows_1 ); ?>;"
            >
                <?php foreach ( $wpat_data['supported_languages'] as $wpat_code => $wpat_lang ) : ?>
                    <?php
                    $wpat_flag_options = array();
                    $wpat_selected_flag = '';

                    if ( isset( $wpat_languages_countries[ $wpat_code ]['countries'] ) && is_array( $wpat_languages_countries[ $wpat_code ]['countries'] ) ) {
                        $wpat_flag_options = $wpat_languages_countries[ $wpat_code ]['countries'];
                    }

                    if ( ! empty( $wpat_flag_options ) ) {
                        $wpat_selected_flag = isset( $wpat_language_flags[ $wpat_code ] )
                            ? (string) $wpat_language_flags[ $wpat_code ]
                            : (string) $wpat_flag_options[0]['country_code'];
                    }
                    ?>
                    <li data-lang-code="<?php echo esc_attr( $wpat_code ); ?>" draggable="true">
                        <label>
                            <span class="wpat-language-drag-handle dashicons dashicons-menu-alt3" aria-hidden="true"></span>
                            <input type="checkbox" name='wpat_supported_languages[]' value="<?php echo esc_attr( $wpat_code ); ?>" <?php if ( is_array( $wpat_data['wpat_supported_languages'] ) && in_array( $wpat_code, $wpat_data['wpat_supported_languages'], true ) ):?> checked <?php endif; ?>>
                            <span class="wpat-language-row-main">
                                <span class="wpat-language-name"><?php echo esc_html( $wpat_lang ); ?></span>
                                <?php if ( count( $wpat_flag_options ) > 1 ) : ?>
                                    <span class="wpat-language-flag-picker" data-wpat-flag-picker>
                                        <input
                                            type="hidden"
                                            id="wpat_language_flags_<?php echo esc_attr( $wpat_code ); ?>"
                                            name="wpat_language_flags[<?php echo esc_attr( $wpat_code ); ?>]"
                                            value="<?php echo esc_attr( $wpat_selected_flag ); ?>"
                                        />
                                        <button
                                            type="button"
                                            class="wpat-language-flag-trigger"
                                            aria-haspopup="listbox"
                                            aria-expanded="false"
                                            aria-label="<?php echo esc_attr( sprintf(
												/* translators: %s: language name. */
												__( '%s flag variant', 'auto-translate' ),
												$wpat_lang
											) ); ?>"
                                            data-wpat-flag-trigger
                                        >
                                            <span
                                                class="wpat-language-flag-swatch"
                                                aria-hidden="true"
                                                style="background-image:url('<?php echo esc_url( plugins_url( 'public/images/flags/svg/' . $wpat_selected_flag . '.svg', $wpat_plugin_file ) ); ?>');"
                                            ></span>
                                            <span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span>
                                        </button>
                                        <span class="wpat-language-flag-menu" role="listbox" tabindex="-1" aria-label="<?php echo esc_attr( sprintf(
											/* translators: %s: language name. */
											__( '%s flag variants', 'auto-translate' ),
											$wpat_lang
										) ); ?>" data-wpat-flag-menu hidden>
                                            <?php foreach ( $wpat_flag_options as $wpat_country ) : ?>
                                                <button
                                                    type="button"
                                                    class="wpat-language-flag-option<?php echo $wpat_selected_flag === $wpat_country['country_code'] ? ' is-active' : ''; ?>"
                                                    role="option"
                                                    aria-label="<?php echo esc_attr( $wpat_country['country_name'] ); ?>"
                                                    aria-selected="<?php echo $wpat_selected_flag === $wpat_country['country_code'] ? 'true' : 'false'; ?>"
                                                    data-flag-value="<?php echo esc_attr( $wpat_country['country_code'] ); ?>"
                                                    data-flag-url="<?php echo esc_url( plugins_url( 'public/images/flags/svg/' . $wpat_country['country_code'] . '.svg', $wpat_plugin_file ) ); ?>"
                                                    title="<?php echo esc_attr( $wpat_country['country_name'] ); ?>"
                                                >
                                                    <span
                                                        class="wpat-language-flag-swatch"
                                                        aria-hidden="true"
                                                        style="background-image:url('<?php echo esc_url( plugins_url( 'public/images/flags/svg/' . $wpat_country['country_code'] . '.svg', $wpat_plugin_file ) ); ?>');"
                                                    ></span>
                                                </button>
                                            <?php endforeach; ?>
                                        </span>
                                    </span>
                                <?php elseif ( 1 === count( $wpat_flag_options ) ) : ?>
                                    <span class="wpat-language-flag-static" aria-hidden="true">
                                        <span
                                            class="wpat-language-flag-swatch"
                                            style="background-image:url('<?php echo esc_url( plugins_url( 'public/images/flags/svg/' . $wpat_selected_flag . '.svg', $wpat_plugin_file ) ); ?>');"
                                        ></span>
                                    </span>
                                <?php endif; ?>
                            </span>
                        </label>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
</div>

<div id="wpat-language-label-style-help-modal" class="wpat-help-modal" aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="wpat-language-help-title">
    <div class="wpat-help-modal__backdrop" data-wpat-close-help-modal></div>
    <div class="wpat-help-modal__dialog">
        <button type="button" class="wpat-help-modal__close" aria-label="<?php esc_attr_e( 'Close', 'auto-translate' ); ?>" data-wpat-close-help-modal>
            <span class="dashicons dashicons-no-alt" aria-hidden="true"></span>
        </button>
        <h4 id="wpat-language-help-title"><?php esc_html_e( 'Language label styles', 'auto-translate' ); ?></h4>
        <p><?php esc_html_e( 'Native names show each language in its own writing. English names show all language names in English.', 'auto-translate' ); ?></p>
        <div class="wpat-help-modal__examples">
            <div class="wpat-help-modal__example">
                <strong><?php esc_html_e( 'Native names', 'auto-translate' ); ?></strong>
                <ul>
                    <li>العربية</li>
                    <li>Français</li>
                    <li>Русский</li>
                    <li>Deutsch</li>
                    <li>Português</li>
                </ul>
            </div>
            <div class="wpat-help-modal__example">
                <strong><?php esc_html_e( 'English names', 'auto-translate' ); ?></strong>
                <ul>
                    <li>Arabic</li>
                    <li>French</li>
                    <li>Russian</li>
                    <li>German</li>
                    <li>Portuguese</li>
                </ul>
            </div>
        </div>
    </div>
</div>
