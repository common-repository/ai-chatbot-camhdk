<?php
/*
Plugin Name: AI Question-Answer Chatbot from Camhdk
Plugin URI: https://camhdk.com
Description: This plugin adds an AI chatbot widget to the lower right corner of the website.
Version: 1.0
Author: camhdk.com
License: GPLv2 or later
Text Domain: ai-chatbot-camhdk
*/



function my_plugin_activation_check() {
    global $wp_version;

    // Minimum required WordPress version
    $min_wp_version = '6.6';

    // Check if the current WordPress version is less than the minimum required
    if ( version_compare( $wp_version, $min_wp_version, '<' ) ) {
        // Deactivate the plugin if the WordPress version is too old
        deactivate_plugins( plugin_basename( __FILE__ ) );
        
        // Show an admin error message and exit
        wp_die(
            'This plugin requires WordPress version ' . $min_wp_version . ' or higher. Your current version is ' . $wp_version . '. Please update WordPress and try again.',
            'Plugin Activation Error',
            array( 'back_link' => true )
        );
    }
}

// Register the activation hook
register_activation_hook( __FILE__, 'my_plugin_activation_check' );



// Register the activation hook
register_activation_hook( __FILE__, 'my_plugin_activation_check' );




// Step 1: Create a Settings Page for the Plugin
// =============================================
function camhdk_chatbot_menu() {
    add_options_page(
        'AI Camhdk Chatbot Settings', // Page title
        'AI Camhdk Chatbot',          // Menu title
        'manage_options',      // Capability
        'camhdk-chatbot',      // Menu slug
        'camhdk_chatbot_settings_page' // Callback function
    );
}
add_action('admin_menu', 'camhdk_chatbot_menu');

// Register settings
function camhdk_chatbot_register_settings() {
    register_setting('pluginPage', 'camhdk_settings');
}
add_action('admin_init', 'camhdk_chatbot_register_settings');

// Settings page HTML with default page logic
function camhdk_chatbot_settings_page() {
    $options = get_option('camhdk_settings', array());
    $selected_page_names = isset($options['camhdk_select_pages']) ? esc_attr($options['camhdk_select_pages']) : '';
    ?>
    <div class="wrap">
        <h1>AI Chatbot Settings</h1>
        <p>Enter the Page Names to display the chatbot (Enter page names/slug separated by commas):</p>
        <form method="post" action="options.php">
            <?php 
            settings_fields('pluginPage'); // Security field for saving options
            ?>
            <input type="text" id="camhdk_select_pages" name="camhdk_settings[camhdk_select_pages]" value="<?php echo $selected_page_names; ?>" />
            <?php submit_button('Save Changes'); ?>
        </form>
    </div>
    <?php
}

// Enqueue styles and scripts conditionally
function camhdk_chatbot_enqueue_scripts() {
    $options = get_option('camhdk_settings');
    $selected_page_names = isset($options['camhdk_select_pages']) ? $options['camhdk_select_pages'] : '';

    if (!$selected_page_names) {
        return;
    }

    // Convert the entered pages into an array and trim whitespace
    $pages = array_map('trim', explode(',', $selected_page_names));

    // Check if the current page matches any of the selected pages
    foreach ($pages as $page_name) {
        // Check if it's a regular page or the WooCommerce shop page
        if (is_page($page_name) || ($page_name === 'shop' && (is_shop() || is_woocommerce()))) {
            $version = '1.0';
            wp_register_style('camhdk-chatbot-styles', plugin_dir_url(__FILE__) . 'styles.css', array(), $version);
            wp_enqueue_style('camhdk-chatbot-styles');
            
            wp_register_script('camhdk_chatbot_script', plugin_dir_url(__FILE__) . 'script.js', array(), $version, true);
            wp_enqueue_script('camhdk_chatbot_script');
            break; // No need to check further, stop if a page matches
        }
    }
}
add_action('wp_enqueue_scripts', 'camhdk_chatbot_enqueue_scripts');

// Add chatbot HTML to the footer
function camhdk_chatbot_html() {
    $options = get_option('camhdk_settings');
    $selected_page_names = isset($options['camhdk_select_pages']) ? $options['camhdk_select_pages'] : '';

    if (!$selected_page_names) {
        return;
    }

    // Convert the entered pages into an array and trim whitespace
    $pages = array_map('trim', explode(',', $selected_page_names));

    // Check if the current page matches any of the selected pages
    foreach ($pages as $page_name) {
        // Check if it's a regular page or the WooCommerce shop page
        if (is_page($page_name) || ($page_name === 'shop' && (is_shop() || is_woocommerce()))) {
            ?>
            <div class="chatbot-container" id="draggable">
                <button type="button" class="chatbot-button" id="toggleBtn">Chat</button>
                <button type="button" class="close-btn" id="closeBtn" style="display: none;">Close</button>
                <div class="loading-gif" id="loadingGif">
                    <p>Loading....</p>
                </div>
                <iframe class="chatbot-iframe" id="chatbot-iframe"></iframe>
            </div>
            <?php
            break; // No need to check further, stop if a page matches
        }
    }
}
add_action('wp_footer', 'camhdk_chatbot_html');

// Redirect to the selected page if it is set as the homepage
add_action('template_redirect', 'camhdk_redirect_homepage');

function camhdk_redirect_homepage() {
    if (is_front_page()) {
        $options = get_option('camhdk_settings');
        if (isset($options['camhdk_select_pages'])) {
            $selected_page_names = $options['camhdk_select_pages'];
            // Convert the entered pages into an array
            $pages = array_map('trim', explode(',', $selected_page_names));

            // Find the first valid page title
            foreach ($pages as $page_name) {
                $page = get_page_by_title($page_name);
                if ($page) {
                    wp_redirect(get_permalink($page->ID));
                    exit;
                }
            }
        }
    }
}
