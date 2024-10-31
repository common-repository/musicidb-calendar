=== MusicIDB Events Calendar ===
Contributors: Megabase
Donate link: https://blog.musicidb.com/donate/
Tags: musicidb, music, industry, database, calendar, venue, concert, live, entertainment, events, bands, youtube, bandcamp
Requires at least: 4.0
Tested up to: 6.6.2
Requires PHP: 7.3
Stable tag: 2.5.12
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl.html

An interactive website calendar for live music and event listings.  

== Description ==

Built for Artists, Venues, Bars, Restaurants and the Music Industry. This plugin uses the MusicIDB API to seamlessly display your events list and calendar onto any WordPress page via a shortcode or theme hooks. 

With an intuitive dashboard, instant updates to your website, and integration of artists’ music, videos, social links and bios, MusicIDB Event Calendar makes it possible to easily launch a fully featured calendar for your website visitors. 

**Artists**

- Modern, compact list view launched for artists
- While creating any event, tag or add any venue you play at
- Each event can display with Facebook event links or ticket links  
- You can tag / add other bands on any show you play, for easy link-sharing with other bands 
- Drop view templates in your local theme to override

Request an API key here: [Artist API Key Request](https://blog.musicidb.com/apikey/)

**Venues**

- Supports multiple views including event list with picss, text only list posterboard layout, grid calendar
- Tag or add any artist/band/dj
- Tagging bands enables you to build a network of artists to drop on your calendar
- 10,000+ artists in the database
- Website visitors can quickly watch videos and play embedded music of artists tagged on your events in a  lightbox (pop-up) on your website calendar page
- Artist media can be embedded from YouTube, Bandcamp, SoundCloud, Spotify and any other services that provide embeddable media. 
- Override views by copying plugin views in your local theme folder
- MusicIDB calendar views allows event images on each date
- *Pro* - Create recurring events such as weekly Open Mic, Karaoke or Trivia events, or weekly promotional events such as food specials, and much more.

This plugin requires a MusicIDB.com account and API key, which you can generate from your venue page once it has been claimed and approved. 

Visit [MusicIDB.com](https://MusicIDB.com) to create your account.  

[API Developer information](http://api.musicidb.com/swagger).

== Installation ==

For instructions with screenshots [View our Plugin Documentation](https://blog.musicidb.com/help/musicidb-events-calendar-plugin-docs/).

1. From your WordPress dashboard, visit **Plugins** > Add New
2. Search for "**MusicIDB Events Calendar**" and install and activate the plugin
3. **Visit [MusicIDB.com](https://MusicIDB.com)**, and create and **log in to your account**
4. **Create and claim** your **venue listing** or **artist listing**
5. While your claim is being processed, **create a few of your upcoming events** by clicking on your MusicIDB.com dashboard calendar (tag your artist or venue listing and make it the default venue with the checkbox)
6. Venues - Once your venue claim has been approved, **visit your venue's page** on MusicIDB.com (page link on left side or use search box), click "Embed Widgets", click the "WordPress Plugin" tab (in the pop-up) and then click "**Generate API Key**"
7. Artists - Request an API key from the link above and skip to step 8.
8. Back on your WordPress Dashboard, click on "**MusicIDB Calendar**" from the left side nav
9. **Enter your API Key**, and click "Save Settings". You should see "Connected :)" (as long as you have created at least one upcoming event on MusicIDB.com).
10. Click on the "**Shortcodes**" tab, and use the options to customize and generate your shortcode which can be pasted into any page through the WordPress editor.

== Frequently Asked Questions ==

= Do I need a MusicIDB.com Account? =

Yes, you will need an account on MusicIDB before you will be able to use this plugin. Registration is easy, [click here](https://musicidb.com/user/registration.htm) to create an account.

You won't need to log in to your website to update your events, you'll be able to manage your website events directly from your MusicIDB.com dashboard.  The ability to create and manage events from within your website is intended to be available in a future version of the plugin.  

= How do I create an API Key? =

For venues - once you have created an account on MusicIDB.com, create and claim your venue listing.  

Once your venue claim has been approved, you can visit your venue's page on MusicIDB.com (you can search for it or click on the left side nav), click Embed Widgets, click the WordPress Plugin tab of the pop-up, and then click Generate API Key.

For artists - request an api key here: [Artist API Key Request](https://blog.musicidb.com/apikey/)

= How do I get support? =

Either chat with us through our website or blog by clicking the orange chat icon in the footer, or email support [at] musicidb.com. Questions are generally answered within one business day. 

We answer questions related to usage, implementation, and customization of MusicIDB Events Calendar. We cannot guarantee resolutions for issues that may be due to custom theme code or 3rd party plugin conflicts & compatibility.

= What calendar options are available?  How can I add my MusicIDB Events from within a template? =

To add MusicIDB Events from a theme template, you can use code like this wrapped in PHP tags:
	                            
    musicidb_events(array(
        'theme' => 'light',
        'view' => 'list',
        'descrip' => 'true',
        'display' => 'img'
    ));   

The passed array parameter names and values are exactly the same as the shortcode attributes, and can be copied from the shortcode generator.

Here are the available parameters, all of which can be configured in the shortcode generator.

id: venue:123,artist:123,venue:456
Used to specify the entities you wish to list events for. Should be a comma separated string. Can be generated with shortcode generator.

view: cal or list
When viewing Calendar/ Event List, declares which view will display initially on page load

display: text or img
On calendar view only, option declares whether only text or images (when available) are visible within each calendar date of the full month view.

numevents: Integer
Maximum number of events to load, per page, in the Event List (default is 15).

theme: light or dark
If you have a dark background, use dark and your MusicIDB events will be styled to match your theme better.  Use light if you have a white or light background color.

style: full or compact
This controls the list style. 
full = "Event List with Pictures" (popular for venues)
compact = "Simple List" (popular for artists)

descrip: visible or hidden
When list view is shown, declares whether the Event Description area is visible prior to expanding the event

buttons: left, center, or right
Control the button position on the "Event List with Pictures" style event list

showvenue: show or hide
If you want to show or hide the venue name in your event list you can use this option to do so. By default venue is hidden on the "Event List with Pictures" list style, and shown on the "Simple List" list style.

showartist: show or hide
If you want to show or hide the artist names in your event list you can use this option to do so. By default artists are hidden on the "Simple List" list style, and shown on the "Event List with Pictures" list style.

All parameters can be viewed [here](https://blog.musicidb.com/help/calendar-integration-options-venue-bookers-promoters/).

= What featured events slider options are available?  How can I add the MusicIDB Featured Events slider from within a template? =

To add MusicIDB Featured Events from a theme template, you can use code like this wrapped in PHP tags:

    musicidb_featured_events(array(
        'ticketdefault' => 'https://website.com/tickets',
        'leftflag' => 'Featured Events',
        'titlesize' => '22',
        'numevents' => '8',
        'background' => '#000000',
        'fallbackimage' => 'https://website.com/image.jpg',
    ));  

Shortcode version example: [musicidb-featured-slider titleSize="22" numevents="8" background="#000000" fallbackImage="https://website.com/image.jpg" leftFlag="Featured Events" ticketDefault="https://website.com/tickets"]		

ticketdefault: URL
If you want users to automatically visit a master tickets site or webpage, you can fill this one to automatically create a ticket link for all events.

leftflag: Text flag over left side
Flag over all featured events at top left corner, for example "Coming to The Music Shack".

titlesize: Integer
Font size for title of event (Which includes automatically printed names of tagged artists)

numevents: Integer
Maximum number of events to load in the slider (absolute limit is 12).

background: Hex Code
Background color behind text, over image.  Will automatically become partially translucent.

fallbackimage: URL
An image to be used for this featured event slide when the featured event image, event poster, and artist photos attached to tagged artists are not found. 

= How do I get the artist videos and music to appear? =

There are over 10,000 artists currently listed on MusicIDB, many of which already have media attached to their artist listing. If you are entering a new artist, you can add their media by searching for them after tagging them on an event, then edit their Artist Library on MusicIDB.com and include the embed code from their media channels such as YouTube, Bandcamp, Soundcloud, etc.

Our team spends time each week improving the database, so in some cases you might find we have helped fill in missing information or media for certain artists. 

Artists can also choose to update their own listings at their leisure, so they can contribute their latest release to their page, giving your website visitors the best possible experience to intice visitors to attend events.

= Can I customize the list templates? =

Yes! The plugin first checks a child theme, followed by a parent theme, and finally falls back to the templates provided in the plugin. 

Inside of your theme, create a directory called 'musicidb-calendar'. Then copy the template that you want to override into that directory. The list templates are in the plugin directory, under wp-content/plugins/musicidb-calendar, and the files you can override are: 

* musicidb-compact-events-list.php
* musicidb-full-events-list.php
* musicidb-event-detail.php

== Screenshots ==

1. Event list with pics layout (popular for venues - dark theme)
2. *NEW - Posterboard layout
3. Calendar view layout
4. *NEW - Event list with BIG pics layout
5. Simple list layout (popular for artists)
6. Event detail pop-up (tagged artist with music video)
7. Featured events slider examples
8. Shortcode generator and options
9. Plugin settings with option to add additional artists or venues
10. Creating an event
11. Generate API Key (from your Venue page on MusicIDB)

== Changelog ==
= 2.5.12 =
* Changed shortcode slugs for list styles - old slugs are still supported
* Updated log, plugin repository images, and screenshots
* Changed default shortcode output to include images on calendar
* Fixed bug with descrip shortcode attribute
* Improved shortcode generator UI
* Minor CSS updates for admin & frontend

= 2.5.11 =
* Changed filenames for existing views
* Added new "Posterboard" view
* Added new option for "large images" on List w/ Images view
* Replaced infinite loading in page builder admin previews with message

= 2.5.10 =
* Tested up to WP 6.3.2
* Tested up to PHP 8.2
* Fixed WP Core styles overriding tab styles in Add Events modal 
* Added numevents shortcode attribute to the Event List shortcode
* Fixed PHP 8.2 Deprecation warnings

= 2.5.9 = 
* Tested for WordPress 6.0
* Modified featured events slider for adaptive height
* Minor cleanups and improvements

= 2.5.8 = 
* Tested with latest WordPress versions
* Improved some of the back-end code for validation

= 2.5.7 = 
* Fixed a theme conflict with load more button

= 2.5.6 =
* Fixed a bug saving API key for first-time visitors

= 2.5.5 =
* Resolved mobile issues for some cases

= 2.5.4 =
* Improved styles on event list and event detail pop-up

= 2.5.3 =
* Fixed a bug with the shortcode rendering properly

= 2.5.2 =
* Improved some layout details on Artists View

= 2.5.0 =
* Artist API Keys implemented
* Artist simple event list view added
* Support for multiple entities, you can add several venues or artists to any event list by finding the venue or artist ID from their search result URL on MusicIDB

= 2.0.0 =
* Implemented MusicIDB API v2 with more efficient database queries and expandable options
* Fixed same-day events from disappearing after 8pm due to server time issue
* Improved security with escaping & sanitization improvements in all templates

= 1.4.3 =
* Fixed bug with featured images appearing properly in event list

= 1.4.2 =
* Fixed bug with connecting to API in some cases

= 1.4 =
* Increased max image size for Featured Events slider
* Errors no longer display when no events are upcoming
* Lazy Loading Images

= 1.3 =
* Introducing Featured Events Slider View & Shortcode

= 1.2 =
* API Key Generator has been built and implemented (See Installation instructions if you need a key)
* You can now click outside of the event details pop-up to close the pop-up
* Scrolling is now disabled in the background when the event details pop-up is open
* Fixed bug with Social Links not displaying for tagged artists on mobile view
* Fixed bug on event details pop-up - for some themes, when multiple artists were tagged, the slider arrows were not visible to scroll between tagged artists' media on desktop
* Moved "Event Link" from event details pop-up to footer of pop-up as most information is already visible and users often do not need to click off and view the event on MusicIDB
* Hiding double lines surrounding Facebook and Ticket Link in event details pop-up when there is no Facebook Link or Ticket link available
* Several other minor improvements and fixes implemented

= 1.1 =
* Fixed some non-SSL image references
* Fixed duplicate image in footer on calendar view


== Upgrade Notice ==
= 2.5.11 =
Important Notice: If you are overriding MusicIDB templates in your theme, you will need to update the filenames for those templates after this upgrade. The filenames have changed. Affected files are musicidb-view-compact.php and musicidb-view-full.php.

= 2.5.0 =
Introduces artist views among other fixes and improvements

= 2.0.0 =
Implements the improved MusicIDB API v2, fixes issue with same-day events disappearing before midnight among other security improvements and updates
