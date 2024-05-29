<?php
// Add a menu item in the WordPress admin
add_action('admin_menu', 'add_api_key_management_page');

function add_api_key_management_page() {
    add_menu_page(
        'API Key Management',
        'API Keys',
        'manage_options',
        'api-key-management',
        'api_key_management_page',
        'dashicons-admin-network',
        100
    );
}

// Display the API key management page
function api_key_management_page() {
    ?>
    <div class="wrap">
        <h1>Site Config JSON API Key Management</h1>
        <?php if (isset($_GET['max_keys']) && $_GET['max_keys'] == 1): ?>
            <div class="notice notice-error is-dismissible">
                <p><?php _e('Cannot generate new API key. Maximum limit of 5 keys reached.', 'text-domain'); ?></p>
            </div>
        <?php endif; ?>
        
    <p>The Site Config JSON API plugin allows you to expose site configuration data via a JSON API. It was designed to prompt an AI (like ChatGPT) to help you and the Main Endpoint (accessible via the buttons below) includes a prompt explaining to the AI what is available. There are several endpoints available with different information. To use the API, you must provide an API key. Use the API key(s) below in a browser tab and paste the response data into your AI Chat.</p>

        
        <h2>Generate New API Key</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php
            settings_fields('api_key_options');
            do_settings_sections('api_key_management');
            wp_nonce_field('generate_api_key_action', 'generate_api_key_nonce');
            ?>
            <input type="hidden" name="action" value="generate_api_key">
            <?php submit_button('Generate New API Key'); ?>
        </form>
        <h2>Existing API Keys</h2>
        <table class="widefat">
            <style>
                .td-key {
                    display: flex;
                    flex-direction: column;
                    align-items: start;
                    gap: .5rem;
                }

                .api-key {
                    flex-grow: 1;
                    font-weight: bold;
                }

                .copy-api-key {
                    margin-right: 10px;
                }

                tbody td {
                    border-bottom: 1px solid #ddd;
                }
            </style>
            <thead>
                <tr>
                    <th>API Key</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $api_keys = get_option('api_keys', array());
                foreach ($api_keys as $key) {
                    $endpoint_url = esc_url(home_url('/wp-json/site-config/v1/data?api_key=' . $key));
                    echo '<tr>';
                    echo '<td class="td-key"><span class="api-key">' . esc_html($key) . '</span> <button class="button copy-api-key" data-key="' . esc_attr($key) . '">Copy</button> <a href="' . $endpoint_url . '" target="_blank" class="button">' . $endpoint_url . '</a></td>';
                    echo '<td><a href="' . esc_url(admin_url('admin-post.php?action=delete_api_key&key=' . urlencode($key))) . '">Delete</a></td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var copyButtons = document.querySelectorAll('.copy-api-key');
            copyButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    var key = this.getAttribute('data-key');
                    navigator.clipboard.writeText(key).then(function() {
                        alert('API Key copied to clipboard');
                    }, function(err) {
                        alert('Failed to copy API Key');
                    });
                });
            });
        });
    </script>
    <?php
}

// Register settings and add sections and fields
add_action('admin_init', 'register_api_key_settings');

function register_api_key_settings() {
    register_setting('api_key_options', 'api_keys', array(
        'type' => 'array',
        'sanitize_callback' => 'sanitize_api_keys'
    ));
}

function sanitize_api_keys($keys) {
    return array_map('sanitize_text_field', $keys);
}

// Generate a new API key
add_action('admin_post_generate_api_key', 'generate_api_key');

function generate_api_key() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    check_admin_referer('generate_api_key_action', 'generate_api_key_nonce');

    $api_keys = get_option('api_keys', array());

    // Check if the number of keys is less than 5
    if (count($api_keys) >= 5) {
        wp_redirect(add_query_arg('max_keys', '1', admin_url('admin.php?page=api-key-management')));
        exit;
    }

    $new_key = bin2hex(random_bytes(16)); // Generate a 32-character random key
    $api_keys[] = $new_key;
    update_option('api_keys', $api_keys);

    wp_redirect(admin_url('admin.php?page=api-key-management'));
    exit;
}

// Delete an API key
add_action('admin_post_delete_api_key', 'delete_api_key');

function delete_api_key() {
    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized user');
    }

    $key_to_delete = $_GET['key'];
    $api_keys = get_option('api_keys', array());
    $api_keys = array_filter($api_keys, function ($key) use ($key_to_delete) {
        return $key !== $key_to_delete;
    });
    update_option('api_keys', $api_keys);

    wp_redirect(admin_url('admin.php?page=api-key-management'));
    exit;
}
