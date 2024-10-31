<?php 

	$sets = apply_filters( 'musicidb_hover_cards_sets', false ); 
	$totalSets = !empty( $sets ) ? count( $sets ) : 0;
	
?>

<?php if( !empty( $sets ) ): ?>
	<?php 
		$counter = 1;
		$processed_artists = array(); 
	?>

	<?php foreach( $sets as $set ): ?>
		<?php 
			$artist = $set->get_artist();

			if( in_array( $artist->get_id(), $processed_artists ) ) {
				continue;
			}

			$processed_artists[] = $artist->get_id(); 
		?>

		<div class="cardFloater">
			<div class="linkTileHolder thumbTileHolder">
				<div class="artistInfoTile">
					<div class="thumbTile">
						<div class="frame">

							<?php if( !empty( $artist->get_image_url() ) ): ?>
								<img src="<?php echo musicidb_get_image_at_size($artist->get_image_url(), 'MediumLarge'); ?>" class="artistReplace alignleft" alt="<?php esc_attr_e( $artist->get_name() ); ?>">
							<?php else: ?>
								<img src="<?php echo esc_url( plugins_url('/images/defaultArtist.jpg', MUSICIDB_PLUGIN), array( 'http', 'https' ) ); ?>" alt="<?php esc_attr_e( $artist->get_name() ); ?>" />
							<?php endif; ?>

						</div><!-- /.frame -->

						<div class="tileData">
							<ul class="socialBubbleIcons">
								
								<li>
									<a href="https://musicidb.com/artist/artistDetail.htm?artistId=<?php esc_attr_e( $artist->get_id() ); ?>" class="musicidbLink" title="Visit <?php esc_attr_e( $artist->get_name() ); ?> on MusicIDB" target="_blank"><img src="//musicidb.com/resources/images/social/MusicIDB-social-icon.jpg" alt="Visit <?php esc_attr_e( $artist->get_name() ); ?> on MusicIDB.com" title="Visit <?php esc_attr_e( $artist->get_name() ); ?> on MusicIDB.com" />
									</a>
								</li>

								<?php if( !empty( $artist->get_links() ) ): ?>

									<?php $links = $artist->get_links(); ?>
									<?php foreach($links as $link): ?>
										<?php $link_details = musicidb_get_link_details( $link->get_name() ); ?>

										<li>
											<a href="<?php echo esc_url( $link->get_link(), array( 'http', 'https' ) ); ?>" class="<?php esc_attr_e( $link_details['class'] ); ?>" title="Visit <?php esc_attr_e( $artist->get_name() ); ?> on <?php esc_attr_e( $link_details['place'] ); ?>" target="_blank"></a>
										</li>
									<?php endforeach; ?>

								<?php endif; ?>

								
							</ul><!-- /.socialBubbleIcons -->

							<h3><?php esc_html_e( $artist->get_name() ); ?></h3>

							<?php 
								$artist_location = $artist->get_location(); 

								if( !empty( $artist_location ) ):
									?>
									 <p>
	                                    <?php if( $artist_location->get_city() ): ?>
	                                        <?php esc_html_e( $artist_location->get_city() ); ?>, <?php esc_html_e( $artist_location->get_state() ); ?>
	                                    <?php else: ?>
	                                        <?php esc_html_e( $artist_location->get_state() ); ?>
	                                    <?php endif; ?>
		                            </p>
		                            <?php
		                        endif; 

		                    ?>

                            <?php 
								$artist_genres = $artist->get_genres(); 
								$genre_count = !empty( $artist_genres ) ? count($artist_genres) : 0;
								$count = 1;
							?>

							<?php if( !empty( $artist_genres ) ): ?>
								<p>
									<?php foreach( $artist_genres as $genre ): ?>
										<?php 
											echo ($count < $genre_count) ? esc_html( $genre . ', ' ) : esc_html( $genre ); 
											$count++;
										?>
									<?php endforeach; ?>
								</p>
							<?php endif; ?>
						</div><!-- /.tileData -->
					</div><!-- /.thumbTile -->
				</div><!-- /.artistInfoTile -->
			</div><!-- /.linkTileHolder -->

			<span class="hoverArtist"><?php esc_html_e( $artist->get_name() ); ?><?php if($counter < $totalSets) { echo ', '; } ?></span>
		</div><!-- /.cardFloater -->

		<?php $counter++; ?>
	<?php endforeach; ?>
<?php endif; ?>