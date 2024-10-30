<?php
/**
 * Created 11.06.2020
 * Version 1.0.0
 * Last update
 * Author: Alex L
 *
 */

/**
 * Create metaBox in Location post type
 */

namespace event_manager\metaBox;


class addMetaBoxLocation {
	public function __construct () {
		add_action( 'add_meta_boxes', [ $this, 'addMetaBoxInLocationPost' ] );
		add_action( 'save_post', [ $this, 'saveLocation' ] );
	}
	
	public function addMetaBoxInLocationPost ( $post_type ) {
		$post_types = [ 'location' ];
		
		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'location-section',
				__( 'Location Information', 'myplugin_textdomain' ),
				[ $this, 'jwaLocationRender' ],
				$post_type,
				'advanced',
				'high'
			);
		}
	}
	
	public function jwaLocationRender ( $post ) {
		wp_nonce_field( 'jwaLocationMetaBox', 'jwaLocationMetaBoxNonce' );
		$id                = $post->ID;
		$lat               = get_post_meta( $id, '_jwa_lat_location', true );
		$lng               = get_post_meta( $id, '_jwa_lng_location', true );
		$address           = get_post_meta( $id, '_jwa_address', true );
		$phone             = get_post_meta( $id, '_jwa_phone', true );
		$fax               = get_post_meta( $id, '_jwa_fax', true );
		$web               = get_post_meta( $id, '_jwa_web', true );
		$locationType      = get_post_meta( $id, '_jwa_location_type', true );
		$email             = get_post_meta( $id, '_jwa_email', true );
		$openHours         = get_post_meta( $id, '_jwa_open_hours', true );
		$indexPrice        = get_post_meta( $id, '_jwa_price_index', true );
		$indexPriceOptions = [
			'n_a'            => 'N/A',
			'free'           => 'Free',
			'inexpensive'    => 'Inexpensive',
			'moderate'       => 'Moderate',
			'expensive'      => 'Expensive',
			'very_expensive' => 'Very Expensive',
		]
		?>
		<div class="map-wrapper">
			<div class="map" id="map" style="width: 100%; height: 300px;"></div>
			<input type="hidden" name="_jwa_lat_location" id="_jwa_lat_location" value="<?php echo esc_attr( $lat ); ?>">
			<input type="hidden" name="_jwa_lng_location" id="_jwa_lng_location" value="<?php echo esc_attr( $lng ); ?>">
		</div>
		<div class="wp_event_manager_meta_data">
			<p class="form-field">
				<label for="_jwa_address">Address: </label>
				<input type="text" name="_jwa_address" id="_jwa_address" value="<?php echo esc_attr( $address ); ?>">
			</p>
			<p class="form-field">
				<label for="_jwa_phone">Phone: </label>
				<input type="text" name="_jwa_phone" id="_jwa_phone" value="<?php echo esc_attr( $phone ); ?>">
			</p>
			<p class="form-field">
				<label for="_jwa_fax">Fax: </label>
				<input type="text" name="_jwa_fax" id="_jwa_fax" value="<?php echo esc_attr( $fax ); ?>">
			</p>
			<p class="form-field">
				<label for="_jwa_web">Website: </label>
				<input type="text" name="_jwa_web" id="_jwa_web" value="<?php echo esc_attr( $web ); ?>">
			</p>
			<p class="form-field">
				<label for="_jwa_email">Email: </label>
				<input type="email" name="_jwa_email" id="_jwa_email" value="<?php echo esc_attr( $email ); ?>">
			</p>
			<p class="form-field">
				<label for="_jwa_location_type">Location Type: </label>
				<input type="text" name="_jwa_location_type" id="_jwa_location_type"
				       value="<?php echo esc_attr( $locationType ); ?>">
			</p>
			<div class="full-w" style="display: table; width: 100%;">
				<p style="text-align: left;">Open Hours:</p>
				<?php
				wp_editor( $openHours, '_jwa_open_hours', $settings = [ 'textarea_name' => '_jwa_open_hours' ] );
				?>
			</div>
			<p class="full-w" style="display: table; width: 100%;">
				<label for="_jwa_price_index">Price Index: </label>
				<select name="_jwa_price_index" id="_jwa_price_index">
					<?php foreach ( $indexPriceOptions as $key => $item ): ?>
						<option
							value="<?php echo $key; ?>" <?php echo $key == $indexPrice ? 'selected' : '' ?>><?php echo $item; ?></option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
		<?php
	}
	
	public function saveLocation ( $post_id ) {
		
		if ( ! isset( $_POST['jwaLocationMetaBoxNonce'] ) ) {
			return $post_id;
		}
		
		$nonce = $_POST['jwaLocationMetaBoxNonce'];
		if ( ! wp_verify_nonce( $nonce, 'jwaLocationMetaBox' ) ) {
			return $post_id;
		}
		
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}
		
		if ( 'location' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}
		
		$lat          = sanitize_text_field( $_POST['_jwa_lat_location'] );
		$lng          = sanitize_text_field( $_POST['_jwa_lng_location'] );
		$address      = sanitize_text_field( $_POST['_jwa_address'] );
		$phone        = sanitize_text_field( $_POST['_jwa_phone'] );
		$fax          = sanitize_text_field( $_POST['_jwa_fax'] );
		$web          = sanitize_text_field( $_POST['_jwa_web'] );
		$locationType = sanitize_text_field( $_POST['_jwa_location_type'] );
		$email        = sanitize_text_field( $_POST['_jwa_email'] );
		$openHours    = html_entity_decode( stripslashes( $_POST['_jwa_open_hours'] ), ENT_QUOTES, 'UTF-8' );
		$indexPrice   = sanitize_text_field( $_POST['_jwa_price_index'] );
		
		update_post_meta( $post_id, '_jwa_lat_location', $lat );
		update_post_meta( $post_id, '_jwa_lng_location', $lng );
		update_post_meta( $post_id, '_jwa_address', $address );
		update_post_meta( $post_id, '_jwa_phone', $phone );
		update_post_meta( $post_id, '_jwa_fax', $fax );
		update_post_meta( $post_id, '_jwa_web', $web );
		update_post_meta( $post_id, '_jwa_location_type', $locationType );
		update_post_meta( $post_id, '_jwa_email', $email );
		update_post_meta( $post_id, '_jwa_open_hours', $openHours );
		update_post_meta( $post_id, '_jwa_price_index', $indexPrice );
		
	}
	
}