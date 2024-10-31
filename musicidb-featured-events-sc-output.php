<?php 

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

use MusicIDB\Client\Api_Exception;
use MusicIDB\Client\MusicIDB_API;
?>

<?php if ($connected) : ?>
    <?php
        $musicidb_plugin = MusicIDBIntegration::get_instance();
        
        $url = !empty($atts['ticketdefault']) ? $musicidb_plugin->musicidb_strip_unicode( $atts['ticketdefault'] ) : '';
        $leftFlag = !empty($atts['leftflag'] && is_string($atts['leftflag'])) ? $musicidb_plugin->musicidb_strip_unicode( $atts['leftflag'] ) : 'Featured Events';
        $size = !empty($atts['titlesize']) ? $atts['titlesize'] : '';
        $background = !empty($atts['background'] && is_string($atts['background'])) ? $musicidb_plugin->musicidb_strip_unicode( $atts['background'] ) : '#ff0000';
        $fallbackImage = !empty($atts['fallbackimage']) ? $musicidb_plugin->musicidb_strip_unicode( $atts['fallbackimage'] ) : '';
        $venue_ids = $_instance->musicidb_get_ids_from_string( $musicidb_plugin->musicidb_strip_unicode( $atts['id'] ) );

        if( empty( $venue_ids ) )
            $venue_ids = array( $default_venue_id );

        try {
        
            if( empty( $venue_ids ) )
                throw new Api_Exception( 'No Venue ID' );

            $args = array(
                'is_published' => true,
                'limit' => $limit,
                'is_delete' => false,
                'is_featured' => true,
                'venues' => $venue_ids,
                'start_date_after' => current_time( 'Y-m-d' ),
                'order_direction' => 'ASC'
            );

            $response = MusicIDB_API::request( 'event', 'filter_query', $args );

            $events = array();
            if( 200 === $response->get_status() ) { 
                $events = $response->get_events();
            } 

            if( count($events) < $limit ) {

                // Handle remainder when there aren't enough
                // featured events. This uses filter_query 
                // to avoid dupes
                $remainder = $limit - count($events);
                
                $args = array(
                    'is_published' => true,
                    'limit' => $remainder,
                    'is_delete' => false,
                    'is_featured' => false,
                    'venues' => $venue_ids,
                    'start_date_after' => date( 'Y-m-d' ),
                    'order_direction' => 'ASC'
                );

                $upcoming_response = MusicIDB_API::request( 'event', 'filter_query', $args );

                if( 404 === $upcoming_response->get_status() ) { 
                    $upcoming = array();
                } else {
                    $upcoming = $upcoming_response->get_events();
                }

                $events = array_merge( $events, $upcoming );
            }

        } catch( Api_Exception $e ) {

            error_log( $e->getMessage() );
            ?>

            <li>
                <p>
                    Sorry we're having trouble loading the events right now...

                    <?php if( defined( 'WP_DEBUG' ) && WP_DEBUG ): ?>
                        <br />Encountered an error: <?php esc_html_e( $e->getMessage() ); ?>
                    <?php endif; ?>
                </p>
            </li>

            <?php
        }

    ?>

    <?php if( empty($events) ): ?>
        <!-- <p>No events found</p> -->
    <?php else: ?>
        <div id="musicidb-featured-events" class="musicidb-events-integration blackBack cf">
            <div class="slider-wrapper">
                <div id="featured-events-slider" class="slider-banner">
                    <?php foreach( $events as $item ): ?>
                        <div class="slider-item">
                            <div class="slider-background">
                                <!-- If a Poster or Event Thumbnail Exists -->
                                <?php if( $item->get_poster() || $item->get_thumbnail() ): ?>
                                    <?php 
                                    /* Featured Event Banner Image */
                                    if (!empty($item->get_thumbnail())): ?>
                                        <?php if(stripos($item->get_thumbnail(), '/event/logo/') === false): ?>
                                            <img data-lazy="<?php echo musicidb_get_image_at_size($item->get_thumbnail(), 'SlidePoster'); ?>"
                                                 class="" alt="<?php esc_attr_e ($item->get_name()); ?>" />
                                        <?php else: ?>
                                            <img data-lazy="<?php echo musicidb_get_image_at_size($item->get_thumbnail(), 'Large'); ?>"
                                                 class="" alt="<?php esc_attr_e ($item->get_name()); ?>" />
                                        <?php endif; ?>
                                    
                                    <?php 
                                    /* Regular Event Poster */
                                    elseif (!empty($item->get_poster())): ?>
                                        <img data-lazy="<?php echo musicidb_get_image_at_size($item->get_poster(), 'SlidePoster'); ?>"
                                             class="" alt="<?php esc_attr_e ($item->get_name()); ?>" />
                                    <?php endif; ?>

                                <!-- If there is no poster or thumbnail, and there are billed artists -->
                                <?php elseif ($item->get_sets()): ?>
                                    
                                    <?php $artist = $item->get_sets()[0]->get_artist(); ?>
                                    
                                    <?php if ($artist): ?>
                                        <?php if ($artist->get_image_url()): ?>
                                            <img data-lazy="<?php echo musicidb_get_image_at_size($artist->get_image_url(), 'Large'); ?>" alt="<?php esc_attr_e ($artist->get_name()); ?>"
                                                 class="" />
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    
                                <?php else: ?>
                                    <!-- If there is no poster or no thumbnail, and there are no billed artists -->
                                    <img data-lazy="<?php echo esc_url($fallbackImage); ?>" 
                                        class="defaultPic" 
                                        alt="<?php esc_attr_e ($item->get_name()); ?>" 
                                        />
                                <?php endif; ?>
                            </div>

                            <div class="slider-foreground">
                                <div class="detailsCard">
                                    <div class="overlay" style="background: <?php esc_attr_e($background); ?>;"></div>
                                    <div class="content-wrapper">
                                        <div class="slider-label">
                                            <div class="label-text"><?php echo esc_html($leftFlag); ?></div>
                                        </div>
                                        <div class="content">
                                            <div class="slider-date"><?php echo date('F jS, Y', strtotime($item->get_date()->get_date())); ?></div>
                                            <h2 class="slider-title" style="font-size: <?php esc_attr_e($size); ?>px;">
                                                <?php if (!empty($item->get_name()) && ($item->get_name() != '-')) : ?>
                                                    <span class="name"><?php echo esc_html($item->get_name()); ?></span>
                                                <?php endif; ?>
                                                <?php
                                                $title = array();

                                                if( !empty( $item->get_sets() ) ) {
                                                    foreach ($item->get_sets() as $set) {
                                                        $artist = $set->get_artist();
                                                        $title[] = $artist->get_name();
                                                    }
                                                }
                                                ?>
                                                <span class="artistNames name"><?php echo esc_html(implode(',  ', $title)); ?></span>
                                            </h2>
                                            <div class="buttonsBlockSection slider-link-wrapper ">
                                                <a href="#/" class="showDetailsLink view-event" data-event-id="<?php esc_attr_e($item->get_id()); ?>" title="View Details">View</a>

                                                <!-- Event Ticket Link -->
                                                <?php if (!empty($item->get_ticket_link())) : ?>
                                                    <a class="slider-link ticketsLink" target="_blank"
                                                       href="<?php echo esc_url($item->get_ticket_link()); ?>">Tickets <span
                                                                class="link-arrow"></span></a>

                                                    <!-- If no ticket link, show facebook link when available -->
                                                <?php elseif (!empty($item->get_facebook_link())): ?>
                                                    <a href="<?php echo esc_url($item->get_facebook_link()); ?>"
                                                       class="slider-link btn fbLinkWide" title="RSVP on Facebook"
                                                       target="_blank"><i class="fui-facebook"></i>RSVP</a>

                                                    <!-- If no tickets and no facebook link, use Fallback Default Ticket Link if available -->
                                                <?php elseif (!empty($url)): ?>
                                                    <a class="slider-link ticketsLink" target="_blank" href="<?php echo esc_url($url); ?>">Tickets
                                                        <span class="link-arrow"></span></a>
                                                <?php endif; ?>
                                               
                                            </div>
                                            <!-- Event Detail Pop-Up -->
                                            <!-- <div class="view-event-wrapper">
                                                
                                            </div> -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div><!-- /#StellarSlider -->
                <div class="rightSlideArea">
                    <div class="slider-controllers">
                        <button type="button" class="btn btn-prev"></button>
                        <button type="button" class="btn btn-next"></button>
                    </div>
                </div>
            </div>

            <div class="eventDetailModal window">
                <img src='<?php echo plugins_url('/images/loading.svg', MUSICIDB_PLUGIN); ?>' class='preLoader'/>
            </div><!-- /.eventDetailModal -->

            <div class="musicidb-integration-mask"></div><!-- /.musicidb-integration-mask -->

        </div>
    <?php endif; ?>
<?php else: ?>
    <!-- <p>Could not connect to MusicIDB, please check plugin settings.</p> -->
<?php
endif;