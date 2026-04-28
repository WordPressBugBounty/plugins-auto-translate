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
?>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<table class="form-table">
    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Base language', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('What is your website\'s original language?','auto-translate'); ?></small>
        </th>
        <td colspan="<?php echo esc_attr( $wpat_data['columns'] ); ?>"><select name="wpat_base_language">
                <?php
                /*
                    * Auto detect is not supported yet. The problem is that if you select "Auto detect" and do not
                    * choose your base language in the "Supported languages" then the user will not be able to
                    * go back to the original language
                    */
                ?>
                <?php foreach ( $wpat_data['supported_languages'] as $wpat_code => $wpat_lang ) : ?>
                    <option value="<?php echo esc_attr( $wpat_code ); ?>" <?php selected( (string) $wpat_data['wpat_base_language'], (string) $wpat_code ); ?>><?php echo esc_html( $wpat_lang ); ?></option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>

    <tr valign="top">
        <th scope="row">
            <?php esc_html_e('Supported languages', 'auto-translate'); ?><br/>
            <small><?php esc_html_e('Select what languages you want your website to be translated into.','auto-translate'); ?></small>
            <div class="suggestion" id="languages-limit" style="
            <?php if( 
                is_array($wpat_data['wpat_supported_languages']) &&
                !in_array('all', $wpat_data['wpat_supported_languages']) &&
                count($wpat_data['wpat_supported_languages']) <= 10
             ): ?>
            display: none
            <?php endif; ?>
            ">
                <p><?php esc_html_e( 'We don\'t recommend selecting more than 10 languages, it might affect the UX negatively.', 'auto-translate' ); ?></p>
            </div>
    </div>
        </th>
        <td>
            <input type="checkbox" name='wpat_supported_languages[]' value="all" <?php if (! is_array($wpat_data['wpat_supported_languages']) || in_array('all', $wpat_data['wpat_supported_languages'], true)):?> checked <?php endif; ?>><?php esc_html_e('All', 'auto-translate'); ?><br/>
            <?php foreach ( $wpat_data['supported_languages'] as $wpat_code => $wpat_lang ) : ?>
                <?php if($wpat_data['count'] % $wpat_data['langs_per_column'] == 0): ?></td><td><?php endif ?>
                <input type="checkbox" name='wpat_supported_languages[]' value="<?php echo esc_attr( $wpat_code ); ?>" <?php if ( is_array( $wpat_data['wpat_supported_languages'] ) && in_array( $wpat_code, $wpat_data['wpat_supported_languages'], true ) ):?> checked <?php endif; ?>><?php echo esc_html( $wpat_lang ); ?><br/>
                <?php $wpat_data['count']++ ?>
            <?php endforeach; ?>
        </td>
    </tr>
</table>
