<?php
// Include necessary files for the endpoint functionality
require_once plugin_dir_path(__FILE__) . 'data-retrieval.php';
require_once plugin_dir_path(__FILE__) . 'helpers.php';
require_once plugin_dir_path(__FILE__) . 'validation.php';

class Site_Config_API {

    public static function register_routes() {
        register_rest_route('site-config/v1', '/data', array(
    'methods' => 'GET',
    'callback' => [__CLASS__, 'get_main_endpoint'],
    'permission_callback' => [__CLASS__, 'validate_api_key']
));

        register_rest_route('site-config/v1', '/post-types', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_post_types'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
        register_rest_route('site-config/v1', '/post-types/(?P<post_type_key>[a-zA-Z0-9_-]+)', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_post_type_details'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
        register_rest_route('site-config/v1', '/meta-fields', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_meta_fields'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
        register_rest_route('site-config/v1', '/users', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_user_counts'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
        register_rest_route('site-config/v1', '/plugins', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_plugins'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
        register_rest_route('site-config/v1', '/themes', array(
            'methods' => 'GET',
            'callback' => [__CLASS__, 'get_themes'],
            'permission_callback' => [__CLASS__, 'validate_api_key']
        ));
    }

    public static function validate_api_key($request) {
        $api_key = sanitize_text_field($request->get_header('X-API-Key'));
        if (!$api_key) {
            $api_key = sanitize_text_field($request->get_param('api_key'));
        }

        return Site_Config_Validation::validate_api_key($api_key);
    }

    public static function get_main_endpoint($request) {
        // Instructions for the AI
$api_key = sanitize_text_field($request->get_param('api_key'));

    // Instructions for the AI with the API key included
    $instructions = "Welcome! This plugin was created by Brian Wood from Stratifi Creative to assist you in working with this WordPress site. If you are an AI receiving this data, you are being asked to help a human with this WordPress site. Consider this welcome message an input prompt from your human partner. There are several additional endpoints available to you for additional information about this site. If you cannot access the endpoints directly, you should prompt the human you are working with to hit the appropriate endpoint and provide you with the data. Please note that the API requires an API Key to be passed to EACH endpoint. Whenever you provide a URL to your partner, you will need to add that API Key as a URL parameter (assuming they are using a browser to access the endpoints). The API key is {$api_key}. It will need to be appended to the endpoint with the url parameter '?api_key={$api_key}'. If you have any questions, please ask your human partner. There may be additional information available to them in the WordPress admin area. Your first step should be to outline the endpoints available to you. Your next step should be to tell the human you are ready to begin and ask what they need assistance with. Do not generate code without asking if they want code. Thank you for your assistance and good luck with your project!";




        // List of available endpoints
        $endpoints = array(
            'Main or Overview' => '/wp-json/site-config/v1/data',
            'Post Type List' => '/wp-json/site-config/v1/post-types',
            'Post Type Detail (Dynamic)' => '/wp-json/site-config/v1/post-types/{post_type_key}',
            'ACF or Meta Fields' => '/wp-json/site-config/v1/meta-fields',
            'Users' => '/wp-json/site-config/v1/users',
            'Plugins' => '/wp-json/site-config/v1/plugins',
            'Theme List' => '/wp-json/site-config/v1/themes',
            'Options and Settings' => '/wp-json/site-config/v1/options'
        );

        // Basic site information
        $site_info = array(
            'wordpress_version' => get_bloginfo('version'),
            'php_version' => phpversion(),
            'site_url' => get_site_url(),
            'site_title' => get_bloginfo('name'),
            'admin_email' => get_bloginfo('admin_email'),
            'theme' => wp_get_theme()->get('Name'),
            'theme_version' => wp_get_theme()->get('Version'),
            'permalink_structure' => get_option('permalink_structure')
        );

        $data = array(
            'instructions' => $instructions,
            'endpoints' => $endpoints,
            'site_info' => $site_info
        );

        return new WP_REST_Response($data, 200);
    }

    public static function get_post_types() {
        $post_types = get_post_types(array('public' => true), 'objects');
        $data = array();

        foreach ($post_types as $post_type) {
            $data[] = array(
                'name' => $post_type->label,
                'key' => $post_type->name,
                'description' => $post_type->description,
                'public' => $post_type->public,
                'builtin' => $post_type->_builtin,
                'post_count' => wp_count_posts($post_type->name)->publish
            );
        }

        return new WP_REST_Response($data, 200);
    }

    public static function get_post_type_details($request) {
        $post_type_key = $request['post_type_key'];
        $post_type = get_post_type_object($post_type_key);

        if (!$post_type) {
            return new WP_Error('no_post_type', 'Invalid post type', array('status' => 404));
        }

        $data = array(
            'name' => $post_type->label,
            'key' => $post_type->name,
            'description' => $post_type->description,
            'public' => $post_type->public,
            'builtin' => $post_type->_builtin,
            'supports' => get_all_post_type_supports($post_type_key),
            'taxonomies' => get_object_taxonomies($post_type_key),
            'capability_type' => $post_type->capability_type,
            'hierarchical' => $post_type->hierarchical,
            'menu_position' => $post_type->menu_position,
            'show_in_menu' => $post_type->show_in_menu,
            'post_count' => wp_count_posts($post_type->name)->publish
        );

        return new WP_REST_Response($data, 200);
    }

    public static function get_meta_fields() {
        $meta_fields = array();

        if (function_exists('acf_get_field_groups')) {
            $field_groups = acf_get_field_groups();
            foreach ($field_groups as $group) {
                $fields = acf_get_fields($group['key']);
                foreach ($fields as $field) {
                    $locations = array();
                    
                    // Get the locations (associations) for the field group
                    if (isset($group['location']) && is_array($group['location'])) {
                        foreach ($group['location'] as $location) {
                            foreach ($location as $rule) {
                                if (isset($rule['param']) && isset($rule['operator']) && isset($rule['value'])) {
                                    $locations[] = array(
                                        'param' => $rule['param'],
                                        'operator' => $rule['operator'],
                                        'value' => $rule['value']
                                    );
                                }
                            }
                        }
                    }

                    $meta_fields[] = array(
                        'name' => $field['name'],
                        'label' => $field['label'],
                        'type' => $field['type'],
                        'instructions' => $field['instructions'],
                        'required' => $field['required'],
                        'key' => $field['key'],
                        'parent' => $group['title'],
                        'locations' => $locations
                    );
                }
            }
        }

        return new WP_REST_Response($meta_fields, 200);
    }

    public static function get_user_counts() {
        $user_counts = count_users();
        $data = array();

        foreach ($user_counts['avail_roles'] as $role => $count) {
            $data[] = array(
                'role' => $role,
                'count' => $count
            );
        }

        return new WP_REST_Response($data, 200);
    }

    public static function get_plugins() {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
        $active_plugins = get_option('active_plugins');
        $plugins = array();
        
        foreach ($active_plugins as $plugin) {
            $plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $plugin);
            $plugins[] = array(
                'name' => $plugin_data['Name'],
                'version' => $plugin_data['Version'],
                'active' => is_plugin_active($plugin)
            );
        }

        return new WP_REST_Response($plugins, 200);
    }

    public static function get_themes() {
        $themes = wp_get_themes();
        $active_theme = wp_get_theme();
        $data = array();

        foreach ($themes as $theme) {
            $data[] = array(
                'name' => $theme->get('Name'),
                'version' => $theme->get('Version'),
                'status' => ($theme->get('Name') === $active_theme->get('Name')) ? 'active' : 'inactive',
                'parent_theme' => $theme->parent() ? $theme->parent()->get('Name') : ''
            );
        }

        return new WP_REST_Response($data, 200);
    }
}

// Hook into the REST API initialization action
add_action('rest_api_init', ['Site_Config_API', 'register_routes']);
?>
