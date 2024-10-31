jQuery(document).ready(function() {
    jQuery('.showDetailsLink').unbind('click').click(function(e) {
        e.preventDefault();

        var eventId = jQuery(this).data('event-id');
        if(eventId !== undefined && jQuery.isNumeric(eventId))
            showEventPopupDetails(eventId);
    });

    //Function Definitions
    function showEventPopupDetails(eventId) {
        var modalContainer = jQuery('.eventDetailModal');
        var mask = jQuery('.musicidb-integration-mask');

        if(musicidb_ajax_object !== undefined) {
            var preLoader = modalContainer.find('.preLoader').first();

            mask.fadeIn(200);
            modalContainer.fadeIn(200);

            /** Validate POST params **/
            if(musicidb_ajax_object.ajax_nonce === undefined || (musicidb_ajax_object.ajax_nonce !== undefined && musicidb_ajax_object.ajax_nonce.trim() == '')) {
                console.log("No nonce was found");
                return false;
            }

            if(eventId === undefined || (eventId !== undefined && !jQuery.isNumeric(eventId))) {
                console.log("eventId must be a number");
                return false;
            }
            /** End Validation **/

            var event_detail_data = {
                'action' : 'load_event_details',
                'eventId' : eventId,
                'security' : musicidb_ajax_object.ajax_nonce
            };

            jQuery.post(
                musicidb_ajax_object.ajax_url,
                event_detail_data,
                function(html) {
                    modalContainer.html(html);
                    jQuery("#artistCardsSlider .slides").slick();
                    jQuery('body').addClass('noScroll');
                    jQuery('html').css("overflow", "hidden");

                    //Set up the click listener on the close button
                    modalContainer.find('.modalClose').click(function(e) {
                        e.preventDefault();

                        modalContainer.fadeOut(200, function() {
                            modalContainer.html(preLoader);
                        });

                        mask.fadeOut(200);
                        jQuery('body').removeClass('noScroll');
                        jQuery('html').css("overflow", "auto");
                    });

                    jQuery(".musicidb-integration-mask").click(function(e) {
                        e.preventDefault();

                        modalContainer.fadeOut(200, function() {
                            modalContainer.html(preLoader);
                        });

                        mask.fadeOut(200);
                        jQuery('body').removeClass('noScroll');
                        jQuery('html').css("overflow", "auto");
                    });
                }
            );
        }
    }

});