<?php
/**
 * Created 15.06.2020
 * Version 1.0.0
 * Last update
 * Author: Alex L
 *
 */

add_action( 'wp_ajax_nopriv_autocomplete_location', 'ajax_listings_location' );
add_action( 'wp_ajax_autocomplete_location', 'ajax_listings_location' );


function ajax_listings_location () {
	global $wpdb;
	
	//get names of all businesses
	$name = $wpdb->esc_like( stripslashes( $_POST['location'] ) ) . '%'; //escape for use in LIKE statement
	$sql  = "select post_title, ID
		from $wpdb->posts
		where post_title like %s
		and post_type='location' and post_status='publish'";
	
	$sql = $wpdb->prepare( $sql, $name );
	
	$results = $wpdb->get_results( $sql );
	
	//copy the business titles to a simple array
	$titles = [];
	foreach ( $results as $r ) {
		$titles[]                                       = addslashes( $r->post_title );
		$IDs[ str_replace( ' ', '_', $r->post_title ) ] = $r->ID;
	}
	
	wp_send_json_success( [ 'title' => $titles, 'ID' => $IDs ] );
	
}