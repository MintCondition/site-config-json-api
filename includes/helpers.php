<?php

class Site_Config_Utilities {

    public static function sanitize_api_key($api_key) {
        return sanitize_text_field($api_key);
    }

    public static function sanitize_request($request) {
        return array_map('sanitize_text_field', $request->get_params());
    }
}
?>
