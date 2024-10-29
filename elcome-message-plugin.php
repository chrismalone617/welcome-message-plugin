<?php
/*
Plugin Name: Welcome Message Plugin
Description: A simple plugin to add a welcome message with customizable text and color.
Version: 1.0
Author: Your Name
*/

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Welcome_Message_Plugin {
    
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
        add_shortcode('welcome_message', [$this, 'display_welcome_message']);
    }

    // Add settings page
    public function add_settings_page() {
        add_options_page(
            'Welcome Message Settings', 
            'Welcome Message', 
            'manage_options', 
            'welcome-message-plugin', 
            [$this, 'create_settings_page']
        );
    }

    // Create the settings page content
    public function create_settings_page() {
        ?>
        <div class="wrap">
            <h1>Welcome Message Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('welcome_message_group');
                do_settings_sections('welcome-message-plugin');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Register settings
    public function register_settings() {
        register_setting('welcome_message_group', 'welcome_message_text', 'sanitize_text_field');
        register_setting('welcome_message_group', 'welcome_message_color', 'sanitize_hex_color');

        add_settings_section('welcome_message_section', '', null, 'welcome-message-plugin');

        add_settings_field(
            'welcome_message_text',
            'Welcome Message',
            [$this, 'welcome_message_text_callback'],
            'welcome-message-plugin',
            'welcome_message_section'
        );

        add_settings_field(
            'welcome_message_color',
            'Text Color',
            [$this, 'welcome_message_color_callback'],
            'welcome-message-plugin',
            'welcome_message_section'
        );
    }

    // Callback function for the text input field
    public function welcome_message_text_callback() {
        $message = get_option('welcome_message_text', '');
        echo '<input type="text" name="welcome_message_text" value="' . esc_attr($message) . '" class="regular-text" />';
    }

    // Callback function for the color picker
    public function welcome_message_color_callback() {
        $color = get_option('welcome_message_color', '#000000');
        echo '<input type="text" name="welcome_message_color" value="' . esc_attr($color) . '" class="color-picker" data-default-color="#000000" />';
        // Enqueue color picker
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        ?>
        <script>
        jQuery(document).ready(function($){
            $('.color-picker').wpColorPicker();
        });
        </script>
        <?php
    }

    // Shortcode to display the welcome message
    public function display_welcome_message() {
        $message = get_option('welcome_message_text', 'Welcome to my site!');
        $color = get_option('welcome_message_color', '#000000');
        return '<p style="color:' . esc_attr($color) . ';">' . esc_html($message) . '</p>';
    }
}

// Initialize the plugin
new Welcome_Message_Plugin();