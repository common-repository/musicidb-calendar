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
						$venue = $event->get_venue();

						$sets = $event->get_sets();
						$totalSets = !empty( $sets ) ? count( $sets ) : 0;
						?>

						<li id="event-<?php esc_attr_e( $event->get_id() ); ?>" class="rowView">
			
							<div class="rowColumn dateColumn">
								<h3 class="simpleTitle">
									<span><?php echo date('D', strtotime( $event_date )); ?></span>
									<span><?php echo date('M', strtotime( $event_date )); ?></span>
									<span><?php echo date('d', strtotime( $event_date )); ?></span>
									<span class="yearNum"><?php echo date('Y', strtotime( $event_date )); ?></span>
								</h3>         		

							</div>

							<?php if( 'show' === $show_venue ): ?>
								<div class="rowColumn venueColumn">
									<?php 
										$venue_address = $venue->get_address();
										$venue_formatted_address = '';
										$venue_formatted_city_state = '';

										if( !empty( $venue_address ) ) {

											$venue_street = $venue_address->get_address();
											$venue_city = $venue_address->get_city();
											$venue_state = $venue_address->get_state();

											$venue_formatted_city_state .= ( !empty( $venue_city ) ) ? $venue_city : '';
											$venue_formatted_city_state .= ( !empty( $venue_city && !empty( $venue_state ) ) ) ? ', ' : '';
											$venue_formatted_city_state .= ( !empty( $venue_state ) ) ? $venue_state : '';

											$venue_formatted_address .= ( !empty( $venue_street ) ) ? $venue_street : '';
											$venue_formatted_address .= ( !empty( $venue_formatted_city_state ) ) ? ', ' . $venue_formatted_city_state : '';

										}
									?>
									<h3 class="simpleTitle">
										<a href="https://www.musicindustrydatabase.com/venue/getVenueDetail.htm?venueId=<?php esc_attr_e( $venue->get_id() ); ?>" title="<?php esc_attr_e( $venue_formatted_address ); ?>" target="_blank">
											<?php esc_html_e( $venue->get_name() ); ?>
							        	</a>
							    	</h3>
								</div>
							<?php endif; ?>

							<?php if( !empty( $venue_formatted_city_state ) ): ?>
								<div class="rowColumn locationColumn">
									<h3 class="simpleTitle">
										<?php esc_html_e( $venue_formatted_city_state ); ?>
									</h3>
								</div>
							<?php endif; ?>

							<?php if( 'show' === $show_artist ): ?>
								
								<div class="rowColumn detailsColumn">

									<h3 class="simpleTitle">

										<?php if( !empty( $event->get_name() ) && $event->get_name() != '-' ): ?> 
											<a href="#/" title="View this Event" 
												class="showDetailsLink" 
												data-event-id="<?php esc_attr_e( $event->get_id() ); ?>"
											>
												<?php esc_html_e( $event->get_name() ); ?>
											</a>
										<?php endif; ?>

										<?php
											add_filter( 'musicidb_hover_cards_sets', function() use ($sets) { return $sets; } );
											musicidb_get_template_part( 'includes/partials/part', 'hover-cards' ); 
										?>
											
									</h3>

								</div>

							<?php endif; ?>	

							<div class="rowColumn buttonsColumn">

								<a href="#/" 
									class="simpleBtn showDetailsLink"
									data-event-id="<?php esc_attr_e( $event->get_id() ); ?>" 
									title="View Details" 
				                    >View Event</a>

								<?php if( !empty( $event->get_ticket_link() ) ): ?>

				                 	<a href="<?php echo esc_url( $event->get_ticket_link(), array( 'http', 'https' ) ); ?>" class="simpleBtn" target="_blank" title="More information at <?php echo esc_url( $event->get_ticket_link(), array( 'http', 'https' ) ); ?>">Tickets</a>
				                 	
				             	<?php endif; ?>
				             	
				             	<?php if( !empty( $event->get_facebook_link() ) ): ?>        
									
									<a href="<?php echo esc_url( $event->get_facebook_link(), array( 'http', 'https' ) ); ?>" class="simpleBtn btn fbLinkWide" title="RSVP on Facebook" target="_blank"><i class="fui-facebook"></i>RSVP</a>
								
								<?php endif; ?>

							</div>

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

		<li><input type="hidden" value="<?php echo $pages; ?>" class="totalPages" /></li>
		
		<?php
	elseif( $response->get_status() === 404 ):
		$pages = 1;
		?>

		<?php if(!empty($settings['musicidb_no_event_msg'])): ?>
			<li id="event-none" class="summaryContent listEvent listItem cf">
				<p><?php echo esc_html( $settings['musicidb_no_event_msg'] ); ?></p>
			</li>
		<?php endif; ?>

		<li><input type="hidden" value="<?php echo $pages; ?>" class="totalPages" /></li>
	
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