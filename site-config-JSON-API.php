<?php
/*
Plugin Name: Site Config JSON API
Description: A plugin to expose site configuration via JSON
Version: 0.1.0
Author: Brian Wood (Stratifi Creative)
*/

// Your plugin code starts here

require_once plugin_dir_path(__FILE__) . 'includes/update-checker.php';

new GitHub_Updater(__FILE__, 'MintCondition', 'site-config-json-api');

// Add a custom API endpoint to expose the site configuration
add_action('rest_api_init', function () {
    register_rest_route('site-config/v1', '/data', array(
        'methods' => 'GET',
        'callback' => 'get_site_configuration',
        'permission_callback' => '__return_true'
    ));
    register_rest_route('site-config/v1', '/test', array(
        'methods' => 'GET',
        'callback' => 'test_endpoint',
        'permission_callback' => '__return_true'
    ));
});

function get_site_configuration() {
    $post_types = get_post_types(array('public' => true, '_builtin' => false), 'objects');
    $acf_fields = array();
    if (function_exists('acf_get_field_groups')) {
        $field_groups = acf_get_field_groups();
        foreach ($field_groups as $group) {
            $fields = acf_get_fields($group['key']);
            $acf_fields[$group['title']] = $fields;
        }
    }
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
    $user_counts = count_users();
    $current_theme = wp_get_theme();
    $theme_details = array(
        'name' => $current_theme->get('Name'),
        'version' => $current_theme->get('Version'),
        'parent_theme' => $current_theme->parent() ? $current_theme->parent()->get('Name') : ''
    );
 
    $data = array(
        'post_types' => $post_types,
        'acf_fields' => $acf_fields,
        'plugins' => $plugins,
        'user_counts' => $user_counts,
        'theme_details' => $theme_details
    );

    return new WP_REST_Response($data, 200);
}

function test_endpoint() {
    return new WP_REST_Response('Test endpoint working', 200);
}

function debug_user_capabilities() {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        $user = wp_get_current_user();
        error_log('User roles during init: ' . implode(', ', $user->roles));
        error_log('User capabilities during init: ' . print_r($user->allcaps, true));
    }
}
add_action('init', 'debug_user_capabilities');
