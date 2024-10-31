<?php 
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
if ( ! defined( 'MUSICIDB_PLUGIN_DIR' ) ) exit; 

// General plugin files
require_once(MUSICIDB_API_DIR . '/class-api-exception.php');
require_once(MUSICIDB_API_DIR . '/class-musicidb-client.php');

// Models 
require_once(MUSICIDB_API_DIR . '/models/common/class-musicidb-model.php');

require_once(MUSICIDB_API_DIR . '/models/common/class-address.php');
require_once(MUSICIDB_API_DIR . '/models/event/class-event-date.php');
require_once(MUSICIDB_API_DIR . '/models/artist/class-artist-link.php');
require_once(MUSICIDB_API_DIR . '/models/artist/class-artist-media.php');

require_once(MUSICIDB_API_DIR . '/models/event/class-event.php');
require_once(MUSICIDB_API_DIR . '/models/venue/class-venue.php');
require_once(MUSICIDB_API_DIR . '/models/artist/class-artist.php');
require_once(MUSICIDB_API_DIR . '/models/event/class-set.php');
require_once(MUSICIDB_API_DIR . '/models/api_key/class-api-key-permissions.php');
require_once(MUSICIDB_API_DIR . '/models/api_key/class-api-key-venue-details.php');
require_once(MUSICIDB_API_DIR . '/models/api_key/class-api-key-artist-details.php');

// Response Models 
require_once(MUSICIDB_API_DIR . '/models/response_models/class-musicidb-response.php');
require_once(MUSICIDB_API_DIR . '/models/response_models/class-event-response.php');
require_once(MUSICIDB_API_DIR . '/models/response_models/class-event-media-response.php');
require_once(MUSICIDB_API_DIR . '/models/response_models/class-venue-response.php');
require_once(MUSICIDB_API_DIR . '/models/response_models/class-artist-response.php');
require_once(MUSICIDB_API_DIR . '/models/response_models/class-api-key-response.php');

// Request Models
require_once(MUSICIDB_API_DIR . '/models/request_models/class-musicidb-request.php');
require_once(MUSICIDB_API_DIR . '/models/request_models/class-event-request.php');
require_once(MUSICIDB_API_DIR . '/models/request_models/class-venue-request.php');
require_once(MUSICIDB_API_DIR . '/models/request_models/class-artist-request.php');
require_once(MUSICIDB_API_DIR . '/models/request_models/class-api-key-request.php');

// API Accessor
require_once(MUSICIDB_API_DIR . '/class-musicidb-api.php');