//Front end JS for MusicIDB Integration Plugin

var MUSICIDB_PATH;

jQuery(document).ready(function() {

    if(jQuery("#featured-events-slider").length > 0) {
        jQuery("#featured-events-slider").slick({
			dots: true,
            infinite: true,
            fade: true,
            autoplay: true,
            pauseOnHover: false, 
            autoplaySpeed: 5500,
            appendArrows: '.slider-controllers',
            prevArrow: '.btn-prev',
            nextArrow: '.btn-next',
            mobileFirst: true,
            slidesToShow: 1,
            adaptiveHeight: true, 
            lazyLoad: 'progressive'
		});
	}

	/** Only run on pages where the plugin exists **/
	if(jQuery(".musicidb-events-integration").length > 0) {
		var upcomingEvents = jQuery("#upcomingEvents");
		var pastEvents = jQuery("#pastEvents");
		
		/** loadEvents function definition **/
		var loadEvents = function(container, listType, page ) {
			var eventList = container.find('.eventsList');
			//var preLoader = container.find('.preLoader');
			var preLoader = container.find('.preLoader').first();

			var pageCounter = container.find('.pageNum');
			var descrip = jQuery('#descripParam').val();
			var buttons = jQuery('#buttonsParam').val();
			var totalPages = container.find('.totalPages');
			var entity_id = jQuery('#entityIdParam').val();
			var entity_type = jQuery('#entityTypeParam').val();
			var list_style = jQuery('#listStyleParam').val();
			var show_venue = jQuery('#showVenueParam').val();
			var show_artist = jQuery('#showArtistParam').val();
			var resultsPerPage = jQuery( '#resultsPerPage' ).val() || 15;
			var largePics = jQuery( '#largePicsParam' ).val() || false;

			if(totalPages.length <= 0 || parseInt(pageCounter.val()) <= parseInt(totalPages.val())) {
				preLoader.show();
				totalPages.parent().remove();

				/** Validate POST params **/
					if(musicidb_ajax_object.ajax_nonce === undefined || (musicidb_ajax_object.ajax_nonce !== undefined && musicidb_ajax_object.ajax_nonce.trim() == '')) {
						console.error("No nonce was found");
						return false;
					}

					if(listType === undefined || (listType !== undefined && typeof listType !== 'string')) {
						console.error("listType must be a string");
						return false;
					} else if(listType.trim() != 'upcoming' && listType.trim() != 'past') {
						console.error("listType must be 'upcoming' or 'past'");
						return false;
					}

					if(page === undefined || (page !== undefined && !jQuery.isNumeric(page))) {
						console.error("page must be a number");
						return false;
					}

					if(resultsPerPage === undefined || (resultsPerPage !== undefined && !jQuery.isNumeric(resultsPerPage))) {
						console.error("resultsPerPage must be a number");
						return false;
					}

					if(descrip === undefined || (!jQuery.isNumeric(descrip) && descrip !== 1 && descrip !== 0)) {
						console.error("descrip must be '1' or '0'");
						return false;
					}

					if(buttons === undefined || (buttons !== undefined && typeof buttons !== 'string')) {
						console.error("buttons must be a string");
						return false;
					} else if(buttons.trim() != 'left' && buttons.trim() != 'right' && buttons.trim() != 'center') {
						console.error("buttons must be 'left', 'right', or 'center'");
						return false;
					}

					if( entity_id !== undefined && typeof entity_id !== 'string' ) {
						console.error( 'entity_id must be a string' );
						return false;
					}

					if( list_style !== undefined && typeof list_style !== 'string' ) {
						console.error( 'list_style must be a string' );
						return false;
					}

					if( show_venue === undefined ) {
						console.error( 'show_venue is required' );
						return false;
					}

					if( show_artist === undefined ) {
						console.error( 'show_venue is required' );
						return false;
					}

				/** End Validation **/
				
				var event_list_data = {
					'action' : 'load_events_list',
					'security' : musicidb_ajax_object.ajax_nonce,
					largePics,
					listType,
					resultsPerPage,
					page,
					descrip,
					buttons,
					entity_id,
					entity_type,
					list_style,
					show_venue,
					show_artist
				};

				jQuery.post(
					musicidb_ajax_object.ajax_url,
					event_list_data,
					function(html) {
						preLoader.hide();
						container.find(".loadMoreBtn").show();

						eventList.append(html);

						//Re-assign total pages (used for first load)
						totalPages = container.find('.totalPages');

						eventList.find('.summaryToggle .fui-plus-circle').unbind('click').click(function(e) {
							e.preventDefault();

							var event = jQuery(this).parents('.listEvent');

							jQuery(this).toggleClass('expanded');
							jQuery(this).toggleClass('collapsed');

							event.find('.fullInfo').stop().slideToggle(200);
						});

						eventList.find('.showDetailsLink').unbind('click').click(function(e) {
							e.preventDefault();

							var eventId = jQuery(this).data('event-id');

							if(eventId !== undefined && jQuery.isNumeric(eventId))
								showEventPopupDetails(eventId);
						});

						pageCounter.val(parseInt(page) + 1);

						if(parseInt(pageCounter.val()) > parseInt(totalPages.val())) {
							container.find('.loadMoreBtn').hide();
						}
					}
				);
			}
		};

		/** LOAD EVENTS ON PAGE LOAD **/
		loadEvents(upcomingEvents, "upcoming", 1 );
		loadEvents(pastEvents, "past", 1 );

		//Click Load More 
		jQuery(".loadMoreBtn").click(function(e) {
			e.preventDefault();

			var container = jQuery(this).closest('.musicidb-tab');
			var preLoader = container.find('.preLoader');
			var currPage = parseInt(container.find('.pageNum').val());
			var listType;

			if(container.attr('id') == 'pastEvents') {
				listType = 'past';
			} else if(container.attr('id') == 'upcomingEvents') {
				listType = 'upcoming';
			}

			jQuery(this).hide();
			preLoader.show();

			loadEvents( container, listType, currPage );
		});

		//Tabs
		jQuery('.musicidb-tabNav a').click(function(e) {
			e.preventDefault();
			
			var id = jQuery(this).attr('href');
			var navContainer = jQuery(this).closest('.musicidb-tabNav');
			var container = navContainer.closest('.musicidb-tabs');
			var liElem = jQuery(this).parent();

			navContainer.find('li').removeClass('current');
			liElem.addClass('current');

			container.children('.musicidb-tab').removeClass('current');
			jQuery(id).addClass('current');
		});
	}
});

jQuery(window).load(function() {
	if(jQuery(".musicidb-events-integration").length > 0) {
		if(jQuery('#viewParam').length > 0) {
			var view = jQuery('#viewParam').val();

			if(view !== undefined && view.trim() !== '') {
				if(view == 'list')
					jQuery('#listViewToggle').click();
				else if(view == 'cal')
					jQuery('#calViewToggle').click();
			}
		}
	}
});

//Function Definitions
function showEventPopupDetails( eventId ) {
	var modalContainer = jQuery('.eventDetailModal');
	var mask = jQuery('.musicidb-integration-mask');

	if(musicidb_ajax_object !== undefined) {		
		var preLoader = modalContainer.find('.preLoader').first();

		var list_style = jQuery('#listStyleParam').val();
		var show_venue = jQuery('#showVenueParam').val();
		
		if( list_style !== undefined && typeof list_style !== 'string' ) {
			console.error( 'list_style must be a string' );
			return false;
		}

		if( show_venue === undefined ) {
			console.error( 'show_venue is required' );
			return false;
		}

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
			'security' : musicidb_ajax_object.ajax_nonce,
			'list_style': list_style,
			'show_venue': show_venue
		};

		jQuery.post(
			musicidb_ajax_object.ajax_url,
			event_detail_data,
			function(html) {
				modalContainer.html(html);

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

				loadMediaTabsMusicIDB();

				//Set up the artist card slider
				jQuery("#artistCardsSlider .slides").slick();
			}
		);
	}
}

function loadMediaTabsMusicIDB() {
	jQuery(".mediaTabNav a").click(function(e){
		e.preventDefault();

		var tabId = jQuery(this).attr("href").substring(1);
		var navContainer = jQuery(this).closest(".mediaTabNav");
		var container = navContainer.closest(".mediaHolder");
		var tabContainer = container.find(".artistMediaTabs");
		var tab = tabContainer.find("."+tabId);

		navContainer.find('.current').removeClass('current');
		jQuery(this).parent().addClass('current');

		tabContainer.find('.current').removeClass('current');
		tab.addClass('current');
	});

	jQuery(".artistMediaExpander").click(function(e){
		e.preventDefault();

		var artistMediaSection = jQuery(this).next(".artistMediaSection");

		if(artistMediaSection.hasClass('expanded')) {
			artistMediaSection.removeClass('expanded').stop().slideUp();
		} else {
			artistMediaSection.addClass('expanded').stop().slideDown();
		}
	});
}