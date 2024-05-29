<?php

class Site_Config_Validation {

    public static function validate_api_key($api_key) {
        $valid_api_keys = get_option('api_keys', array());
        return in_array($api_key, $valid_api_keys, true);
    }
}
?>
