<?php

add_action('admin_menu', 'site_config_add_admin_menu');
add_action('admin_init', 'site_config_settings_init');

function site_config_add_admin_menu() { 
    add_options_page('Site Config JSON API', 'Site Config JSON API', 'manage_options', 'site_config_json_api', 'site_config_options_page');
}

function site_config_settings_init() { 
    register_setting('siteConfigAPIPage', 'site_config_settings');

    add_settings_section(
        'site_config_section', 
        __('API Key Management', 'site_config'), 
        'site_config_settings_section_callback', 
        'siteConfigAPIPage'
    );

    add_settings_field( 
        'api_keys', 
        __('API Keys', 'site_config'), 
        'api_keys_render', 
        'siteConfigAPIPage', 
        'site_config_section' 
    );
}

function api_keys_render() { 
    $options = get_option('site_config_settings');
    ?>
    <textarea cols="40" rows="5" name="site_config_settings[api_keys]"><?php echo $options['api_keys']; ?></textarea>
    <p class="description"><?php _e('Enter API keys separated by commas.', 'site_config'); ?></p>
    <?php
}

function site_config_settings_section_callback() { 
    echo __('Manage API keys that can access the Site Config JSON API.', 'site_config');
}

function site_config_options_page() { 
    ?>
    <form action="options.php" method="post">
        <h2>Site Config JSON API</h2>
        <?php
        settings_fields('siteConfigAPIPage');
        do_settings_sections('siteConfigAPIPage');
        submit_button();
        ?>
    </form>
    <?php
}
?>
