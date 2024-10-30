<?php
/**
 * Created 15.06.2020
 * Version 1.0.0
 * Last update
 * Author: Alex L
 *
 */

get_header();


?>
	<section>
		<div class="location-page">
			<div class="container">
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 dfr">
						<div class="breadcrumbs"><?php
							if ( function_exists( 'bcn_display' ) ) {
								bcn_display( $return = false, $linked = true, $reverse = false, $force = false );
							}
							?></div>
						<?php if ( have_posts() ): ?>
							<div class="content">
								<?php while ( have_posts() ): the_post(); ?>
									<h1><?php the_title(); ?></h1>
									<?php
									$id           = get_the_ID();
									$address      = get_post_meta( $id, '_jwa_address', true );
									$lat          = get_post_meta( $id, '_jwa_lat_location', true );
									$lng          = get_post_meta( $id, '_jwa_lng_location', true );
									$address      = get_post_meta( $id, '_jwa_address', true );
									$phone        = get_post_meta( $id, '_jwa_phone', true );
									$fax          = get_post_meta( $id, '_jwa_fax', true );
									$web          = get_post_meta( $id, '_jwa_web', true );
									$locationType = get_post_meta( $id, '_jwa_location_type', true );
									$email        = get_post_meta( $id, '_jwa_email', true );
									$openHours    = get_post_meta( $id, '_jwa_open_hours', true );
									$indexPrice   = get_post_meta( $id, '_jwa_price_index', true );
									
									$shordcode = get_option( 'addthis_shord_code' );
									$sidebar   = get_option( 'sidebar_single_location' );
									?>
									<input type="hidden" name="_jwa_lat_location" id="_jwa_lat_location"
									       value="<?php echo esc_attr( $lat ); ?>">
									<input type="hidden" name="_jwa_lng_location" id="_jwa_lng_location"
									       value="<?php echo esc_attr( $lng ); ?>">
									<p><?php echo $address ?></p>
									<?php echo do_shortcode( $shordcode ); ?>
									<div id="map"></div>
									<?php the_content(); ?>
									<hr class="sline">
									<ul class="contacts">
										<?php if ( $address ): ?>
											<li>Address<a
													href="https://maps.google.com/maps?saddr=&daddr=(<?php echo $lat; ?>, <?php echo $lng; ?>)
" target="_blank" rel="nofollow"><?php echo
													$address ?></a>
											</li>
										<?php endif; ?>
										
										<?php if ( $phone ): ?>
											<li>Phone<a href="tel:<?php echo $phone; ?>"><?php echo $phone; ?></a></li>
										<?php endif; ?>
										<?php if ( $fax ): ?>
											<li>Fax
												<p><?php echo $fax; ?></p>
											</li>
										<?php endif; ?>
										<?php if ( $email ): ?>
											<li>Email<a href="mailto:<?php echo $email; ?>"><?php echo $email; ?></a></li>
										<?php endif; ?>
										<?php if ( $locationType ): ?>
											<li>All Locations
												<p><?php echo $locationType; ?></p>
											</li>
										<?php endif; ?>
										<?php if ( $web ): ?>
											<li>Website<a class="icon-" href="<?php echo $web; ?>" rel="nofollow"><?php echo $web; ?></a></li>
										<?php endif; ?>
									</ul>
									<?php if ( $openHours ): ?>
										<div class="open-hours">
											<h5>Open Hours</h5>
											<?php echo $openHours; ?>
										</div>
									<?php endif; ?>
								<?php endwhile; ?>
								<hr class="sline">
								<?php comments_template(); ?>
							</div>
						<?php endif; ?>
						<div class="sidebar"><?php dynamic_sidebar( $sidebar ); ?></div>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php
get_footer();
