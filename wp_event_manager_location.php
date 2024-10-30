<?php
/**
 * Created 11.06.2020
 * Version 1.0.0
 * Last update
 * Author: Alex L
 *
 */

/**
 * Plugin Name:  Location for WP Event Manager
 * Plugin URI: http://justwebagency.com/
 * Description: Add Location page in single event
 * Author: Alex L
 * Author URI: https://gitlab.com/AlsconWeb
 * Text Domain: wp-event-manager-calendar
 * Domain Path: /languages
 * Version: 1.0.0
 * Since: 1.0
 * Requires WordPress Version at least: 4.1
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'GAM_Updater' ) ) {
	include( 'autoupdater/gam-plugin-updater.php' );
}

function pre_check_before_installing_location () {
	/*
	 * Check weather WP Event Manager is installed or not
 	 */
	if ( ! in_array( 'wp-event-manager/wp-event-manager.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		global $pagenow;
		if ( $pagenow == 'plugins.php' ) {
			echo '<div id="error" class="error notice is-dismissible"><p>';
			echo __( 'WP Event Manager is require to use WP Event Manager - Location', 'wp-event-manager-location' );
			echo '</p></div>';
		}
	}
}

add_action( 'admin_notices', 'pre_check_before_installing_location' );

class WP_Event_Manager_Location extends GAM_Updater {
	private $gMapKye;
	
	public function __construct () {
		define( 'EVENT_MANAGER_LOCATION_VERSION', '1.0.0' );
		define( 'EVENT_MANAGER_LOCATION_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
		define( 'EVENT_MANAGER_LOCATION_PLUGIN_URL', untrailingslashit( plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename( __FILE__ ) ) ) );
		
		include( 'metabox/addMetaBoxLocation.php' );
		include( 'ajax/ajaxAutoComplete.php' );
		include( 'admin/wp_event_manager_location_settings.php' );
		
		add_action( 'init', [ $this, 'AddCTPLocation' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'addAdminScript' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'addScript' ] );
		add_action( 'single_event_sidebar_end', [ $this, 'addLocationPart' ], 10 );
		
		add_filter( 'single_template', [ $this, 'addCustomTemplateSingle' ], 50 );
		
		new \event_manager\metaBox\addMetaBoxLocation();
		// Init updates
		$this->init_updates( __FILE__ );
		$fieldsDefault = get_option( 'event_manager_form_fields', false );
		
		
		if ( ! array_key_exists( 'location', $fieldsDefault['event'] ) ) {
			
			$newField = $fieldsDefault['event'] + [
					'location' => [
						'label'       => 'Location',
						'type'        => 'text',
						'description' => '',
						'placeholder' => '',
						'priority'    => 19,
					],
					
					'id_location' => [
						'label'       => 'ID location',
						'type'        => 'text',
						'description' => '',
						'placeholder' => '',
						'priority'    => 20,
					],
				
				];
			
			$fieldsDefault['event'] = $newField;
			
			
			update_option( 'event_manager_form_fields', $fieldsDefault );
		}
		
		$this->gMapKye = get_option( 'event_manager_google_maps_api_key', false );
	}
	
	public function AddCTPLocation () {
		register_post_type( 'location', [
			'labels'             => [
				'name'              => 'Locations',
				'singular_name'     => 'Location',
				'add_new'           => 'Add New Location',
				'add_new_item'      => 'Add New Location',
				'edit_item'         => 'Edit Location',
				'new_item'          => 'New Location',
				'view_item'         => 'View Location',
				'search_items'      => 'Search Location',
				'not_found'         => 'Not found Location',
				'parent_item_colon' => '',
				'menu_name'         => 'Location',
			
			],
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => true,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => [ 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'revision' ],
			'show_in_menu'       => 'edit.php?post_type=event_listing',
		] );
	}
	
	public function addAdminScript ( $hook_suffix ) {
		
		if ( $hook_suffix == 'post-new.php' || $hook_suffix == 'post.php' ) {
			wp_enqueue_script( 'wp-event-manager-google-maps-admin', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/js/gMap
			.js', [ 'jquery' ], '1.0.0' );
			wp_enqueue_script( 'wp-event-autocomplete-location', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/js/autoCompleteField.js', [ 'jquery' ], '1.0.0' );
			wp_enqueue_script( 'jq-autocomplete', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/js/jquery.auto-complete.min.js', [ 'jquery' ], '1.0.0' );
			wp_enqueue_script( 'jwa-google-maps-admin', '//maps.googleapis.com/maps/api/js?key=' . $this->gMapKye . '&libraries=places&language=en',
				[ 'jquery' ], '1.0.0' );
			wp_localize_script( 'wp-event-autocomplete-location', 'jwa_ajax', [
				'url' => admin_url( 'admin-ajax.php' ),
			] );
			wp_localize_script( 'wp-event-manager-google-maps-admin', 'gKey', [
				'key' => $this->gMapKye,
			] );
			
			wp_enqueue_style( 'jq-autocomplete-css', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/css/jquery.auto-complete.css' );
		}
		
		
	}
	
	public function addScript () {
		if ( is_singular( 'location' ) ) {
			wp_enqueue_style( 'front-css', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/css/page.css' );
			
			wp_enqueue_script( 'wp-event-manager-front-location', EVENT_MANAGER_LOCATION_PLUGIN_URL . '/assets/js/front.js', [ 'jquery' ], '1.0.0' );
			wp_localize_script( 'wp-event-manager-front-location', 'gKey', [
				'key' => $this->gMapKye,
			] );
		}
	}
	
	/**
	 * Overrides the page template for a single post
	 *
	 * @param $template
	 *
	 * @return string
	 */
	public function addCustomTemplateSingle ( $template ) {
		global $post;
		
		if ( 'location' === $post->post_type ) {
			return EVENT_MANAGER_LOCATION_PLUGIN_DIR . '/template/single-location.php';
		}
		
		return $template;
	}
	
	public function addLocationPart () {
		get_event_manager_template( 'content-single-location_part.php', [], 'content-single-location_part', EVENT_MANAGER_LOCATION_PLUGIN_DIR . '/template/' );
	}
}

$GLOBALS['event_manager_location'] = new WP_Event_Manager_Location();