<?php if($connected) : ?>

	<?php if( is_admin() ): // handle page builders like Divi and Elementor by stopping render in admin pages ?>
		<p>Admin Preview is not available for MusicIDB Calendar. Please preview the page to view the calendar.</p>
		<?php return; ?>
	<?php endif; ?>

<?php
		//Set/Validate shortcode atts
		
		$musicidb_plugin = MusicIDBIntegration::get_instance();

		$supportsCalView = !empty( $entities['venue'] ) && count( $entities['venue'] ) == 1;
		$themeAtt = $musicidb_plugin->musicidb_strip_unicode( $atts['theme'] );
		$theme = ($themeAtt == 'dark') ? 'blackBack' : 'lightBack';
		$descrip = ($atts['descrip'] && $atts['descrip'] == 'true' ) ? 1 : 0;
		$view = ( $atts['view'] && $atts['view'] == 'cal' && $supportsCalView ) ? 'cal' : 'list';
		$display = ($atts['display'] && $atts['display'] == 'img') ? 'img' : 'text';
		$buttons = ($atts['buttons'] && is_string($atts['buttons'])) ? $musicidb_plugin->musicidb_strip_unicode( $atts['buttons'] ) : 'left';
		$entity_id = !empty( $atts['id'] ) ? $musicidb_plugin->musicidb_strip_unicode( $atts['id'] ) : $musicidb_plugin->musicidb_strip_unicode( $default_id );
		$entity_type = !empty( $atts['type'] ) ? $musicidb_plugin->musicidb_strip_unicode( $atts['type'] ) : '';
		$list_style = !empty( $atts['style'] ) ? musicidb_map_list_style( $musicidb_plugin->musicidb_strip_unicode( $atts['style'] ) ) : $default_style;
		$large_pics = ((!empty( $atts['largepics'] ) && $atts['largepics'] === 'true') || $list_style == 'largepics') ? true : false;

		$show_venue = $atts['showvenue'];

		if( empty( $show_venue ) ) {
			$show_venue = ( 'compact' == $list_style ) ? 'show' : 'hide';
		}

		$show_artist = $atts['showartist'];

		if( empty( $show_artist ) ) {
			$show_artist = 'show';
		}

		if($buttons != 'left' && $buttons != 'right' && $buttons != 'center')
			$buttons = 'left';

		$style_class = '';
		switch( $list_style ) {
			case 'full':
			case 'largepics':
				$style_class = 'viewListPics';
				break;
			case 'compact':
				$style_class = 'viewCompact';
				break;
			case 'posterboard':
				$style_class = 'viewPosters';
				break;
			default:
				$style_class = '';		
				break;
		}
	?>

<div id="musicidb-events-integration" class="musicidb-events-integration <?php echo $theme; ?> cf">
	<div id="venue-event-list" class="inside cf">
		<input type="hidden" value="<?php echo esc_attr($theme); ?>" id="themeParam" />
		<input type="hidden" value="<?php echo esc_attr($descrip); ?>" id="descripParam" />
		<input type="hidden" value="<?php echo esc_attr($view); ?>" id="viewParam" />
		<input type="hidden" value="<?php echo esc_attr($buttons); ?>" id="buttonsParam" />
		<input type="hidden" value="<?php echo esc_attr($entity_id); ?>" id="entityIdParam" />
		<input type="hidden" value="<?php echo esc_attr($entity_type); ?>" id="entityTypeParam" />
		<input type="hidden" value="<?php echo esc_attr($list_style); ?>" id="listStyleParam" />
		<input type="hidden" value="<?php echo esc_attr($show_venue); ?>" id="showVenueParam" />
		<input type="hidden" value="<?php echo esc_attr($show_artist); ?>" id="showArtistParam" />
		<input type="hidden" value="<?php echo esc_attr( $limit ); ?>" id="resultsPerPage" />
		<input type="hidden" value="<?php echo $large_pics === true ? 'true' : 'false'; ?>" id="largePicsParam" />
		<input type="hidden" value="<?php echo site_url(); ?>" id="siteUrl" />
		
		<div class="musicidb-tabs">
			<?php if( $supportsCalView ): ?>
				<div class="singleWide">
			        <ul class="calViewButtons musicidb-tabNav">
			            <li class="musicidb-tab1">
			             	<a id="listViewToggle" href="#listView">
			             		<i class="fui-list-numbered"></i><!-- List View -->
			             	</a>
			            </li>

			            <li class="musicidb-tab2">
			             	<a id="calViewToggle" href="#calView">
			             		<i class="fui-calendar"></i><!-- Calendar View -->				             	
			             	</a>
			            </li>
			        </ul>
		        </div>
	        <?php endif; ?>

	   		<div id="listView" class="<?php if($view == 'list'): ?>current<?php endif; ?> <?php esc_attr_e( $style_class ); ?> musicidb-tab">
	   			<div class="musicidb-tabs ui-tabs-nav">
		   			<ul class="musicidb-tabNav eventTabsLinks">
		   				<li class="current"><a href="#upcomingEvents">Upcoming Events</a></li>
		   				<li><a href="#pastEvents">Past Events</a></li>
		   			</ul>

		   			<div id="upcomingEvents" class="musicidb-tab current">
						<ul class="
							eventsList 
							<?php esc_attr_e( 'style-' . $style_class ); ?>
							<?php echo $large_pics === true ? 'largePics' : ''; ?>
						">
						</ul>

						<div class="centerThisGuy">
							<div class="loadUpcomingAnim loading-animation">
								<img src='<?php echo plugins_url('/images/loading.svg', MUSICIDB_PLUGIN); ?>' class='preLoader' />
							</div>
							
							<button class="loadMoreBtn btn greenBtn">Load More</button>
						</div>

						<input type="hidden" class="pageNum" value="1" />
		   			</div><!-- /#upcomingEvents -->

		   			<div id="pastEvents" class="musicidb-tab">

						<ul class="
							eventsList 
							<?php esc_attr_e( 'style-' . $style_class ); ?> 
							<?php echo $large_pics === true ? 'largePics' : ''; ?>
						"></ul>

						<div class="centerThisGuy">
							<div class="loadPastAnim loading-animation">
								<img src='<?php echo plugins_url('/images/loading.svg', MUSICIDB_PLUGIN); ?>' class='preLoader' />
							</div>

							<button class="loadMoreBtn btn greenBtn">Load More</button>
						</div>

						<input type="hidden" class="pageNum" value="1" />
		   			</div><!-- /#pastEvents -->
		   		</div><!-- /.tabs -->
	   		</div><!-- /#listView -->

	   		<?php if( !empty( $entities['venue'] ) && count( $entities['venue'] ) == 1 ): ?>
		   		<div id="calView" class="current musicidb-tab">
		   			<iframe style="width: 100%; border: none;" src="//musicidb.com/venue/getVenueDetailFrame.htm?venueId=<?php esc_attr_e( $entities['venue'][0] ); ?>&showHead=false&showEvents=true&showGigs=false&view=cal&display=<?php echo esc_attr($display); ?>&theme=<?php echo esc_attr($themeAtt); ?>&descrip=<?php echo esc_attr($descrip); ?>" height="950"></iframe>
		   		</div><!-- /#calView -->
		   	<?php endif; ?>
		</div><!-- /.tabs -->

   		<div class="eventDetailModal window">
   			<img src='<?php echo plugins_url('/images/loading.svg', MUSICIDB_PLUGIN); ?>' class='preLoader' />
   		</div><!-- /.eventDetailModal -->
   		
   		<div class="musicidb-integration-mask"></div><!-- /.musicidb-integration-mask -->

  		<a href="https://musicidb.com" target="_blank" class="widgetsLink">
  			<img id="musicidb-logo" 
  			src="<?php echo esc_url(plugin_dir_url(__FILE__) . 'images/MusicIDB-Logo.png'); ?>" alt="MusicIDB - The Music Industry Database" 
  			title="MusicIDB - The Music Industry Database">
  		</a>

    </div><!-- /#venue.inside -->
</div>	

<?php else: ?>
		<p>Could not connect to MusicIDB, please check plugin settings.</p>
<?php 
	endif; 