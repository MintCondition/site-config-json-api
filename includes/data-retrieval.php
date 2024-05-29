<?php

class Site_Config_Data {

    public static function get_post_types() {
        return get_post_types(array('public' => true, '_builtin' => false), 'objects');
    }

    public static function get_acf_fields() {
        $acf_fields = array();
        if (function_exists('acf_get_field_groups')) {
            $field_groups = acf_get_field_groups();
            foreach ($field_groups as $group) {
                $fields = acf_get_fields($group['key']);
                $acf_fields[$group['title']] = $fields;
            }
        }
        return $acf_fields;
    }

    public static function get_plugins() {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $active_plugins = get_option('active_plugins');
        $plugins = array();
        foreach ($active_plugins as $plugin) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $plugins[] = array(
                'name' => $plugin_data['Name'],
                'version' => $plugin_data['Version']
            );
        }
        return $plugins;
    }

    public static function get_user_counts() {
        return count_users();
    }

    public static function get_theme_details() {
        $current_theme = wp_get_theme();
        return array(
            'name' => $current_theme->get('Name'),
            'version' => $current_theme->get('Version'),
            'parent_theme' => $current_theme->parent() ? $current_theme->parent()->get('Name') : ''
        );
    }
}
?>
