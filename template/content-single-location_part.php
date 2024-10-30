<?php
/**
 * Created 16.06.2020
 * Version 1.0.0
 * Last update 23.06.2020
 * Author: Alex L
 *
 */

$id            = get_the_ID();
$location_name = get_post_meta( $id, '_location', true );
$locationID    = get_post_meta( $id, '_id_location', true );

if ( $location_name ):
	?>
	<div class="location-part-url">
		<h3 class="wpem-heading-text"><?php echo __( 'Location Page', 'wp-event-manager-location' ) ?></h3>
		<div class="location">
			<?php if ( $locationID == '' ) {
				global $wpdb;
				
				//get names of all businesses
				$name = $wpdb->esc_like( stripslashes( $location_name ) ) . '%'; //escape for use in LIKE statement
				$sql  = "select post_title, ID
				from $wpdb->posts
				where post_title like %s
				and post_type='location' and post_status='publish'";
				
				$sql     = $wpdb->prepare( $sql, $name );
				$results = $wpdb->get_results( $sql );
//				var_dump( $results );
			} ?>
			<p><a href="<?php the_permalink( $locationID ? $locationID : $results[0]->ID ); ?>"><?php echo $location_name;
					?></a>
			</p>
		</div>
	</div>
<?php endif; ?>