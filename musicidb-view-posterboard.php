<?php

use MusicIDB\Client\Api_Exception;
use MusicIDB\Client\MusicIDB_API;

	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
	
	if ( ! wp_verify_nonce( $_REQUEST['security'], 'musicidb-integration-nonce' ) )
    	wp_die( 'Security check failed' );

	$settings = get_option('musicidb_options');
	$response = array();

try {

	$args = array(
		'is_published' => true,
        'limit' => $resultsPerPage,
        'page' => $page,
        'is_delete' => false,
	);

	if( !empty( $entities['venue'] ) )
		$args['venues'] = $entities['venue'];

	if( !empty( $entities['artist'] ) )
		$args['artists'] = $entities['artist'];

	if($listType == 'upcoming'):
        $args['start_date_after'] = current_time( 'Y-m-d' );
        $args['order_direction'] = 'ASC';
	elseif($listType == 'past'):
		$args['start_date_before'] = current_time( 'Y-m-d' );
		$args['order_direction'] = 'DESC';
	endif;

    $response = MusicIDB_API::request( 'event', 'filter_query', $args );
	
	if( $response->get_status() === 200 ):
		$events = $response->get_events();
		$total = $response->get_total();

		$pages = ceil($total / $resultsPerPage);
		
		?>

		<?php if( !empty($events) ): ?>

			<?php foreach($events as $event): ?>

				<?php
					if( !empty( $event->get_date() ) ):
						$event_date = $event->get_date()->get_date();
						$event_door = $event->get_date()->get_door();

						$sets = $event->get_sets();
						$totalSets = !empty( $sets ) ? count( $sets ) : 0;
						?>

						<li id="event-<?php esc_attr_e( $event->get_id() ); ?>" class="summaryContent listEvent posterItem">
						
							<?php if( !empty( $event->get_more_info() ) ): ?>
								<div class="summaryToggle collapsed">
									<i class="fui-plus-circle" title="Expand description"></i>
								</div>
							<?php endif; ?>							

							<div class="eventPosterArea">
								<?php $ticket_link = !empty( $event->get_ticket_link() ) ? esc_url( $event->get_ticket_link(), array( 'http', 'https' ) ) : false; ?>

								<?php if( !empty( $ticket_link ) ): ?>
									<a href="<?php echo esc_url( $ticket_link, array( 'http', 'https' ) ); ?>" target="_blank" title="More information at <?php esc_attr_e( $ticket_link ); ?>">
								<?php endif; ?>

								<!-- If a Poster or Event Thumbnail Exists -->
								<?php if( !empty( $event->get_poster() ) || !empty( $event->get_thumbnail() ) ): ?>
										<?php 
											if( !empty( $event->get_poster() ) ) {
												
												$thumbnail_src = musicidb_get_image_at_size( $event->get_poster(), 'StandardPoster' );

											} elseif( !empty( $event->get_thumbnail() ) ) {

												$event_thumbnail = $event->get_thumbnail();

												if( stripos($event_thumbnail, '/event/logo/') === false ) {
				                                    
				                                    $thumbnail_src = musicidb_get_image_at_size( $event_thumbnail, 'StandardPoster' );

												} else {
				                                    
				                                    $thumbnail_src = musicidb_get_image_at_size( $event_thumbnail, 'MediumLarge' );

												}
											}
										?>

										<img src="<?php echo $thumbnail_src; ?>" class="eventImg" alt="<?php esc_attr_e( $event->get_name() ); ?>" />
								<!-- If there is no poster or thumbnail, and there are billed artists -->
								<?php elseif( !empty( $sets ) ): ?>
									<div class="bandPics<?php echo ($totalSets == 1) ? ' oneBand' : ''; ?>">

										<?php foreach( $sets as $set ): ?>
											<?php $artist = $set->get_artist(); ?>

											<?php if( !empty( $artist ) ): ?>
												<div class="bandPicBox">
													<?php if( !empty( $artist->get_image_url() ) ): ?>
														<img src="<?php echo musicidb_get_image_at_size( $artist->get_image_url(), 'MediumLarge' ); ?>" class="artistReplace" />
													<?php endif; ?>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>

									</div>

								<!-- If there is no poster or no thumbnail, and there are no billed artists -->
								<?php else: ?>
									<div>
										<img src="<?php echo esc_url( plugins_url('/images/defaultEvent.png', MUSICIDB_PLUGIN ), array( 'http', 'https' ) ); ?>" class="artistReplace defaultPic" />
									</div><!-- /.bandPicBox -->
								<?php endif; ?>

								<?php if( !empty( $ticket_link ) ): ?>
									</a>
								<?php endif; ?>
							</div>

							<div class="listingBody">
								<h3 class="date" title="<?php echo date('D M d, Y', strtotime( $event_date )); ?>">
									<span class="monthName dayNameAbb"><?php echo date('D', strtotime( $event_date )); ?></span>
									<span class="monthName"><?php echo date('M', strtotime( $event_date )); ?></span>
									<span class="dayNum"><?php echo date('d', strtotime( $event_date )); ?></span>
									<span class="yearNum"><?php echo date('Y', strtotime( $event_date )); ?></span>
								</h3>
								<h3 class="titleofevent">
									<a href="#/" class="showDetailsLink" data-event-id="<?php esc_attr_e( $event->get_id() ); ?>" title="View Details">
										<?php 
											if($event->get_name() != '-') { 
												esc_html_e( $event->get_name() ); 
											} 
										?>
									</a>

									<?php
										if( 'show' === $show_artist ) {
											add_filter( 'musicidb_hover_cards_sets', function() use ($sets) { return $sets; } );
											musicidb_get_template_part( 'includes/partials/part', 'hover-cards' ); 
										}
							        ?>

							        <?php if( 'show' === $show_venue ): ?>
									
									<?php $venue = $event->get_venue(); ?>
										<span class="atText">at</span>

										<a href="https://musicidb.com/venue/getVenueDetail.htm?venueId=<?php esc_attr_e( $venue->get_id() ); ?>" title="<?php esc_attr_e( $venue_formatted_address ); ?>" target="_blank" class="venueTitle">
											<?php esc_html_e( $venue->get_name() ); ?>
							        	</a>
							        	
									<?php endif; ?>

								</h3><!-- /.titleofevent -->

								<?php if( !empty( $event->get_subtitle() ) ): ?>
									<div class="h4 subtitleEvent"><?php esc_html_e( $event->get_subtitle() ); ?></div>	
								<?php endif; ?>

								<div class="eventInfoArea">
									<div class="eventInfoWide">
										<p class="eventLogistics">
											<?php 
												$showSep = false;
												if( !empty( $event_door ) ) {
													esc_html_e( $event_door ); 
													$showSep = true;
												}
											?> 
											
											<?php if( !empty( $event->get_ticket_price() ) ): ?>
												<?php if($showSep): ?><span class="sep">&bull;</span><?php endif; ?> Tickets: <?php esc_html_e($event->get_ticket_price() ); ?>
												<?php $showSep = true; ?>
											<?php endif; ?>

											<?php if( !empty( $event->get_door_cover() ) ): ?>
												<?php if($showSep): ?><span class="sep">&bull;</span><?php endif; ?> Door Cost: $<?php esc_html_e( $event->get_door_cover() ); ?>
												<?php $showSep = true; ?>
											<?php endif; ?>

											<?php if( !empty( $event->get_age_restriction() ) && $event->get_age_restriction() != 'Not Provided' ): ?>
												<?php if($showSep): ?><span class="sep">&bull;</span><?php endif; ?> <?php echo $event->get_age_restriction(); ?>
												<?php $showSep = true; ?>
											<?php endif; ?>
										</p>

										<?php /*if( !empty( $event->get_event_genres() ) ): ?>
											<?php 
												$event_genres = $event->get_event_genres(); 
												$genre_count = !empty( $event_genres ) ? count($event_genres) : 0;
												$count = 1;
											?>

											<p class="genresOnEvent shortMarginBottom">
												<!-- Genres: -->
												<?php foreach( $event_genres as $genre ): ?>
													<?php 
														echo ($count < $genre_count) ? esc_html( $genre . ', ' ) : esc_html( $genre ); 
														$count++;
													?>
												<?php endforeach; ?>
											</p>
										<?php endif; */ ?>

										<p class="marginBottom">
											<a href="#/" data-event-id="<?php esc_attr_e( $event->get_id() ); ?>" class="inconViewLink showDetailsLink" title="View Details">View Event</a>
										</p>

										<?php if( !empty( $event->get_more_info() ) ): ?>
											<div class="fullInfo" <?php if($descrip == false): ?>style="display: none;"<?php endif; ?>>
												<div class="eventDescription">
													<?php echo apply_filters( 'the_content', wp_kses_post( $event->get_more_info() ) ); ?>
												</div>
											</div><!-- /.fullInfo -->
										<?php endif; ?>
									</div><!-- /.eventInfoWide -->
								</div><!-- /.eventInfoArea -->
							</div><!-- /.listingBody -->

							<?php
								$className = "";

								if($buttons == 'left')
									$className = "moveLeft";
								elseif($buttons == 'right')
									$className = "moveRight";
								elseif($buttons == 'center')
									$className = "moveCenter";
							?>
							<ul class="controlbar <?php esc_attr_e( $className ); ?>">
								<?php if( $listType == 'upcoming' && !empty( $event->get_ticket_link() ) ): ?>
									<li>
										<a href="<?php echo esc_url( $event->get_ticket_link(), array( 'http', 'https' ) ); ?>" class="greenBtn TicketLink" target="_blank" title="More information at <?php esc_attr_e( $event->get_ticket_link() ); ?>">
										<span>Tickets</span>
										</a>
									</li>
								<?php endif; ?>

								<?php if( !empty( $event->get_media_count() ) && $event->get_media_count() > 0 ): ?>
									<li>
										<a href="#" class="orangeBtn mediaBtn showDetailsLink" data-event-id="<?php esc_attr_e( $event->get_id() ); ?>"><span><i class="icon-playvideo"></i>Media</span></a>
									</li>
								<?php endif; ?>

								<?php if( !empty( $event->get_facebook_link() ) ): ?>
									<li>
										<a href="<?php echo esc_url( $event->get_facebook_link(), array( 'http', 'https' ) ); ?>" class="ticketsLink btn fbLinkWide" title="RSVP on Facebook" target="_blank"><i class="fui-facebook"></i>RSVP</a>
									</li>
								<?php endif; ?>
							</ul><!-- /.controlbar -->
						</li>
						<?php 

					endif; 
				?>
			<?php endforeach; ?>
		<?php else: ?>
			<?php $pages = 1; ?>

			<li id="event-none" class="summaryContent listEvent listItem cf">
				<p><?php echo esc_attr($settings['musicidb_no_event_msg']); ?></p>
			</li>
		<?php endif; ?>

		<li class="listEvent totalPagesLi"><input type="hidden" value="<?php echo $pages; ?>" class="totalPages" /></li>
		
		<?php
	elseif( $response->get_status() === 404 ):
		$pages = 1;
		?>

		<?php if(!empty($settings['musicidb_no_event_msg'])): ?>
			<li id="event-none" class="summaryContent listEvent listItem cf">
				<p><?php echo esc_html( $settings['musicidb_no_event_msg'] ); ?></p>
			</li>
		<?php endif; ?>

		<li class="listEvent totalPagesLi"><input type="hidden" value="<?php echo $pages; ?>" class="totalPages" /></li>
	
		<?php
	endif;

} catch(Api_Exception $e) {
	error_log( $e->getMessage() );
?>

	<li>
		<p>
			Sorry we're having trouble loading the events right now&hellip;

			<?php if( defined( 'WP_DEBUG' ) && WP_DEBUG ): ?>
				<br />Encountered an error: <?php esc_html_e( $e->getMessage() ); ?>
			<?php endif; ?>
		</p>
	</li>

<?php
}