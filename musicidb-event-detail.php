<?php

use MusicIDB\Client\Api_Exception;
use MusicIDB\Client\MusicIDB_API;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! wp_verify_nonce( $_REQUEST['security'], 'musicidb-integration-nonce' ) )
	wp_die( 'Security check failed' );

if($eventId) :
	$settings = get_option('musicidb_options');
	
	try {
		$eventResponse = MusicIDB_API::request( 'event', 'get_event', array(
			'id' => $eventId
		) );

		$eventArtistMediaResponse = MusicIDB_API::request( 'event', 'get_event_artist_media', array(
			'id' => $eventId
		) );

		?>
		
		<div id="event-details-popup" class="eventDetailsPopup">

			<a href="#" class="modalClose" title="Cancel"><i class="fui-cross"></i></a>

			<div class="modalInner">

				<?php

				if( 200 === $eventResponse->get_status() ):

					$events = $eventResponse->get_events();

					if( !empty( $events ) ): 
						
						$event = $events[0];
						$eventArtistMedia = $eventArtistMediaResponse->get_artist_media();

						if( $event->get_date() ) {
							$event_date = (property_exists( $event->get_date() , 'date')) ?  $event->get_date()->get_date() : '';
							$event_door = (property_exists( $event->get_date() , 'door')) ?  $event->get_date()->get_door() : '';
						} else {
							$event_date = '';
							$event_door = '';
						} ?>

<article id="event-<?php esc_attr_e( $event->get_id() ); ?>" class="myEventItem">

	<div class="eventTopDetails">
		<!-- Event Date and Name -->
		<h2 class="date shortMarginBottom">
			<span class="monthName dayNameAbb"><?php echo date('D', strtotime($event_date)); ?></span>
			<span class="monthName"><?php echo date('M', strtotime($event_date)); ?></span>
			<span class="dayNum"><?php echo date('d', strtotime($event_date)); ?></span>
			<span class="yearNum"><?php echo date('Y', strtotime($event_date)); ?></span>
        </h2>

		<h2 class="eventTitleandArtists">
			<a href="//musicidb.com/event/eventDetail.htm?eventId=<?php esc_attr_e( $event->get_id() ); ?>" target="_blank">
				<?php if($event->get_name() != "-"): ?>
                	<?php esc_html_e( $event->get_name() ); ?>
                <?php endif; ?>
			</a>

			<!-- Artist Names -->
			<?php if( !empty( $eventArtistMedia ) ): ?>
				<?php $counter = 1; ?>

				<span class="artistsInfo">
					<?php foreach( $eventArtistMedia as $artist ): ?>
						<span><?php echo ( $counter < count( $eventArtistMedia ) ) ? esc_html( $artist->get_name() ) . ', ' : esc_html( $artist->get_name() ); ?></span>
						<?php $counter++; ?>
					<?php endforeach; ?>
				</span>
			<?php endif; ?>
		</h2>

		<?php if( 'show' == $show_venue ): ?>									
			<?php $venue = $event->get_venue(); ?>
			<p>
				at <a href="https://musicidb.com/venue/getVenueDetail.htm?venueId=<?php esc_attr_e( $venue->get_id() ); ?>" title="<?php esc_attr_e( $venue_formatted_address ); ?>" target="_blank">
					<?php esc_html_e( $venue->get_name() ); ?>
	        	</a>
        	</p>						        	
		<?php endif; ?>

		<?php if( $event_door || $event->get_door_cover() || ($event->get_age_restriction() && $event->get_age_restriction() != 'Not Provided') ): ?>
			<!-- Event Logistics -->
			<p class="eventLogistics">
		        <?php if($event_door): ?>
		        	<?php esc_html_e( $event_door ); ?>
		        <?php endif; ?>
		        <?php if( $event->get_door_cover() ): ?>
		        	<?php if( $event_door ): ?> <span class="sep">&bull;</span> <?php endif; ?>
		        	<?php if( $event->get_door_cover() == '0' ): ?>Free
		        	<?php else: ?>
		        		$<?php esc_html_e( $event->get_door_cover() ); ?>
		        	<?php endif; ?>
		        <?php endif; ?>
		        <?php if($event->get_age_restriction() && $event->get_age_restriction() != 'Not Provided'): ?>
			        <?php if($event_door || $event->get_door_cover()): ?> <span class="sep">&bull;</span> <?php endif; ?>
			        <?php esc_html_e( $event->get_age_restriction() ); ?>
		    	<?php endif; ?>
		    </p>
		<?php endif; ?>

	</div><!-- /.eventTopDetails -->

    <!-- Artist Cards -->
    <?php if( !empty( $eventArtistMedia ) ): ?>
		<div class="cardsSlideWrap desktopLayout">
			<div id="artistCardsSlider">
				<ul class="slides cf">
					<?php foreach( $eventArtistMedia as $eventArtist ): ?>
						<li>
							<?php 
								$media = $eventArtist->get_media();

								if( 
									$media->get_videos() 
									|| $media->get_audio() 
									|| property_exists($eventArtist, 'bio') && $eventArtist->get_bio() 
								) {
									$artistCardClass = "cardFloater";
								} else {
									$artistCardClass = "cardFloater noMedia"; 
								}
							?>

							<div class="<?php esc_attr_e( $artistCardClass ); ?>">
								<div class="linkTileHolder">
					                <div class="artistInfoTile">
					                    <div class="thumbTile">
					                        <div class="frame">
					                            <?php if( $eventArtist->get_image_url() ): ?>
													<img src="<?php echo musicidb_get_image_at_size( $eventArtist->get_image_url(), 'MediumLarge' ); ?>" alt="<?php esc_attr_e( $eventArtist->get_name() ); ?>" class="artistReplace alignleft">
					                            <?php else: ?>
					                            	<img src="<?php echo esc_url( plugins_url('/images/defaultArtist.jpg', MUSICIDB_PLUGIN ), array( 'http', 'https' ) ); ?>" alt="<?php esc_attr_e( $eventArtist->get_name() ); ?>" class="defaultImage" />
					                            <?php endif; ?>
					                        </div><!-- /.frame -->
					                        
					                        <div class="tileData">
					                            <ul class="socialBubbleIcons desktopLayout">
													
					                            	<li>
														<a href="https://musicidb.com/artist/artistDetail.htm?artistId=<?php esc_attr_e( $eventArtist->get_id() ); ?>" class="musicidbLink" title="Visit <?php esc_attr_e ( $eventArtist->get_name() ); ?> on MusicIDB" target="_blank"><img src="//musicidb.com/resources/images/social/MusicIDB-social-icon.jpg" alt="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on MusicIDB.com" title="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on MusicIDB.com" />
														</a>
													</li>

													<?php if( !empty( $eventArtist->get_links() ) ): ?>
														<?php $links = $eventArtist->get_links(); ?>
														<?php foreach($links as $link): ?>
															<?php 
																$link_details = musicidb_get_link_details( $link->get_name() ); 
															?>

															<li>
																<a href="<?php echo esc_url( $link->get_link(), array( 'http', 'https' ) ); ?>" class="<?php esc_attr_e( $link_details['class'] ); ?>" title="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on <?php esc_attr_e( $link_details['place'] ); ?>" target="_blank"></a>
															</li>
														<?php endforeach; ?>

													<?php endif; ?>

													
			                                    </ul><!-- /.socialBubbleIcons -->

			                                    <h3><?php esc_html_e( $eventArtist->get_name() ); ?></h3>

			                                    <?php if( $eventArtist->get_location()->get_city() || $eventArtist->get_location()->get_state() ): ?>
				                                    <p>
						                                <?php if( $eventArtist->get_location()->get_city() ): ?>
						                                	<?php esc_html_e( $eventArtist->get_location()->get_city() ); ?>, <?php esc_html_e( $eventArtist->get_location()->get_state() ); ?>
						                                <?php else: ?>
						                                    <?php esc_html_e( $eventArtist->get_location()->get_state() ); ?>
						                                <?php endif; ?>
						                            </p>
						                        <?php endif; ?>
					                            
					                            <?php if( $eventArtist->get_genres() ): ?>
					                            	<p>
					                            		<?php foreach( $eventArtist->get_genres() as $eventArtistGenre ): ?>
															<?php esc_html_e( $eventArtistGenre ); ?>
														<?php endforeach; ?>
					                            	</p>
					                            <?php endif; ?>
					                        </div><!--  /.tileData -->
					                    </div><!-- /.thumbTile -->
					                </div><!-- /.artistInfoTile -->
					            </div><!-- /.linkTileHolder -->
					        </div><!-- /.cardFloater -->

							
							<?php 
							// We did find artist media ********************
							if( !empty( $media ) ): ?>
								<?php 
									$artistAudioCount = !empty( $media->get_audio() ) ? count( $media->get_audio() ) : 0;
									$artistVideoCount = !empty( $media->get_videos() ) ? count( $media->get_videos() ) : 0;
									$artistEventCount = !empty( $eventArtist->get_event_count() ) ? $eventArtist->get_event_count() : 0;

									$embedCodes = musicidb_get_embed_codes( $media );

									$noMedia = ( $artistAudioCount > 0 || $artistVideoCount > 0 ) ? '' : 'noMedia';
								?>

								<div class="mediaHolder mediaOne <?php esc_attr_e( $noMedia ); ?>">
									<div class="artistMediaTabs cf">

										<?php if( !empty( $embedCodes ) ): ?>
											<?php $counter = 0; ?>

											<?php foreach( $embedCodes as $embedCode ): ?>
												<div class="embed-<?php esc_attr_e( $eventArtist->get_id() ); ?>-<?php esc_attr_e( $counter ); ?> artistMediaTab cf <?php echo ($counter == 0) ? 'current' : ''; ?>">

													<?php 
													// TODO: How do we handle fb embeds gracefully?
													echo wp_kses( $embedCode['embed_code'], musicidb_get_allowed_embed_tags() ); ?>
												</div><!-- embed-## -->

												<?php $counter++; ?>
											<?php endforeach; ?>
										<?php endif; ?>

										<?php 
											if(property_exists($eventArtist, 'bio') && $eventArtist->get_bio()): 
												$otherTabClasses = ($embedCodes) ? "artistMediaTab cf" : "artistMediaTab cf current";
										?>
											<div class="artistBio <?php echo $otherTabClasses; ?>">
												<div class="bioWrapper">
													<?php echo $eventArtist->get_bio(); ?>
												</div><!-- /.bioWrapper -->
											</div><!-- /.artistBio -->

										<?php endif; ?>

									</div><!-- /.artistMediaTabs -->
									
									<span class="playNowText">Play Now</span>

									<?php if( !empty( $embedCodes ) ): ?>
										<ul class="mediaTabNav">
											<?php $counter = 0; ?>

											<?php foreach( $embedCodes as $embedCode ): ?>
												<li class="<?php echo ($counter == 0) ? 'current' : ''; ?>">
													<a href="#embed-<?php esc_attr_e( $eventArtist->get_id() ); ?>-<?php esc_attr_e( $counter ); ?>" title="View Artist Media">

													<?php if( 'audio' == $embedCode['type'] ): ?>
															<i class="icon-music"></i></a>
													<?php elseif( 'video' == $embedCode['type'] ): ?>
														<i class="icon-playvideo"></i></a>
													<?php endif; ?>
												</li>

												<?php $counter++; ?>
											<?php endforeach; ?>

											<?php if( !empty( $eventArtist->get_bio() ) ): ?>
												<li <?php echo empty( $embedCodes ) ? 'class="current"' : ''; ?>>
													<a href="#artistBio" title="View Artist Bio">
														<i class="icon-microphone"></i>
													</a>
												</li>
											<?php endif; ?>
										</ul>

										<div class="eventArtistStats">
											
											<?php if( $artistVideoCount > 0 ): ?>
												<span class="videoStat stat" title="Videos on their page"><?php esc_html_e( $artistVideoCount ); ?> <i class="icon-playvideo"></i></span>
											<?php endif; ?>

											<?php if( $artistAudioCount > 0 ): ?>
												<span class="audioStat stat" title="Embedded audio files on their page"><?php esc_html_e( $artistAudioCount ); ?> <i class="icon-music"></i></span>
											<?php endif; ?>
											
											<?php if( !empty( $artistEventCount ) && $artistEventCount > 0): ?>
												<span class="eventStat stat" title="Events this artist has been tagged on"><?php esc_html_e( $artistEventCount ); ?> <i class="icon-calendaralt-cronjobs"></i></span>
											<?php endif; ?>

										</div><!-- /.artistStats -->
									<?php endif; ?>
								</div><!-- /.mediaHolder -->
							<?php endif; ?>

						</li>
					<?php endforeach; ?>
				</ul>
			</div><!-- /#artistCardsSlider -->
		</div><!-- /#cardsSlideWrap -->

		<div id="artistCardsList" class="mobileLayout">

			<h2>Artists</h2>
			<ul class="artistsList cf">
			<?php foreach( $eventArtistMedia as $eventArtist ): ?>
				<li>
					
					<?php 
						$media = $eventArtist->get_media();

						if( 									
								!empty( $media->get_videos() ) 
								|| !empty( $media->get_audio() )
						) {
							$artistCardClass = "cardFloater";

							if( !empty( $media->get_audio() ) ) {
								$audio = $media->get_audio();
								$artistAudioCount = count($audio);
							}

							if( !empty( $media->get_videos() ) ) {
								$videos = $media->get_videos();
								$artistVideoCount = count($videos);
							}
						} else {
							$artistCardClass = "cardFloater noMedia"; 
						}
					?>

					<div class="<?php esc_attr_e( $artistCardClass ); ?>">
						<div class="linkTileHolder">
			                <div class="artistInfoTile">
			                    <div class="thumbTile">
			                        <div class="frame">
			                            <?php if( !empty( $eventArtist->get_image_url() ) ): ?>
											<img src="<?php echo musicidb_get_image_at_size($eventArtist->get_image_url(), 'MediumLarge'); ?>" class="artistReplace alignleft">
			                            <?php else: ?>
			                            	<img src="<?php echo esc_url( plugins_url('/images/defaultArtist.jpg', MUSICIDB_PLUGIN), array( 'http', 'https' ) ); ?>" alt="<?php esc_attr_e( $eventArtist->get_name() ); ?>" class="defaultImage" />
			                            <?php endif; ?>
			                        </div><!-- /.frame -->
			                        								                        
			                        <div class="tileData">
			                            <ul class="socialBubbleIcons mobileLayout">

	                                        <li>
			                            		<a href="//musicidb.com/artist/artistDetail.htm?artistId=<?php esc_attr_e( $eventArtist->get_id() ); ?>" class="musicidbLink"
	                                                    title="" target="_blank"><img src="//musicidb.com/resources/images/social/MusicIDB-social-icon.jpg" alt="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on MusicIDB.com" title="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on MusicIDB.com">
	                                            </a>
	                                        </li>

	                                        <?php if( !empty( $eventArtist->get_links() ) ): ?>

												<?php $links = $eventArtist->get_links(); ?>
												<?php foreach($links as $link): ?>
													<?php 
														$link_details = musicidb_get_link_details( $link->get_name() ); 
													?>

													<li>
														<a href="<?php echo esc_url( $link->get_link(), array( 'http', 'https' ) ); ?>" class="<?php esc_attr_e( $link_details['class'] ); ?>" title="Visit <?php esc_attr_e( $eventArtist->get_name() ); ?> on <?php esc_attr_e( $link_details['place'] ); ?>" target="_blank"></a>
													</li>
												<?php endforeach; ?>

											<?php endif; ?>
	                                    </ul><!-- /.socialBubbleIcons -->

	                                    <h3><?php esc_html_e( $eventArtist->get_name() ); ?></h3>

	                                    <?php if( $eventArtist->get_location()->get_city() || $eventArtist->get_location()->get_state() ): ?>

		                                    <p>
				                                <?php if( $eventArtist->get_location()->get_city() ): ?>
				                                	<?php esc_html_e( $eventArtist->get_location()->get_city() ); ?>, <?php esc_html_e( $eventArtist->get_location()->get_state() ); ?>
				                                <?php else: ?>
				                                    <?php esc_html_e( $eventArtist->get_location()->get_state() ); ?>
				                                <?php endif; ?>
				                            </p>
				                        <?php endif; ?>
			                            
			                            <?php if( $eventArtist->get_genres() ): ?>
			                            	<p>
			                            		<?php foreach( $eventArtist->get_genres() as $eventArtistGenre ): ?>
													<?php esc_html_e( $eventArtistGenre ); ?>
												<?php endforeach; ?>
			                            	</p>
			                            <?php endif; ?>
			                        </div><!--  /.tileData -->
			                    </div><!-- /.thumbTile -->
			                </div><!-- /.artistInfoTile -->
			            </div><!-- /.linkTileHolder -->

				        <div class="eventArtistStats">
			        		<span class="videoStat stat" title="Number of videos attached to this artist listing"><?php esc_html_e( $artistVideoCount ); ?> <i class="icon-playvideo"></i></span>
				        	<span class="audioStat stat" title="Number of embedded audio files attached to this artist listing (from Bandcamp, SoundCloud or other sources)"><?php esc_html_e( $artistAudioCount ); ?> <i class="icon-music"></i></span>
				        	<span class="eventStat stat" title="Number of events this artist has been tagged on"><?php esc_html_e( $artistEventCount ); ?> <i class="icon-calendaralt-cronjobs"></i></span>
				        </div><!-- /.artistStats -->
			        </div><!-- /.cardFloater -->

					<?php 
					if( !empty( $media->get_videos() ) || !empty( $media->get_audio() ) ): ?>
						<?php $embedCodes = musicidb_get_embed_codes( $media ); ?>

				        <div class="mediaHolder mediaTwo">

		        			<?php if( !empty( $embedCodes ) ): ?>
		        				<?php $counter = 0; ?>

		        				<?php foreach( $embedCodes as $embedCode ): ?>

		        					<h3 class="artistMediaExpander">
				        				<?php if( 'audio' == $embedCode['type'] ): ?>
				        					<i class="leftIcon icon-music"></i>Music
					        			<?php elseif( 'video' == $embedCode['type'] ): ?>
				        					<i class="leftIcon icon-playvideo"></i>Videos
					        			<?php endif; ?>

										<i class="expandCollapseIcon icon-chevron-down"></i>
									</h3>

			        				<div class="artistMediaSection cf">
				        				<?php echo wp_kses( $embedCode['embed_code'], musicidb_get_allowed_embed_tags() ); ?>
				        			</div>

				        		<?php endforeach; ?>
							<?php endif; ?>

				        </div><!-- /.mediaHolder -->
				    <?php endif; ?>
				</li>
			<?php endforeach; ?>
			</ul><!-- /.artistsList -->
		</div><!-- /#artistCardsList -->
	<?php endif; ?>

	<?php if($event->get_ticket_link() || $event->get_facebook_link()): ?>
		<div class="modalTickets">
          	<ul class="controlbar cf">
          		<?php if($event->get_ticket_link()): ?>
        	        <li>
        	           	<a href="<?php echo esc_url( $event->get_ticket_link(), array( 'http', 'https' ) ); ?>" class="greenBtn TicketLink" target="_blank" title="More information at <?php esc_attr_e( $event->get_ticket_link() ); ?>"> 
        	            	<span>Tickets</span>
        	        	</a>
        	        </li>
    	        <?php endif; ?>

    	        <?php if($event->get_facebook_link()): ?>
					<li>
						<a href="<?php echo esc_url( $event->get_facebook_link(), array( 'http', 'https' ) ); ?>" class="ticketsLink btn fbLinkWide" title="RSVP on Facebook" target="_blank"><i class="fui-facebook"></i>RSVP</a>
					</li>
				<?php endif; ?>

				<!--
					TODO: import gcal link format function
				<li>
					
					<c:set var = "gCalURL" 
					value="https://calendar.google.com/calendar/render?action=TEMPLATE&text=" />
					<c:set var = "gstring" value="" />
					<c:if test="${ event.eventName == '-'}"></c:if>
	                <c:if test="${ event.eventName != '-'}">
	            		<c:set var = "gstring" value="${ event.eventName }" />
	            	</c:if>
					
					<c:if test="${not empty event.eventArtist}">											
							<c:forEach items="${event.eventArtist}" var="eventArtist" varStatus="loop">
								<c:set var = "gstring" 
									value="${ gstring } ${eventArtist.artistName}" /><c:if test="${!loop.last}"><c:set var = "gstring" value="${gstring},"/>
								</c:if>
							</c:forEach>
							<%-- <c:if test="${!loop.last}">, </c:if> --%>
					</c:if>

					<c:set var = "gstring" value="${ gstring }" />
						
					
					<c:if test="${not empty event.venue.venueName}">
						<c:set var = "gstring" value="${ gstring } at ${event.venue.venueName} " />
					</c:if>
					
					<script type="text/javascript"> 
							createGCalStringUrl ("${gCalURL}"+encodeURIComponent('<c:out value="${gstring}"/>'),"${event.endDate}","${event.endDate}","${event.doorTime}","${event.endTime}","googleCalenderUrl");
					</script>
					<a  class="btn googleCalBtn" title="Add to Your GCal" target="_blank" id="googleCalenderUrl"><i class="fui-google"></i>+ to Gcal</a>
				</li>
				-->

          	</ul>
		</div><!-- /.modalTickets -->
	<?php endif; ?>

	<div class="eventDescription">
		<?php if( $event->get_poster() ): ?>
			<div style="text-align: center; margin: 0 auto;" class="popupPosterWrap">
				<img src="<?php echo musicidb_get_image_at_size($event->get_poster(), 'LargePoster'); ?>" alt="<?php esc_attr_e( $event->get_name() ); ?> Poster" class="largePoster" />
			</div>
		<?php elseif( $event->get_thumbnail() ): ?>
			<div style="text-align: center; margin: 0 auto;" class="popupPosterWrap">
				<img src="<?php echo musicidb_get_image_at_size( $event->get_thumbnail(), 'Large' ); ?>" class="largePoster" alt="<?php esc_attr_e( $event->get_name() ); ?> Poster" >
			</div>
		<?php endif; ?>				

		<?php if( $event->get_more_info() ): ?>
    		<?php echo wp_kses_post( $event->get_more_info() ); ?>
    	<?php endif; ?>
	</div>

	<?php if($event->get_id()): ?>
    	<div class="centerThisGuy modalFootLinkers">
	    	<a href="//musicidb.com/event/eventDetail.htm?eventId=<?php esc_attr_e( $event->get_id() ); ?>" class="inconViewLink" title="View this Event on MusicIDB.com" target="_blank">
	    	View on MusicIDB</a>
	    </div>
    <?php endif; ?>

</article><!-- /#event-## -->
							
					<?php 
					endif;
				
				else:
					
					error_log( 'ERROR: MusicIDB API - Bad Response. Status: ' . $eventResponse->get_status() . ' Message: ' . $eventResponse->get_message() );
					?>

					<article id="event-<?php esc_attr_e(  $eventId ); ?>" class="myEventItem">
						<div class="eventTopDetails">
							<p>
								Sorry, we're having trouble loading this event right now&hellip;

								<?php if( defined( 'WP_DEBUG' ) && WP_DEBUG ): ?>
									<br />Encountered an error: <?php esc_html_e( $response->get_message() ); ?>
									<br />Status Code: <?php esc_html_e( $response->get_status() ); ?>
								<?php endif; ?>
							</p>
						</div>
					</article>

				<?php endif; ?>

			</div><!-- /.modalInner -->
		</div><!-- /#event-details-popup -->

		<?php

	} catch( Api_Exception $e ) {

		error_log( $e->getMessage() );

		if( defined( 'WP_DEBUG' ) && WP_DEBUG )
			esc_html_e( $e->getMessage() );

	}

else :
		?>
		
		<div id="event-details-popup" class="eventDetailsPopup">
			<a href="#" class="modalClose" title="Cancel"><i class="fui-cross"></i></a>

			<div class="modalInner">
				<article id="event-<?php esc_attr_e( $eventId ); ?>" class="myEventItem">
					<div class="eventTopDetails">
						<p>Sorry, we're having trouble loading this event&hellip;</p>
					</div>
				</article>
			</div>
		</div>

		<?php
endif;