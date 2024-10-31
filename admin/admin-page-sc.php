<?php        
    // Die on direct access
    if( !defined( 'ABSPATH' ) )
        wp_die( 'Not allowed' );

    // check user capabilities
    if ( !current_user_can( 'manage_options' ) )
        wp_die( 'Not allowed' );      

    $is_modal = get_query_var( 'is_modal', false );
    $copy_button_class = ($is_modal) ? 'button-secondary' : 'button-primary';
?>

<div id="musicidb-events-settings-sc" class="admin-tab musicidb-settings">
    <ul class="musicidb-admin-tabs tabs">
        <li>
            <a href="#musicidb-events-settings-calendar">Event List / Calendar</a>
        </li>

        <?php if( !empty( $associated_entities['venue'] ) ): ?>
            <li>
                <a href="#musicidb-events-settings-featured-slider">Featured Event Slider</a>
            </li>
        <?php endif; ?>
    </ul>

    <div id="musicidb-events-settings-calendar" class="admin-tab megaFlexSection">
        <!-- Event List Calendar -->

        <div class="leftArea">
            
            <h2>Shortcode Options</h2>

            <?php if($connected == 0): ?>

                <p>You have not successfully connected your API key, please visit the <a href="<?php echo site_url();?>/wp-admin/admin.php?page=musicidb-integration">settings page</a> to do so.</p>

            <?php else: ?>

                <ul class="optionList">
                    <li>
                        <label for="themeSelect">Website Theme</label>
                        <select name="themeSelect" id="themeSelect">
                            <option value="light">Light</option>
                            <option value="dark">Dark</option>
                        </select>
                    </li>

                    <li>
                        <label>
                            Event list to display
                            <span class="small">Select one or more</span>
                        </label>

                        <div id="idSelect" name="idSelect">
                            <?php foreach( $associated_entities as $type => $mappings ): ?>
                                
                                <h2><?php esc_attr_e( ucfirst( $type ) ); ?></h2>

                                <ul class="selectedEntities">
                                    <?php 
                                        foreach( $mappings as $id => $entity ) {

                                            $checked = ( $id == $default_id && $type == $default_type ) ? ' checked' : '';

                                            printf( 
                                                '<li><input type="checkbox" id="entity-%d" value="%d" data-type="%s"%s><label for="entity-%d">%s</label></li>', 
                                                esc_attr( $id ),
                                                esc_attr( $id ),
                                                esc_attr( $type ),
                                                $checked,
                                                esc_attr( $id ),
                                                esc_html( $entity->get_name() )
                                            );

                                        }
                                    ?>
                                </ul>

                            <?php endforeach; ?>
                        </div>
                    </li>

                     <?php if( !empty( $associated_entities['venue'] ) ): ?>
                        <li>
                            <label for="defaultView">Default to Calendar or List?</label>
                            <select name="defaultView" id="defaultView">
                                <option value="list">List View</option>
                                <option value="cal">Calendar View</option>
                            </select>
                        </li>
                    <?php endif; ?>

                    <li>
                        <label for="listStyleSelect">List Layout</label>
                        <select id="listStyleSelect" name="listStyleSelect">
                            <option value="listwithpics" <?php echo ( $default_style == 'listwithpics' ) ? 'selected' : ''; ?>>
                                List with Pics
                            </option>
                            <option value="bigpics"  <?php echo ( $default_style == 'bigpics' ) ? 'selected' : ''; ?>>
                                List with Big Pics
                            </option>
                            <option value="posterboard" <?php echo ( $default_style == 'posterboard' ) ? 'selected' : ''; ?>>
                                Posterboard 
                            </option>
                            <option value="simple"  <?php echo ( $default_style == 'simple' ) ? 'selected' : ''; ?>>
                                Simple List (popular for artists)
                            </option>                        
                        </select>
                    </li>

                    <?php if( !empty( $associated_entities['venue'] ) ): ?>                    

                        <li>
                            <label for="displyImgs">Display Images on Calendar?</label>
                            <input type="checkbox" checked="checked" name="displyImgs" id="displyImgs" />
                        </li>

                        <li>
                            <label for="showDesc">Expanded Descriptions on List?</label>
                            <input type="checkbox" name="showDesc" id="showDesc" />
                        </li>
                    <?php endif; ?>

                </ul>

                <h2>Copy/Paste this Shortcode onto any page</h2>
                <input type="text" readonly="readonly" class="shortcode" value='[musicidb]' size="50" />
                
                <?php if( true === $is_modal ): ?>
                    <a href="#" class="insertShortcode button button-primary" data-target-class="shortcode">Insert Code</a>
                <?php endif; ?>

                <a href="#" class="copyToClipboard button <?php esc_attr_e( $copy_button_class ); ?>">Copy to Clipboard</a>
                
                <span class="copySuccess green hidden">Copied!</span>
                <span class="copyFail red hidden">Sorry, there was a problem please try again...</span>

                <h3>Advanced</h3>

                <ul class="optionList">

                    <?php if( !empty( $associated_entities['venue'] ) ): ?>
                        <li>
                            <label for="">Show / Hide Venue Name</label>
                            <select name="showVenue" id="showVenue">
                                <option value="show" <?php echo ( $default_style == 'compact' ) ? 'selected' : ''; ?>>Show</option>
                                <option value="hide" <?php echo ( $default_style == 'full' ) ? 'selected' : ''; ?>>Hide</option>
                            </select>
                        </li>
                    <?php endif; ?>

                    <?php if( !empty( $associated_entities['artist'] ) ): ?>
                        <li>
                            <label for="">Show / Hide Artist Name</label>
                            <select name="showArtist" id="showArtist">
                                <option value="show" selected>Show</option>
                                <option value="hide">Hide</option>
                            </select>
                        </li>                    
                    <?php endif; ?>

                    <li>
                        <label for="buttonPositions">Event Buttons Position</label>
                        <select name="buttonPositions" id="buttonPositions">
                            <option value="left">Left Aligned</option>
                            <option value="center">Center</option>
                            <option value="right">Right</option>
                        </select>
                    </li>
                </ul>

            <?php endif; ?>

        </div>
         <div class="rightArea">                
            <?php
            $examples = plugins_url('/images/MusicIDB-List-Styles-Collage.png', MUSICIDB_PLUGIN );
            ?>
            <img src="<?php echo esc_url( $examples ); ?>"  
            alt="MusicIDB Calendar List Examples"
            class="listExamplesPic" />
        </div>

    </div>

    
    <?php if( !empty( $associated_entities['venue'] ) ): ?>
        <!-- Featured Events Slider -->
        <div id="musicidb-events-settings-featured-slider" class="admin-tab">
            <h2>Shortcode Options</h2>
            <?php if($connected == 0): ?>
                <p>You have not successfully connected your API key, please visit the <a href="<?php echo site_url();?>/wp-admin/admin.php?page=musicidb-integration">settings page</a> to do so.</p>
            <?php else: ?>

                <ul class="optionList">
                    <li>
                        <label for="selected-venue">
                            Venue
                        </label>

                        <div id="selected-venue">
                            <?php foreach( $associated_entities as $type => $mappings ): ?>
                                
                                <?php 
                                    if( 'venue' != $type ) 
                                        continue; 
                                ?>

                                <ul class="selectedEntities">
                                    <?php 
                                        foreach( $mappings as $id => $entity ) {

                                            $checked = ( $id == $default_venue_id ) ? ' checked' : '';

                                            printf( 
                                                '<li><input type="checkbox" id="feat-venue-%d" value="%d" %s><label for="feat-venue-%d">%s</label></li>', 
                                                esc_attr( $id ),
                                                esc_attr( $id ),
                                                $checked,
                                                esc_attr( $id ),
                                                esc_html( $entity->get_name() )
                                            );

                                        }
                                    ?>
                                </ul>

                            <?php endforeach; ?>
                        </div>
                    </li>

                    <li>
                        <label for="tickets-url">
                            Default Tickets Link Url
                            <span class="small">Please include http://</span>
                        </label>
                        <input placeholder="http://yourwebsite.com/tickets" type="text" name="tickets-url" id="tickets-url" size="50" />
                    </li>

                    <li>
                        <label for="flag-text">
                            Top left flag text
                        </label>
                        <input placeholder="Featured Events" type="text" name="flag-text" id="flag-text" size="50" />
                    </li>

                    <li>
                        <label for="title-size">
                            Event Title Front Size
                        </label>
                        <input placeholder="16" type="text" name="title-size" id="title-size" size="10" />
                    </li>

                    <li>
                        <label for="amount">
                            Max # of Events
                            <span class="small">Max 12</span>
                        </label>
                        <input placeholder="6" type="number" name="amount" id="amount" size="10" />
                    </li>

                    <li>
                        <label for="overlay">
                            Left Side Overlay Color
                        </label>
                        <input placeholder="#ff0000" type="text" name="overlay" id="overlay" size="10" />
                    </li>

                    <li>
                        <label for="fallback-image">
                            Fallback image
                        </label>
                        <input placeholder="" type="text" name="fallback-image" id="fallback-image" size="50" />
                    </li>


                </ul>

                <h2>Copy/Paste this Shortcode onto any page</h2>
                <input type="text" readonly="readonly" class="shortcode-slider" value='[musicidb-featured-slider id="<?php esc_attr_e( $default_venue_id ); ?>"]' size="50" />

                <?php if( true === $is_modal ): ?>
                    <a href="#" class="insertShortcode button button-primary" data-target-class="shortcode-slider">Insert Code</a>
                <?php endif; ?>

                <a href="#" class="copyToClipboard button <?php esc_attr_e( $copy_button_class ); ?>">Copy to Clipboard</a>
             
                <span class="copySuccess green hidden">Copied!</span>
                <span class="copyFail red hidden">Sorry, there was a problem please try again...</span>

            <?php endif; ?>
        </div>
    <?php endif; ?>
</div><!-- /.wrap -->