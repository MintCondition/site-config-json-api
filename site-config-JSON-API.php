<?php
/*
Plugin Name: Site Config JSON API
Description: A plugin to expose site configuration via JSON
Version: 0.0.1
Author: Brian Wood (Stratifi Creative)
*/

// Your plugin code starts here

require_once plugin_dir_path(__FILE__) . 'includes/update-checker.php';

new GitHub_Updater(__FILE__, 'MintCondition', 'site-config-json-api');
