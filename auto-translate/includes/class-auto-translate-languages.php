<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Auto_Translate_Languages {

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

    public static function get_languages_data($lang_code_list) {
        $languages_data = array();
        foreach ($lang_code_list as $lang_code) {
            $lang_code = Auto_Translate_Config::normalize_lang_code( $lang_code );
            $languages_data[$lang_code] = array(
                'lang_name' => self::get_lang_name($lang_code),
                'lang_code' => $lang_code,
                'country_code' => self::get_country_code($lang_code)
            );
        }
        return $languages_data;
    }

}
