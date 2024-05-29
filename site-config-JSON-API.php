<?php
/*
Plugin Name: Site Config JSON API
Description: A plugin to expose site configuration via JSON
Version: 0.5.0
Author: Brian Wood (Stratifi Creative)
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

define('SITE_CONFIG_API_NAMESPACE', 'site-config/v1');
define('SITE_CONFIG_API_ROUTE', '/data');
define('SITE_CONFIG_API_TEST_ROUTE', '/test');

require_once plugin_dir_path(__FILE__) . 'includes/update-checker.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-key-management.php';
require_once plugin_dir_path(__FILE__) . 'includes/api-endpoints.php';
require_once plugin_dir_path(__FILE__) . 'includes/validation.php';
require_once plugin_dir_path(__FILE__) . 'includes/data-retrieval.php';
require_once plugin_dir_path(__FILE__) . 'includes/helpers.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin-settings.php';

new GitHub_Updater(__FILE__, 'MintCondition', 'site-config-json-api');

add_action('rest_api_init', ['Site_Config_API', 'register_routes']);
