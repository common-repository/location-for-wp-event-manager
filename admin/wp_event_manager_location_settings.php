<?php
/**
 * Created 21.06.2020
 * Version 1.0.0
 * Last update
 * Author: Alex L
 *
 */

/*
* This file use for setings at admin site for google maps settings.
*/
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

class WP_Event_Manager_Location_Settings {
	
	public function __construct () {
		add_filter( 'event_manager_settings', [ $this, 'locationSettings' ] );
		
	}
	
	public function locationSettings ( $settings ) {
		
		$sidebarArray              = [];
		$sidebarArray['nosidebar'] = 'Select Sidedar';
		foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
			
			$sidebarArray[ ucwords( $sidebar['id'] ) ] = ucwords( $sidebar['name'] );
		}
		
		$settings['location_settings'] = [
			
			__( 'Location Settings', 'wp-event-manager-google-maps' ),
			[
				[
					'name'       => 'addthis_shord_code',
					'std'        => '',
					'label'      => __( 'Add This Shordcode ', 'wp-event-manager-google-maps' ),
					'desc'       => __( 'Enter short code for Add This plugin', 'wp-event-manager-google-maps' ),
					'type'       => 'text',
					'attributes' => [],
				],
				[
					'name'    => 'sidebar_single_location',
					'std'     => '',
					'label'   => __( 'Sidebar', 'wp-event-manager-google-maps' ),
					'desc'    => __( 'Select Sidebar', 'wp-event-manager-google-maps' ),
					'type'    => 'select',
					'options' => $sidebarArray,
				],
			],
		];
		
		return $settings;
	}
	
}

new WP_Event_Manager_Location_Settings();