(function($) {
    
    var $document = $(document);

    $document.ready(function() {

        setup_admin_tabs( document );

       //Copy to clipboard button
        $document.on('click', '#musicidb-events-settings-sc .copyToClipboard', function(e) {
            e.preventDefault();

            var copyTextarea = $('.shortcode');
            var copyFeaturedTextarea = $('.shortcode-slider');
            copyTextarea.select();
            copyFeaturedTextarea.select();

            try {
                var successful = document.execCommand('copy');

                if(successful)
                    $('.copySuccess').fadeIn().delay(200).fadeOut();
                else
                    $('.copyFail').fadeIn().delay(200).fadeOut();

            } catch (err) {
                console.log(err);

                $('.copyFail').fadeIn().delay(200).fadeOut();
            }
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #themeSelect', 
            shortcode_att: 'theme', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'val'
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #idSelect', 
            shortcode_att: 'id', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: function( att, shortcode, $element ) {

                var selected = $element.find('input:checked');
                var id = '';

                if( selected.length > 0 ) {
                
                    selected.each( function( index ) {

                        var $this = $(this);

                        var ent_id = $this.val();
                        var ent_type = $this.data('type');

                        id += ent_type + ':' + ent_id;

                        if( index < selected.length - 1 ) {
                            id += ',';
                        }

                    });

                }

                return musicidb_replace_shortcode_att( att, id, shortcode );

            }
        });

        musicidb_shortcode_field_update({
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #buttonPositions', 
            shortcode_att: 'buttons', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'val'
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #showDesc', 
            shortcode_att: 'descrip', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'checked'
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #displyImgs', 
            shortcode_att: 'display', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: function( att, shortcode, $element ) {

                var display = ($element.prop('checked')) ? 'img' : 'text';
                return musicidb_replace_shortcode_att( att, display, shortcode );
                
            }
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #defaultView', 
            shortcode_att: 'view', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'val'
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #largePicsOption', 
            shortcode_att: 'largepics', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'checked',
        });

        musicidb_shortcode_field_update({
            event_type: 'change',
            field_selector: '#musicidb-events-settings-sc #showVenue',
            shortcode_att: 'showvenue',
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'val'
        });

        musicidb_shortcode_field_update({
            event_type: 'change',
            shortcode_att: 'showartist',
            field_selector: '#musicidb-events-settings-sc #showArtist',
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: 'val'
        });

        musicidb_shortcode_field_update({ 
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-sc #listStyleSelect', 
            shortcode_att: 'style', 
            output_selector: '#musicidb-events-settings-sc .shortcode',
            handler: function( att, shortcode, $element ) {
                
                let list_style = $element.val();
                let show_venue = 'show';
                let show_artist = 'show';
                let $showVenue = $( '#showVenue' );
                let $showArtist = $( '#showArtist' );

                switch( list_style ) {

                    case 'listwithpics':
                    case 'largepics':
                    case 'bigpics':
                    case 'full':
                        show_venue = 'hide';
                        show_artist = 'show';
                        break;

                    case 'simple':
                    case 'compact':
                        show_venue = 'show';
                        show_artist = 'show';
                        break;

                    default:
                        show_venue = 'show';
                        show_artist = 'show';
                        break;

                }

                $showVenue.val( show_venue );
                shortcode = musicidb_replace_shortcode_att( 'showvenue', show_venue, shortcode );
                
                $showArtist.val( show_artist );
                shortcode = musicidb_replace_shortcode_att( 'showartist', show_artist, shortcode );

                return musicidb_replace_shortcode_att( att, list_style, shortcode );

            }
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #selected-venue',
            shortcode_att: 'id',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: function( att, shortcode, $element ) {

                var selected = $element.find('input:checked');
                var id = '';

                if( selected.length > 0 ) {
                
                    selected.each( function( index ) {

                        var $this = $(this);

                        var ent_id = $this.val();

                        id += ent_id;

                        if( index < selected.length - 1 ) {
                            id += ',';
                        }

                    });

                }

                return musicidb_replace_shortcode_att( att, id, shortcode );
            }

        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #tickets-url',
            shortcode_att: 'ticketDefault',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #flag-text',
            shortcode_att: 'leftFlag',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #title-size',
            shortcode_att: 'titleSize',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #amount',
            shortcode_att: 'numevents',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #overlay',
            shortcode_att: 'background',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        musicidb_shortcode_field_update( {
            event_type: 'change', 
            field_selector: '#musicidb-events-settings-featured-slider #fallback-image',
            shortcode_att: 'fallbackImage',
            output_selector: '#musicidb-events-settings-featured-slider .shortcode-slider',
            handler: 'val'
        });

        var $shortcode_btn = $( '#musiscidb-insert-shortcode' );
        if( $shortcode_btn.length > 0 ) {

            $shortcode_btn.on( 'click', function( e ) {

                e.preventDefault();

                tb_show( 
                    'MusicIDB Shortcode Options', 
                    ajax_shortcode_options_url
                );

            });

            $document.on('click', '#musicidb-events-settings-sc .insertShortcode', function(e) {
            
                e.preventDefault();

                var target = $(this).data('target-class');

                if( 'undefined' == typeof target ) {
                    console.error( 'no target shortcode field supplied' );
                    return;
                }

                if( '' == target ) {
                    console.error( 'target cannot be empty' );
                    return;
                }

                target = '.' + target;
                var copyTextarea = $(target);

                if( copyTextarea.length > 0 ) {
                 
                    var val = copyTextarea.val();

                    wp.media.editor.insert( val );
                    tb_remove();
                
                }

            });

            // Ehhhhh.....
            // Depending on a mutation observer
            // is undesirable and expensive, but
            // there is no callback after ajax 
            // loads in thickbox
            var in_dom = $( '#musicidb-events-settings' ).length > 0;
            var observer = new MutationObserver(function( mutations ) {

                var $loaded_content = $( '#musicidb-events-settings' );

                // Make sure tabs get set up
                // after ajax content loads
                if( $loaded_content.length <= 0 && in_dom ) {
                    
                    in_dom = false;
                    return;

                } else if( $loaded_content.length > 0 ) {
                
                    if(!in_dom) {

                        setup_admin_tabs( '#musicidb-events-settings' );
                        
                        // override thickbox styles...
                        // thickbox doesn't let us dynamically
                        // size content, so this is as good as
                        // this is going to get
                        $('#TB_ajaxContent').css({ 
                            width: '100%',
                            height: '100%',
                            padding: 0
                        });

                        $('#TB_window').css({
                            overflow: 'auto'
                        });

                    }

                    in_dom = true;

                } 
             
            });

            observer.observe( document.body, { childList: true } );

        }
    });

    function setup_admin_tabs( selector ) {

        var tabs = $(selector).find('ul.tabs');

        if( tabs.length <= 0 || tabs.hasClass('tabs-init') )
            return;

        tabs.addClass('tabs-init');
        
        tabs.each(function() {
            // For each set of tabs, we want to keep track of
            // which tab is active and its associated content
            var $active, $content, $links = $(this).find('a');

            // If the location.hash matches one of the links, use that as the active tab.
            // If no match is found, use the first link as the initial active tab.
            $active = $($links.filter('[href="'+location.hash+'"]')[0] || $links[0]);
            $active.addClass('current');

            $content = $($active[0].hash);
            // Hide the remaining content
            $links.not($active).each(function () {
                $(this.hash).hide();
            });

            // Bind the click event handler
            $(this).on('click', 'a', function(e){
                // Make the old tab inactive.
                $active.removeClass('current');
                $content.hide();

                // Update the variables with the new link and content
                $active = $(this);
                $content = $(this.hash);
                // Make the tab active.
                $active.addClass('current');
                $content.show();

                // Prevent the anchor's default click action
                e.preventDefault();
            });
        });

    }

    // Delegate shortcode options event handlers
    function musicidb_shortcode_field_update( opts ) {
        
        if( 'object' != typeof opts )
            return false;

        if( 'undefined' == typeof opts.event_type ) {
            console.error( 'event_type is required by musicidb_shortcode_field_update' );
            return false;
        }

        if( 'undefined' == typeof opts.field_selector ) {
            console.error( 'field_selector is required by musicidb_shortcode_field_update' );
            return false;
        }

        if( 'undefined' == typeof opts.shortcode_att ) {
            console.error( 'shortcode_att is required by musicidb_shortcode_field_update');
            return false;
        }

        if( 'undefined' == typeof opts.output_selector ) {
            console.error( 'output_selector is required by musicidb_shortcode_field_update' );
            return false;
        }

        var opts = {
            event_type: opts.event_type, 
            field_selector: opts.field_selector,
            shortcode_att: opts.shortcode_att,
            output_selector: opts.output_selector,
            handler: ( 'undefined' == typeof opts.handler) ? 'val' : opts.handler,
            callback: ( 'undefined' == typeof opts.callback) ? null : opts.callback
        };

        $document.on( opts.event_type, opts.field_selector, function(e) {

            var $shortcode_field = $(opts.output_selector)
            var shortcode = $shortcode_field.val();
            var out = shortcode;

            var default_handler = function( type, att, shortcode, $element ) {

                if( 'val' != type && 'checked' != type )
                    return shortcode;

                var val;

                if( 'val' == type ) {
                    val = $element.val();
                } else if( 'checked' == type ) {
                    val = $element.prop('checked');
                }
    
                return musicidb_replace_shortcode_att( att, val, shortcode );

            };

            if( 'function' == typeof opts.handler )
                out = opts.handler( opts.shortcode_att, shortcode, $(this) );
            else if( 'string' == typeof opts.handler )
                out = default_handler( opts.handler, opts.shortcode_att, shortcode, $(this) );

            if( 'function' == typeof opts.callback )
                opts.callback( out );

            $shortcode_field.val( out );
        });

    }

    function musicidb_replace_shortcode_att( att, val, shortcode ) {

        var shortcode_len = shortcode.length;
        var insert_index = shortcode_len - 1;
        var regex = new RegExp( "\\s" + att + '="[^"]*"', 'g' );

        if( shortcode.indexOf( ' ' + att ) > 0 && '' != val )
            return shortcode.replace( regex, ' ' + att + '="' + val + '"' );
        else if( '' != val )
            return shortcode.substring( 0, insert_index ) + ' ' + att + '="' + val + '"' + shortcode.substring( shortcode_len - 1 );
        else
            return shortcode.replace( regex, '' );

    }

})(jQuery);