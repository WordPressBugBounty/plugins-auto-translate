<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Auto_Translate_Languages {
    private static function get_native_names_map() {
        return array(
            'ar' => 'العربية',
            'de' => 'Deutsch',
            'en' => 'English',
            'es' => 'Español',
            'fr' => 'Français',
            'hi' => 'हिन्दी',
            'id' => 'Bahasa Indonesia',
            'it' => 'Italiano',
            'ja' => '日本語',
            'ko' => '한국어',
            'nl' => 'Nederlands',
            'pl' => 'Polski',
            'pt' => 'Português',
            'ru' => 'Русский',
            'tr' => 'Türkçe',
            'uk' => 'Українська',
            'vi' => 'Tiếng Việt',
            'zh-CN' => '简体中文',
            'zh-TW' => '繁體中文',
        );
    }

    public static function get_lang_country($lang_code, $i = 0) {
        $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
        $wpat_languages_countries = Auto_Translate_Config::get_languages_countries();

        if ( isset( $wpat_languages_countries[ $lang_code ]['countries'][ $i ] ) ) {
            return $wpat_languages_countries[ $lang_code ]['countries'][ $i ];
        }

        $fallback = isset( $wpat_languages_countries['en']['countries'][0] )
            ? $wpat_languages_countries['en']['countries'][0]
            : array( 'country_code' => 'xx', 'country_name' => 'Unknown' );

        return $fallback;
    }

    public static function get_lang_name($lang_code) {
        $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
        $wpat_languages_countries = Auto_Translate_Config::get_languages_countries();

        if ( isset( $wpat_languages_countries[ $lang_code ]['lang_name'] ) ) {
            return $wpat_languages_countries[ $lang_code ]['lang_name'];
        }

        return isset( $wpat_languages_countries['en']['lang_name'] )
            ? $wpat_languages_countries['en']['lang_name']
            : 'English';
    }

    public static function get_country_code($lang_code, $i = 0) {
        $country = self::get_lang_country($lang_code, $i);
        return $country['country_code'];
    }

    public static function get_country_code_by_selection( $lang_code, $selected_country_code = '' ) {
        $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
        $selected_country_code = sanitize_text_field( (string) $selected_country_code );
        $wpat_languages_countries = Auto_Translate_Config::get_languages_countries();

        if ( isset( $wpat_languages_countries[ $lang_code ]['countries'] ) && is_array( $wpat_languages_countries[ $lang_code ]['countries'] ) ) {
            foreach ( $wpat_languages_countries[ $lang_code ]['countries'] as $country ) {
                if ( isset( $country['country_code'] ) && $selected_country_code === sanitize_text_field( (string) $country['country_code'] ) ) {
                    return $selected_country_code;
                }
            }
        }

        return self::get_country_code( $lang_code );
    }

    public static function get_native_name( $lang_code ) {
        $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
        $map = self::get_native_names_map();

        if ( isset( $map[ $lang_code ] ) ) {
            return $map[ $lang_code ];
        }

        return self::get_lang_name( $lang_code );
    }

    public static function get_languages_data( $lang_code_list, $label_mode = 'english', $selected_flags = array() ) {
        $languages_data = array();
        foreach ( $lang_code_list as $lang_code ) {
            $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
            $english_name = self::get_lang_name( $lang_code );
            $native_name = self::get_native_name( $lang_code );
            $languages_data[$lang_code] = array(
                'lang_name' => ( 'native' === $label_mode ) ? $native_name : $english_name,
                'lang_name_english' => $english_name,
                'lang_name_native' => $native_name,
                'lang_code' => $lang_code,
                'country_code' => self::get_country_code_by_selection(
                    $lang_code,
                    is_array( $selected_flags ) && isset( $selected_flags[ $lang_code ] ) ? $selected_flags[ $lang_code ] : ''
                ),
            );
        }
        return $languages_data;
    }

}
