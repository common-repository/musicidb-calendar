<?php
/*
 *  Plugin Name: MusicIDB Events Calendar
 *  Description: A Modern Calendar and Event List for Artists, Venues, Bars, Restaurants and Companies with a focus on live music and entertainment features.
 *  Author: MusicIDB.com
 *  Version: 2.5.12
 *  Author URI: https://musicidb.com/
 *
 *	------------------------------------------------------------------------
 *	Copyright 2024 Megabase, Inc.
 *
 *	This plugin is free software; you can redistribute it and/or modify
 *	it under the terms of the GNU General Public License as published by
 *	the Free Software Foundation; either version 3 of the License, or
 *	(at your option) any later version.
 *
 *	This plugin is distributed in the hope that it will be useful,
 *	but WITHOUT ANY WARRANTY; without even the implied warranty of
 *	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *	GNU General Public License for more details.
 *
 *	You should have received a copy of the GNU General Public License
 *	along with this plugin.  If not, see http://www.gnu.org/licenses.
*/

/** Define path identifiers **/
if (!defined('MUSICIDB_PLUGIN'))
    define('MUSICIDB_PLUGIN', __FILE__);

if (!defined('MUSICIDB_PLUGIN_DIR'))
    define('MUSICIDB_PLUGIN_DIR', untrailingslashit(dirname(MUSICIDB_PLUGIN)));

if( !defined('MUSICIDB_API_DIR') )
    define('MUSICIDB_API_DIR', untrailingslashit(MUSICIDB_PLUGIN_DIR . '/includes/musicidb-api-client-v2'));

require_once(MUSICIDB_PLUGIN_DIR . '/bootstrap.php');

use musicidb\Client\MusicIDB_API;
use musicidb\Client\Api_Exception;

if (!class_exists('MusicIDBIntegration')):

class MusicIDBIntegration {
    private static $instance;

    private $api_key;
    private $connected;

    /** The Constructor **/
    function __construct() {

        if( null !== self::$instance ) {
            return self::$instance;
        } else {
            self::$instance = $this;
        }

        register_deactivation_hook( MUSICIDB_PLUGIN, array( $this, 'musicidb_deactivation' ) );

        //Register the plugin options
        register_setting(
            'musicidb-integration',
            'musicidb_options',
            array($this, 'musicidb_validate_input')
        );

        $settings = (get_option('musicidb_options')) ? get_option('musicidb_options') : array();

        //Registers the admin menu
        add_action('admin_menu', array($this, 'admin_menu'));

        //Fires on WP init
        add_action('init', array($this, 'musicidb_init'));

        //Fires on WP admin_init
        add_action('admin_init', array($this, 'musicidb_admin_init'));

        // Fires after options are updated
        add_action( 'update_option_musicidb_options', array( $this, 'musicidb_update_option_musicidb_options' ) );

        //Enqueue plugin admin scripts
        add_action('admin_enqueue_scripts', array($this, 'musicidb_enqueue_admin_scripts'));
        add_action('wp_enqueue_scripts', array($this, 'musicidb_enqueue_scripts'));

        //Add AJAX actions
        add_action('wp_ajax_load_events_list', array($this, 'load_events_list'));        //Registers AJAX action for logged-in users
        add_action('wp_ajax_nopriv_load_events_list', array($this, 'load_events_list'));    //Registers AJAX action for non-logged-in users

        add_action('wp_ajax_load_event_details', array($this, 'load_event_details')); 
        add_action('wp_ajax_nopriv_load_event_details', array($this, 'load_event_details')); 

        add_action('wp_ajax_load_shortcode_options', array($this, 'musicidb_load_shortcode_options'));

        add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'musicidb_action_links') );

        if (array_key_exists('musicidb_api_key', $settings) && $settings['musicidb_api_key']) {
            $this->api_key = ($settings['musicidb_api_key']) ? $settings['musicidb_api_key'] : '';
            $this->connected = (array_key_exists('musicidb_api_connected', $settings) && $settings['musicidb_api_connected']) ? $settings['musicidb_api_connected'] : 0;
        }

        if( array_key_exists( 'musicidb_no_event_msg', $settings ) )
            $settings['musicidb_no_event_msg'] = ($settings['musicidb_no_event_msg']) ? $settings['musicidb_no_event_msg'] : '';

        if( !isset( $settings['musicidb_api_key'] ) ) {
            $settings['musicidb_api_key'] = '';
        }

        if( !isset( $settings['musicidb_api_connected'] ) ) {
            $settings['musicidb_api_connected'] = 0;
        }

        if( empty( $settings['musicidb_default_type_and_id'] ) ) {
            
            // Handle existing installs by 
            // falling back to old venue_id
            $default_id = !empty( $settings['musicidb_venue_id'] ) ? 'venue:' . $settings['musicidb_venue_id'] : '';
            $settings['musicidb_default_type_and_id'] = $default_id;

        }

        if( !isset( $settings['musicidb_additional_venues'] ) ) {
            $settings['musicidb_additional_venues'] = '';
        }

        if( !isset( $settings['musicidb_additional_artists'] ) ) {
            $settings['musicidb_additional_artists'] = '';
        }

        if( !isset( $settings['musicidb_no_event_msg'] ) ) {
            $settings['musicidb_no_event_msg'] = 'No events listed at the moment&hellip;';
        }

        update_option('musicidb_options', $settings);
    }

    public function musicidb_deactivation() {

        // Clean up after ourselves
        remove_action('admin_menu', array($this, 'admin_menu'));
        remove_action('init', array($this, 'musicidb_init'));
        remove_action('admin_init', array($this, 'musicidb_admin_init'));
        remove_action( 'update_option_musicidb_options', array( $this, 'musicidb_update_option_musicidb_options' ) );
        remove_action('admin_enqueue_scripts', array($this, 'musicidb_enqueue_admin_scripts'));
        remove_action('wp_enqueue_scripts', array($this, 'musicidb_enqueue_scripts'));
        remove_action('wp_ajax_load_events_list', array($this, 'load_events_list'));
        remove_action('wp_ajax_nopriv_load_events_list', array($this, 'load_events_list'));
        remove_action('wp_ajax_load_event_details', array($this, 'load_event_details')); 
        remove_action('wp_ajax_nopriv_load_event_details', array($this, 'load_event_details')); 
        remove_action( 'media_buttons', array( $this, 'musicidb_add_media_button'), 20 );
        remove_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array($this, 'musicidb_action_links') );

        $this->musicidb_flush_all_transients();

    }

    public function musicidb_update_option_musicidb_options() {

        // We want to flush cache when settings
        // are updated
        $this->musicidb_flush_all_transients();

    }

    private function musicidb_flush_all_transients() {

        // Delete all of the transients that we create
        delete_transient( 'musicidb_integration_entity_responses' );

    }

    public function musicidb_init() {

    }

    /** Fires on admin_init **/
    public function musicidb_admin_init() {

        $this->musicidb_register_settings();
        $this->musicidb_register_custom_buttons();

    }

    function musicidb_register_settings() {
        
        //Register a new section on the settings page
        add_settings_section(
            'musicidb_settings_section',
            'General Settings',
            array($this, 'musicidb_settings_section'),
            'musicidb-integration'
        );

        //Register the API Key field
        add_settings_field(
            'musicidb_api_key',
            'API Key',
            array($this, 'musicidb_api_key_field'),
            'musicidb-integration',
            'musicidb_settings_section',
            [
                'label_for' => 'musicidb_api_key',
                'class' => 'musicidb_row'
            ]
        );

        add_settings_field(
            'musicidb_default_type_and_id',
            'Default Entity',
            array($this, 'musicidb_default_field'),
            'musicidb-integration',
            'musicidb_settings_section',
            [
                'label_for' => 'musicidb_default_type_and_id',
                'class' => 'musicidb_row'
            ]
        );

        add_settings_field(
            'musicidb_additional_venues',
            'Additional Venue IDs (comma separated - retrieve by searching MusicIDB.com and hovering on the URL to the search result where ID is visible)',
            array($this, 'musicidb_additional_venues_field'),
            'musicidb-integration',
            'musicidb_settings_section',
            [
                'label_for' => 'musicidb_additional_venues',
                'class' => 'musicidb_row'
            ]
        );

        add_settings_field(
            'musicidb_additional_artists',
            'Additional Artist IDs (comma separated)',
            array($this, 'musicidb_additional_artists_field'),
            'musicidb-integration',
            'musicidb_settings_section',
            [
                'label_for' => 'musicidb_additional_artists',
                'class' => 'musicidb_row'
            ]
        );

        //Register no events message
        add_settings_field(
            'musicidb_no_event_msg',
            'Message shown when no events found',
            array($this, 'musicidb_no_event_msg_field'),
            'musicidb-integration',
            'musicidb_settings_section',
            [
                'label_for' => 'musicidb_no_event_msg',
                'class' => 'musicidb_row'
            ]
        );

    }

    function musicidb_register_custom_buttons() {

        // Priority 20 here is to ensure that
        // our media button appears after the 
        // default one 
        add_action( 'media_buttons', array( $this, 'musicidb_show_shortcode_button'), 20 );

    }

    function musicidb_show_shortcode_button() {

        echo '
            <a href="#" id="musiscidb-insert-shortcode" class="button">
                <i class="custicon-MusicIDBicon"></i>                
                Add Events
            </a>
        ';            
    }

    /** Enqueues plugin admin scripts **/
    public function musicidb_enqueue_admin_scripts() {
        wp_enqueue_style(
            'musicidb-admin-styles', 
            plugins_url('/css/admin-styles.css', MUSICIDB_PLUGIN), 
            array(), 
            null
        );

        wp_enqueue_script(
            'musicidb-admin-scripts', 
            plugins_url('/js/admin-scripts.min.js', MUSICIDB_PLUGIN), 
            array('jquery'),
            null
        );

        $ajax_shortcode_options_url = add_query_arg( array(
            'action' => 'load_shortcode_options',
            '_nonce' => wp_create_nonce( 'load-shortcode-modal-nonce' )
        ), admin_url( 'admin-ajax.php' ) );

        wp_localize_script( 'musicidb-admin-scripts', 'ajax_shortcode_options_url', $ajax_shortcode_options_url );

    }

    /** Enqueues plugin front-end scripts **/
    public function musicidb_enqueue_scripts() {
        //Styles
        wp_enqueue_style('musicidb-whhg', plugins_url('/css/whhg.css', MUSICIDB_PLUGIN));
        wp_enqueue_style('musicidb-whhg-fui', plugins_url('/css/whhg-flat-ui.css', MUSICIDB_PLUGIN));
        wp_enqueue_style('musicidb-slick-theme', plugins_url('/css/slick-theme.css', MUSICIDB_PLUGIN));
        
        wp_enqueue_style(
            'musicidb-styles', 
            plugins_url('/css/styles.css', MUSICIDB_PLUGIN),
            array(), 
            filemtime( plugin_dir_path(__FILE__) . '/css/styles.css' )
        );

        //Scripts
        wp_enqueue_script(
            'musicidb-slick', 
            plugins_url('/js/slick.min.js', MUSICIDB_PLUGIN), 
            array('jquery')
        );

        wp_enqueue_script(
            'musicidb-scripts', 
            plugins_url('/js/scripts.min.js', MUSICIDB_PLUGIN), 
            array('jquery', 'musicidb-slick'),
            filemtime( plugin_dir_path(__FILE__) . '/js/scripts.min.js' )
        );

        wp_enqueue_script(
            'musicidb-popup', 
            plugins_url('/js/popup.min.js', MUSICIDB_PLUGIN), 
            array('jquery'),
            filemtime( plugin_dir_path(__FILE__) . '/js/popup.min.js' )
        );

        // TODO: We should use more specific nonces for
        // AJAX requests
        // Add AJAX URL to scripts for use in our POST call
        wp_localize_script('musicidb-scripts', 'musicidb_ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'ajax_nonce' => wp_create_nonce('musicidb-integration-nonce')
        ));
    }

    /** Validate Input **/
    public function musicidb_validate_input($input) {
        $output = array();

        foreach ($input as $key => $value) {

            if (isset($input[$key])) {

                // General Sanitization
                if( is_string( $input[$key] ) ) {
                    $output[$key] = sanitize_text_field(stripslashes($input[$key]));
                } elseif( is_numeric( $input[$key] ) ) {
                    $output[$key] = intval( sanitize_text_field( stripslashes( $input[$key] ) ) );
                }

                // Specific Validation Rules
                if ($key == 'musicidb_api_key' && !is_string($input[$key])) {
                    $output[$key] = '';
                }

                if ($key == 'musicidb_no_event_msg' && !is_string($input[$key])) {
                    $output[$key] = '';
                }

                if( $key == 'musicidb_default_type_and_id' && !is_string( $input[$key] ) ) {
                    $output[$key] = '';
                }

                if( $key == 'musicidb_api_connected' && ( intval( $input[$key] ) !== 0 && intval( $input[$key] ) !== 1 )   ) {
                    $output[$key] = 0;
                }

                if( $key == 'musicidb_additional_venues' || $key == 'musicidb_additional_artists' ) {
                    $output[$key] = implode( ',', $this->musicidb_get_ids_from_string( $input[$key] ) );
                }

            }
        }

        return apply_filters('musicidb_validate_input', $output, $input);
    }

    /** Adds options to the admin menu **/
    public function admin_menu() {

        add_menu_page('MusicIDB Calendar', 'MusicIDB Calendar', 'manage_options', 'musicidb-integration', array($this, 'musicidb_admin'), 'dashicons-calendar');

        add_submenu_page('musicidb-integration', 'MusicIDB Calendar Settings', 'Settings', 'manage_options', 'musicidb-integration');
    
    }

    /** The plugin settings page **/
    public function musicidb_admin() {

        // check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }

        // add error/update messages

        // check if the user submitted the settings
        // wordpress will add the "settings-updated" $_GET parameter to the url
        if( isset( $_GET['settings-updated'] ) ) {
            // add settings saved message with the class of "updated"
            add_settings_error( 'musicidb_options', 'musicidb_message_updated', 'Settings Saved', 'success' );
        }

        $settings = get_option('musicidb_options');

        if( empty( $settings['musicidb_api_key'] ) ) {

            add_settings_error( 'musicidb_options', 'musicidb_warning_api_key', 'No MusicIDB API Key is set, please add your API Key', 'warning' );

        }

        // If default selection is empty (after first save)
        // set it to the first returned entity. If this fails
        // display an error message to the user
        if( !empty( $settings['musicidb_api_key'] ) && empty( $settings['musicidb_default_type_and_id'] ) ) {

            $associated_entities = $this->musicidb_get_associated_entities();

            if( !empty( $associated_entities ) ) {

                foreach( $associated_entities as $type => $mappings ) { 
                    
                    foreach( $mappings as $id => $entity ) { 
                    
                        $default_id = $type . ':' . $id;
                        break;
                    
                    }

                    break;
                }

                $settings['musicidb_default_type_and_id'] = $default_id;
                update_option( 'musicidb_options', $settings );

            } else {

                add_settings_error( 'musicidb_options', 'musicidb_error_default_type_and_id', 'There were no artists or venues found for this API key' );

            }

        }

        // show error/update messages
        settings_errors( 'musicidb_options' );
        ?>

        <div id="musicidb-events-settings" class="wrap">
            <a href="https://musicidb.com" target="_blank" class="MusicIDBlogo">
                <img 
                src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'images/MusicIDB-Logo.png'); ?>" 
                alt="MusicIDB - The Music Industry Database" />
            </a>

            <h1>MusicIDB Events Calendar</h1>
            <p>Integration Settings</p>
            <ul class="musicidb-admin-tabs tabs">
                <li class="current">
                    <a href="#musicidb-events-settings-general">General</a>
                </li>
                <li>
                    <a href="#musicidb-events-settings-sc">Shortcodes</a>
                </li>
                <li>
                    <a href="#musicidb-events">Manage My Events</a>
                </li>
            </ul>

            <div id="musicidb-events-settings-general" class="admin-tab">
                <form action="options.php" method="post">
                    <?php
                    // output security fields for the registered setting
                    settings_fields('musicidb-integration');

                    // output setting sections and their fields
                    do_settings_sections('musicidb-integration');

                    // output save settings button
                    submit_button('Save Settings');
                    ?>
                </form>
            </div>

            <?php $this->musicidb_display_shortcode_options(); ?>

            <div id="musicidb-events" class="admin-tab">
                <h2>Manage Your Calendar and Events</h2>
                <p>Visit <a href="https://MusicIDB.com" target="_blank">MusicIDB</a> to manage your calendar</p>
                <p>For help, please email <a href="mailto:Support@MusicIDB.com">Support@MusicIDB.com</a></p>
            </div>

            <p>
                <a href="https://blog.musicidb.com/help/musicidb-events-calendar-plugin-docs/" target="_blank">Plugin Documentation & Support</a> 
                &nbsp;|&nbsp; 
                <a href="https://wordpress.org/plugins/musicidb-calendar/" target="_blank">WordPress.org Plugin Repository</a> 
                &nbsp;|&nbsp; 
                <a href="https://wordpress.org/support/plugin/musicidb-calendar/reviews/" target="_blank">Leave a Review</a> 
                &nbsp;|&nbsp; 
                <a href="https://musicidb.com/" target="_blank">MusicIDB.com</a>
            </p>

        </div><!-- /.wrap -->
        <?php

    }

    /** Used for customizing the general settings section **/
    public function musicidb_settings_section($args) {
        //Nothing to do here...
    }

    /** The API Key Field
     **
     **    @param $args The arguments passed to the register function
     **/
    public function musicidb_api_key_field($args) {
        //Get the existing options
        $settings = get_option('musicidb_options');
        $connected = 0;
        $error = "";
        ?>

        <input type="password" id="<?php echo esc_attr($args['label_for']); ?>"
               name="musicidb_options[<?php echo esc_attr($args['label_for']); ?>]"
               value="<?php echo sanitize_text_field($this->api_key); ?>"/>

        <?php

            //Check if API Key exists
            try {
                
                if( $this->api_key ) {
                    
                    $api_key_response = MusicIDB_API::request( 'api_key', 'get_details' );

                    if( 200 === $api_key_response->get_status() ) {
                        $connected = 1;
                    }

                }

            } catch( Api_Exception $e ) {
            
                error_log( $e->getMessage() );
                $error = $e->getMessage();

            } finally {
                
                $settings['musicidb_api_connected'] = $connected;
                update_option( 'musicidb_options', $settings );

            }

        ?>
    
        <?php if( $connected ): ?>
            <span class="green connected">Connected :)</span>
        <?php else: ?>
            <?php if( $settings['musicidb_api_key'] ): ?>
                <span class="red not-connected">Incorrect API Key or could not connect to MusicIDB</span>
            <?php endif; ?>

            <?php if( $error ): ?>
                <p>Error: <?php echo esc_attr($error); ?></p>
            <?php endif; ?>
        <?php endif; ?>

        <?php
    }

    public function musicidb_default_field( $args ) {
        $settings = get_option('musicidb_options');

        $connected = $settings['musicidb_api_connected'];

        if( 0 === $connected ) {
            return false;
        }

        $defaults = get_musicidb_defaults();
        $default_type = $defaults['type'];
        $default_id = $defaults['id'];

        $associated_entities = $this->musicidb_get_associated_entities();
        
        ?>
        <select id="<?php echo esc_attr($args['label_for']); ?>" name="musicidb_options[<?php echo esc_attr($args['label_for']); ?>]">
            <?php foreach ($associated_entities as $type => $mappings ): ?>
                <optgroup label="<?php esc_attr_e( ucfirst( $type ) ); ?>">

                    <?php 
                        foreach( $mappings as $id => $entity ) {

                            $escaped_val = esc_attr( $type . ':' . $id );
                            $selected = ( $id == $default_id ) ? ' selected' : '';
                            $escaped_name = esc_html( $entity->get_name() );

                            printf( '<option value="%s"%s>%s</option>', $escaped_val, $selected, $escaped_name );

                        }
                    ?>

                </optgroup>
            <?php endforeach; ?>
        </select>
        <?php
    }

    public function musicidb_additional_venues_field( $args ) {

        $settings = get_option('musicidb_options');
        $connected = $settings['musicidb_api_connected'];

        if( 0 === $connected ) {
            return false;
        }

        ?>

        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>"
               name="musicidb_options[<?php echo esc_attr($args['label_for']); ?>]"
               value="<?php esc_attr_e($settings[$args['label_for']]); ?>" />
        <?php

    }

    public function musicidb_additional_artists_field( $args ) {

        $settings = get_option('musicidb_options');
        $connected = $settings['musicidb_api_connected'];

        if( 0 === $connected ) {
            return false;
        }

        ?>

        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>"
               name="musicidb_options[<?php echo esc_attr($args['label_for']); ?>]"
               value="<?php esc_attr_e($settings[$args['label_for']]); ?>" />
        <?php

    }

    public function musicidb_no_event_msg_field($args) {
        //Get the existing options
        $settings = get_option('musicidb_options');
        $connected = 0;
        $error = "";
        ?>
        <input type="text" id="<?php echo esc_attr($args['label_for']); ?>"
               name="musicidb_options[<?php echo esc_attr($args['label_for']); ?>]"
               value="<?php esc_attr_e($settings[$args['label_for']]); ?>"/>
        <?php
    }

    //WP AJAX hook functions
    public function load_events_list() {
        check_ajax_referer('musicidb-integration-nonce', 'security');

        $settings = get_option('musicidb_options');

        $defaults = get_musicidb_defaults();

        $default_id = $defaults['id'];
        $default_type = $defaults['type'];
        $default_style = $defaults['style'];

        //Set and validate post vars
        $largePics = (isset($_POST['largePics']) && is_bool( $_POST['largePics'] ) && $_POST['largePics'] === true ) ? true : false;
        $listType = (isset($_POST['listType']) && $_POST['listType'] && is_string($_POST['listType'])) ? sanitize_text_field($_POST['listType']) : 'upcoming';
        $page = (isset($_POST['page']) && $_POST['page'] && is_numeric($_POST['page'])) ? intval($_POST['page']) : 1;
        $resultsPerPage = (isset($_POST['resultsPerPage']) && $_POST['resultsPerPage'] && is_numeric($_POST['resultsPerPage'])) ? intval($_POST['resultsPerPage']) : 15;
        $descrip = (isset($_POST['descrip']) && intval( $_POST['descrip'] == 1)) ? true: false;
        $buttons = (isset($_POST['buttons']) && $_POST['buttons'] && is_string($_POST['buttons'])) ? sanitize_text_field($_POST['buttons']) : 'left';
        $listStyle = ( !empty( $_POST['list_style'] ) && is_string( $_POST['list_style'] ) ) ? sanitize_text_field( $_POST['list_style'] ) : $default_style;

        // strip out any unicode chars that made it this far
        $listType = $this->musicidb_strip_unicode( $listType );
        $page = $this->musicidb_strip_unicode( $page );
        $resultsPerPage = $this->musicidb_strip_unicode( $resultsPerPage );
        $buttons = $this->musicidb_strip_unicode( $buttons );
        $listStyle = musicidb_map_list_style( $this->musicidb_strip_unicode( $listStyle ) );

        $show_venue = ('compact' == $listStyle) ? 'show' : 'hide';
        if( !empty( $_POST['show_venue'] ) && ($_POST['show_venue'] == 'show' || $_POST['show_venue'] == 'hide' ) ) {
            $show_venue = $_POST['show_venue'];
        }

        $show_artist = 'show';
        if( !empty( $_POST['show_artist'] ) && ($_POST['show_artist'] == 'show' || $_POST['show_artist'] == 'hide' ) ) {
            $show_artist = $_POST['show_artist'];
        }

        $template = musicidb_map_list_template( $listStyle );

        // Repetitive & can be abstracted, but
        // need to decide how to do this without requiring
        // extra unnecessary db calls
        // TODO: DRY - Clean up 
        $entities = array();

        if( !empty( $_POST['entity_id'] ) && !empty( $_POST['entity_type'] ) ) {

            // Fallback for legacy shortcodes

            $entity_id = ( $_POST['entity_id'] == intval( $_POST['entity_id'] ) )
                            ? intval( $_POST['entity_id'] ) 
                            : $default_id;

            $entity_type = ( is_string( $_POST['entity_type'] ) )
                            ? $_POST['entity_type']
                            : $default_type;

            $entities[$entity_type] = array( strval( $entity_id ) );

        } elseif( !empty( $_POST['entity_id'] ) ) {

            // if id attr was supplied
            $entities = musicidb_parse_id_att( $_POST['entity_id'] );

        } else {

            // fallback to defualt
            $entities = musicidb_parse_id_att( $settings['musicidb_default_type_and_id'] );

        }

        require_once musicidb_get_template_part( 'musicidb', 'view-' . $template, false );
        
        wp_die();
    }

    public function load_event_details() {

        check_ajax_referer('musicidb-integration-nonce', 'security');

        $defaults = get_musicidb_defaults();
        $default_id = $defaults['id'];
        $default_type = $defaults['type'];
        $default_style = $defaults['style'];

        $eventId = (isset($_POST['eventId']) && $_POST['eventId'] && is_numeric($_POST['eventId'])) ? intval($_POST['eventId']) : '';

        $list_style = ( !empty( $_POST['list_style'] ) && is_string( $_POST['list_style'] ) ) ? sanitize_text_field( $_POST['list_style'] ) : $default_style;

        $list_style = musicidb_map_list_style( $list_style );

        $show_venue = ('compact' == $list_style) ? 'show' : 'hide';
        
        if( !empty( $_POST['show_venue'] ) && ($_POST['show_venue'] == 'show' || $_POST['show_venue'] == 'hide' ) ) {
            $show_venue = $_POST['show_venue'];
        }

        require_once musicidb_get_template_part( 'musicidb', 'event-detail', false );

        wp_die();

    }

    public function musicidb_load_shortcode_options() {

        check_ajax_referer( 'load-shortcode-modal-nonce', '_nonce' );

        set_query_var( 'is_modal', true );

        echo '<div id="musicidb-events-settings" class="wrap">';
            $this->musicidb_display_shortcode_options();
        echo '</div>';

        wp_die();

    }

    private function musicidb_display_shortcode_options() {

        $settings = get_option( 'musicidb_options' );
        $connected = $settings['musicidb_api_connected'];

        $associated_entities = $this->musicidb_get_associated_entities();

        $defaults = get_musicidb_defaults();
        $default_id = $defaults['id'];
        $default_type = $defaults['type'];
        $default_style = $defaults['style'];

        if( !empty( $associated_entities['venue'] ) ) {

            reset( $associated_entities['venue'] );
            $default_venue_id = key( $associated_entities['venue'] );

            if( 'venue' == $default_type )
                $default_venue_id = $default_id;
        
        }

        include( 'admin/admin-page-sc.php' );

    }

    private function musicidb_get_associated_entities() {

        $settings = get_option( 'musicidb_options' );
        $entity_responses = array();

        try {

            $api_key_response = MusicIDB_API::request( 'api_key', 'get_details' );

            if( 200 === $api_key_response->get_status() ) {

                $entity_ids = $api_key_response->get_associated_entities();
                $entity_responses = get_transient( 'musicidb_integration_entity_responses' );

                if( false === $entity_responses ) {

                    $entity_responses = array();
                    $entity_ids['venue'] = !empty( $entity_ids['venue'] ) ? $entity_ids['venue'] : array();
                    $entity_ids['artist'] = !empty( $entity_ids['artist'] ) ? $entity_ids['artist'] : array();

                    $entity_ids['venue'] = array_merge( $entity_ids['venue'], $this->musicidb_get_ids_from_string( $settings['musicidb_additional_venues'] ) );
                    $entity_ids['artist'] = array_merge( $entity_ids['artist'], $this->musicidb_get_ids_from_string( $settings['musicidb_additional_artists'] ) );

                    foreach( $entity_ids as $type => $ids ) {
    
                        foreach( $ids as $id ) {

                            $entity_response = MusicIDB_API::request( $type, 'get_' . $type, array( 'id' => $id ) );

                            if( 200 === $entity_response->get_status() ) {

                                $response_data = array();

                                if( 'artist' == $type ) {
                                    $response_data = $entity_response->get_artists();
                                } elseif( 'venue' == $type ) {
                                    $response_data = $entity_response->get_venues();
                                }

                                if( !empty( $response_data ) ) {
                                    
                                    $entity_responses[$type][$id] = $response_data[0];

                                    // Handle initial save of defaults
                                    if( empty( $settings['musicidb_default_type_and_id'] ) ) {
                                        $settings['musicidb_default_type_and_id'] = $type . ':' . $id; 
                                    }
                                }

                            }

                        }

                    }

                    set_transient( 'musicidb_integration_entity_responses', $entity_responses, 2 * HOUR_IN_SECONDS );
                }

            }

        } catch( Api_Exception $e ) {
            
            error_log( $e->getMessage() );
            $error = $e->getMessage();

        } finally {

            return $entity_responses;

        }

    }

    private function musicidb_get_ids_from_string( $csv ) {

        if( empty( $csv ) )
            return array();

        $ids = array();
        $raw_ids = explode( ',', $csv );

        foreach( $raw_ids as $id ) {
            
            if( $id != intval( $id ) )
                continue;

            if( empty( trim( $id ) ) )
                continue;

            $ids[] = trim( intval( $id ) );

        }

        return $ids;

    }

    /** Register the Shortcode **/
    public static function sc_musicidb_events($atts) {

        $settings = get_option('musicidb_options');
        $connected = $settings['musicidb_api_connected'];
        $api_key = $settings['musicidb_api_key'];
        
        $defaults = get_musicidb_defaults();
        $default_type = $defaults['type'];
        $default_id = $defaults['id'];
        $default_style = $defaults['style'];

        $atts = shortcode_atts( get_default_musicidb_args(), $atts, 'musicidb');
        $limit = isset($atts['numevents']) ? intval( $atts['numevents'] ) : 15;

        $entities = array();

        if( !empty( $atts['id'] ) && !empty( $atts['type'] ) ) {

            // Fallback for legacy shortcodes

            $entity_id = ( $atts['id'] == intval( $atts['id'] ) )
                            ? intval( $atts['id'] ) 
                            : $default_id;

            $entity_type = ( is_string( $atts['type'] ) )
                            ? $atts['type']
                            : $default_type;

            $entities[$entity_type] = array( strval( $entity_id ) );

        } elseif( !empty( $atts['id'] ) ) {

            // if id attr was supplied
            $entities = musicidb_parse_id_att( $atts['id'] );

        } else {

            // fallback to defualt
            $entities = musicidb_parse_id_att( $settings['musicidb_default_type_and_id'] );

        }

        ob_start();

        //Include the shortcode output
        include musicidb_get_template_part( 'musicidb', 'events-sc-output', false );

        $content = ob_get_clean();

        return $content;

    }

    /** Register the Shortcode **/
    public static function sc_musicidb_featured_events($atts) {
        $settings = get_option('musicidb_options');
        $connected = $settings['musicidb_api_connected'];
        $api_key = $settings['musicidb_api_key'];
        $limit = isset($atts['numevents']) ? intval( $atts['numevents'] ) : 10;

        $_instance = self::get_instance();
        $associated_entities = $_instance->musicidb_get_associated_entities();

        $defaults = get_musicidb_defaults();
        $default_id = $defaults['id'];
        $default_type = $defaults['type'];

        reset( $associated_entities['venue'] );
        $default_venue_id = key( $associated_entities['venue'] );

        if( 'venue' == $default_type )
            $default_venue_id = $default_id;

        if( !is_array( $atts ) ) {
            $atts = array();
        }

        if( empty( $atts['id'] ) ) {
            $atts['id'] = $default_venue_id;
        }

        $atts = shortcode_atts( get_default_featured_musicidb_args(), $atts, 'musicidb-featured-slider' );

        ob_start();

        //Include the shortcode output
        include musicidb_get_template_part('musicidb', 'featured-events-sc-output', false);

        $content = ob_get_clean();

        return $content;
    } 

    function musicidb_action_links( $links ) {
       $links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=musicidb-integration') ) .'">Settings</a>';
       $links[] = '<a href="https://musicidb.com" target="_blank">MusicIDB.com</a>';

       return $links;
    }

    function musicidb_strip_unicode( $target_string ) {

        $chr_map = array(

           // Windows codepage 1252
           "\xC2\x82", // U+0082⇒U+201A single low-9 quotation mark
           "\xC2\x84", // U+0084⇒U+201E double low-9 quotation mark
           "\xC2\x8B", // U+008B⇒U+2039 single left-pointing angle quotation mark
           "\xC2\x91", // U+0091⇒U+2018 left single quotation mark
           "\xC2\x92", // U+0092⇒U+2019 right single quotation mark
           "\xC2\x93", // U+0093⇒U+201C left double quotation mark
           "\xC2\x94", // U+0094⇒U+201D right double quotation mark
           "\xC2\x9B", // U+009B⇒U+203A single right-pointing angle quotation mark

           // Regular Unicode 
           "\xC2\xAB"    , // U+00AB left-pointing double angle quotation mark
           "\xC2\xBB"    , // U+00BB right-pointing double angle quotation mark
           "\xE2\x80\x98", // U+2018 left single quotation mark
           "\xE2\x80\x99", // U+2019 right single quotation mark
           "\xE2\x80\x9A", // U+201A single low-9 quotation mark
           "\xE2\x80\x9B", // U+201B single high-reversed-9 quotation mark
           "\xE2\x80\x9C", // U+201C left double quotation mark
           "\xE2\x80\x9D", // U+201D right double quotation mark
           "\xE2\x80\x9E", // U+201E double low-9 quotation mark
           "\xE2\x80\x9F", // U+201F double high-reversed-9 quotation mark
           "\xE2\x80\xB9", // U+2039 single left-pointing angle quotation mark
           "\xE2\x80\xBA", // U+203A single right-pointing angle quotation mark
        );

        return str_replace( $chr_map, '', $target_string );

    }

    public function get_api_key() {
        return $this->api_key;
    }

    public static function get_instance() {
        if(!empty(self::$instance)) {
            return self::$instance;
        } else {
            return new self();
        }
    }

}

// Instantiate the Plugin
$MusicIDBIntegration = MusicIDBIntegration::get_instance();

// Instantiate the API Accessor
$MusicIDB_API = MusicIDB_API::get_instance();

//Register the shortcode
add_shortcode('musicidb', array($MusicIDBIntegration, 'sc_musicidb_events'));
add_shortcode('musicidb-featured-slider', array($MusicIDBIntegration, 'sc_musicidb_featured_events'));

if( !function_exists( 'musicidb_map_list_style' ) ) {

    function musicidb_map_list_style( $list_style ) {

        $styles = apply_filters( 'musicidb_list_style_names', array(
            // Full
            'full' => 'full',
            'listwithpics' => 'full',
            
            // Big Pics
            'largepics' => 'largepics',
            'bigpics' => 'largepics',

            // Posterboard
            'posterboard' => 'posterboard',
            
            // Compact
            'compact' => 'compact',
            'simple' => 'compact',
        ) );

        return !empty( $styles[$list_style] ) ? $styles[$list_style] : 'full';

    }

}

if( !function_exists( 'musicidb_map_list_template' ) ) {

    function musicidb_map_list_template( $list_style ) {

        $templates = apply_filters( 'musicidb_list_templates', array(
            // Full
            'full' => 'full',
            'listwithpics' => 'full',
            
            // Big Pics
            'largepics' => 'full',
            'bigpics' => 'full',

            // Posterboard
            'posterboard' => 'posterboard',
            
            // Compact
            'compact' => 'compact',
            'simple' => 'compact',
        ) );

        return !empty( $templates[$list_style] ) ? $templates[$list_style] : 'full';

    }

}

if (!function_exists('musicidb_featured_events')) {
    /**
     * Function for use in theme files
     * @param array args The arguments for the MusicIDB Widget Instance
     *
     **/
    function musicidb_featured_events($args = array()) {
        $args = wp_parse_args($args, get_default_featured_musicidb_args());
        echo MusicIDBIntegration::sc_musicidb_featured_events($args);
    }
}

if (!function_exists('musicidb_events')) {
    /**
     * Function for use in theme files
     * @param array args The arguments for the MusicIDB Widget Instance
     *
     **/
    function musicidb_events($args = array()) {
        $args = wp_parse_args($args, get_default_musicidb_args());
        echo MusicIDBIntegration::sc_musicidb_events($args);
    }
}

if( !function_exists( 'musicidb_get_link_details' ) ) {

    function musicidb_get_link_details( $link_name ) {

        $class_names = array (
            'Facebook' => array( 
                'class' => "fbLink",
            ),
            'Twitter' => array( 
                'class' => "twitterLink",
            ),
            'Google+' => array( 
                'class' => "googlePlusLink",
            ),
            'Bandcamp' => array( 
                'class' => "bandcampLink",
            ),
            'Instagram' => array( 
                'class' => "instagramLink",
            ),
            'Official' => array( 
                'class' => "webLink",
                'place' => "Their Official Website",
            ),
            'SoundCloud' => array( 
                'class' => "soundcloudLink",
            ),
            'ReverbNation' => array( 
                'class' => "reverbLink",
            ),
            'YouTube' => array( 
                'class' => "youtubeLink",
            ),
            'Wikipedia' => array( 
                'class' => "otherLink wikiLink",
            ),
            'iTunes' => array( 
                'class' => "otherLink itunesLink",
            ),
            'Spotify' => array( 
                'class' => "otherLink spotLink",
            ),
            'Buy Music' => array( 
                'class' => "otherLink storeLink",
                'place' => "Their Official Store"
            ),
            'Other' => array( 
                'class' => "otherLink",
            )
        );

        $class_name = !empty( $class_names[$link_name]['class'] ) 
                        ? $class_names[$link_name]['class'] 
                        : 'otherLink';

        $place = !empty( $class_names[$link_name]['place'] ) 
                    ? $class_names[$link_name]['place']
                    : $link_name;

        return array( 
            'name' => $link_name,
            'class' => $class_name, 
            'place' => $place,
        );
    }

}

if( !function_exists( 'musicidb_parse_id_att' ) ) {
    function musicidb_parse_id_att( $id ) {
        
        $entity_types_and_ids = explode( ',', $id );
        $separated_types_and_ids = array();

        foreach( $entity_types_and_ids as $type_and_id ) {

            $type_id_split = explode( ':', $type_and_id );

            if( count( $type_id_split ) < 2 )
                continue; 

            $separated_types_and_ids[$type_id_split[0]][] = strval( intval( $type_id_split[1] ) ); // IDs should be ints, API expects strings

        }

        return $separated_types_and_ids;

    }
}

if( !function_exists( 'musicidb_get_embed_codes' ) ) {
    
    function musicidb_get_embed_codes( $artist_media ) {
        
        $videos = $artist_media->get_videos();
        $audio = $artist_media->get_audio();
        $max_codes = 2;

        $embedCodes = array();

        if( !empty( $videos ) && !empty( $audio ) ) {
            
            $embedCodes[] = array(
                'type' => 'video',
                'embed_code' => $videos[0],
            );

            $embedCodes[] = array( 
                'type' => 'audio',
                'embed_code' => $audio[0]
            );

        } elseif( !empty( $videos ) ) {

            // latest media first
            $videos = array_reverse( $videos );

            for( $i = 0; $i < $max_codes; $i++ ) {
                if( empty( $videos[$i] ) )
                    break;

                $embedCodes[] = array(
                    'type' => 'video',
                    'embed_code' => $videos[$i],
                );
            }

        } elseif( !empty( $audio ) ) {

            // latest media first
            $audio = array_reverse($audio);

            for( $i = 0; $i < $max_codes; $i++ ) {
                if( empty( $audio[$i] ) )
                    break;

                $embedCodes[] = array(
                    'type' => 'audio',
                    'embed_code' => $audio[$i]
                );
            }

        }

        return $embedCodes;

    }

}

/**
 *  Gets default args for shortcode/template include
 **/
if( !function_exists( 'get_default_featured_musicidb_args' ) ) {
    function get_default_featured_musicidb_args() {
        return array(
            'id' => '',
            'ticketdefault' => null,
            'leftflag' => 'Featured Events',
            'titlesize' => '22',
            'numevents' => 10,
            'background' => '#000',
            'fallbackimage' => null
        );
    }
}

/**
 *  Gets default args for shortcode/template include
 **/
if( !function_exists( 'get_default_musicidb_args' ) ) {
    function get_default_musicidb_args() {
        $defaults = get_musicidb_defaults();
        $default_id = $defaults['id'];
        $default_type = $defaults['type'];
        $default_style = $defaults['style'];

        return array(
            'id' => $default_type . ':' . $default_id,
            'type' => '',
            'style' => $default_style,
            'display' => 'img',
            'theme' => 'light',
            'descrip' => FALSE,
            'largepics' => false,
            'view' => 'list',
            'numevents' => 15,
            'buttons' => 'left',
            'showvenue' => '',
            'showartist' => '',
        );

    }
}

if( !function_exists( 'get_musicidb_defaults' ) ) {
    function get_musicidb_defaults() {

        $settings = get_option('musicidb_options');
        $split_type_and_id = explode( ':', $settings['musicidb_default_type_and_id'] );

        if( count( $split_type_and_id ) < 2 ) {
            return false;
        }

        $nice_split = array(
            'type' => $split_type_and_id[0],
            'id' => $split_type_and_id[1],
            'style' => ( 'venue' == $split_type_and_id[0] ) ? 'full' : 'compact',
        );

        return $nice_split;

    }
}

if( !function_exists( 'musicidb_get_image_at_size' ) ) {

    /**
     *        Gets the specified image at the specified size (sizes must match MusicIDB)
     *
     * @param $url    The base image URL
     * @param $size    The size to get the image at
     **/
    function musicidb_get_image_at_size($url, $size) {
        if (strripos($url, ".")) {
            $lastDot = substr($url, strripos($url, "."));
            $resizedUrl = str_replace($lastDot, "-" . $size . $lastDot, $url);
        } else {
            error_log("Could not resize image url: " . $url . " - Using original url.");
            $resizedUrl = $url;
        }

        return esc_url( $resizedUrl, array( 'http', 'https' ) );
    }

}

//Template loader functions - Allows overriding by theme

if (!function_exists('musicidb_get_template_part')) {
    function musicidb_get_template_part($slug, $name = null, $load = true) {
        // Execute code for this part
        do_action('get_template_part_' . $slug, $slug, $name);

        // Setup possible parts
        $templates = array();

        if (isset($name))
            $templates[] = $slug . '-' . $name . '.php';
        else
            $templates[] = $slug . '.php';

        // Allow template parts to be filtered
        $templates = apply_filters('musicidb_get_template_part', $templates, $slug, $name);

        // Return the part that is found

        return musicidb_locate_template($templates, $load, false);
    }
}

if (!function_exists('musicidb_locate_template')) {
    function musicidb_locate_template($template_names, $load = false, $require_once = true)
    {
        // No file found yet
        $located = false;

        // Try to find a template file
        foreach ((array)$template_names as $template_name) {

            // Continue if template is empty
            if (empty($template_name))
                continue;

            // Trim off any slashes from the template name
            $template_name = ltrim($template_name, '/');

            // Check child theme first
            if (file_exists(trailingslashit(get_stylesheet_directory()) . 'musicidb-calendar/' . $template_name)) {
                $located = trailingslashit(get_stylesheet_directory()) . 'musicidb-calendar/' . $template_name;
                break;

                // Check parent theme next
            } elseif (file_exists(trailingslashit(get_template_directory()) . 'musicidb-calendar/' . $template_name)) {
                $located = trailingslashit(get_template_directory()) . 'musicidb-calendar/' . $template_name;
                break;

                // Check plugin last
            } elseif (file_exists(trailingslashit(MUSICIDB_PLUGIN_DIR) . $template_name)) {
                $located = trailingslashit(MUSICIDB_PLUGIN_DIR) . $template_name;
                break;
            }
        }

        if ((true == $load) && !empty($located))
            load_template($located, $require_once);

        return $located;
    }
}

if( !function_exists( 'musicidb_get_allowed_embed_tags' ) ) {

    function musicidb_get_allowed_embed_tags() {

        return array(
            'iframe' => array(
                'src'             => array(),
                'height'          => array(),
                'width'           => array(),
                'frameborder'     => array(),
                'allowfullscreen' => array(),
            ),
        );

    }

}

endif; //if(!class_exists)
