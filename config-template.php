<?php
/**
 * Tsugi Configuration Template
 *
 * This file serves as a template for the Tsugi configuration.
 * The docker-entrypoint.sh script uses environment variables to generate
 * the actual config.php file at runtime.
 *
 * For manual configuration, copy this to config.php and modify as needed.
 *
 * Documentation: http://do1.dr-chuck.com/tsugi/phpdoc/Tsugi/Config/ConfigInfo.html
 */

$CFG = new \stdClass();

// =============================================================================
// DATABASE CONFIGURATION
// =============================================================================

// Database connection string
// Format: mysql:host=hostname;port=port;dbname=database
$CFG->pdo = 'mysql:host=127.0.0.1;port=3306;dbname=tsugi';

// Database credentials
$CFG->dbuser = 'ltiuser';
$CFG->dbpass = 'ltipassword';

// Database table prefix (allows multiple Tsugi instances in one database)
$CFG->dbprefix = '';

// Optional: SSL connection to database
// $CFG->pdo_options = array(
//     PDO::MYSQL_ATTR_SSL_CA => '/path/to/ca-cert.pem',
// );

// =============================================================================
// APPLICATION URL CONFIGURATION
// =============================================================================

// The base URL for the Tsugi installation
// IMPORTANT: This must be set correctly for LTI signatures to work
$CFG->wwwroot = 'http://localhost:8080/tsugi';

// If you want Tsugi to serve as a "home" application, set this
// Otherwise, leave as false
$CFG->apphome = false;

// Service name displayed in the UI
$CFG->servicename = 'TSUGI';

// =============================================================================
// ADMIN CONFIGURATION
// =============================================================================

// Admin password - can be plaintext or SHA256 hash
// To generate a hash: echo -n "yourpassword" | sha256sum
$CFG->adminpw = 'admin';

// =============================================================================
// DEVELOPER MODE
// =============================================================================

// Enable developer mode to:
// - Show the test harness for launching tools
// - Display developer menus and debug information
// Set to false in production!
$CFG->DEVELOPER = true;

// =============================================================================
// SECURITY SECRETS
// =============================================================================

// IMPORTANT: Change all of these in production!

// Secret for encrypting persistent login cookies
$CFG->cookiesecret = 'change-me-cookie-secret';

// Salt for session ID hashing (prevents predictable session IDs)
$CFG->sessionsalt = 'change-me-session-salt';

// Secret for bulk mail operations
$CFG->mailsecret = 'change-me-mail-secret';

// =============================================================================
// TIMEZONE
// =============================================================================

// Set the default timezone
// See: https://www.php.net/manual/en/timezones.php
$CFG->timezone = 'UTC';

// =============================================================================
// DATA STORAGE
// =============================================================================

// Directory for blob storage (alternative to database storage)
// Must be writable by the web server
$CFG->dataroot = '/tsugi-data/blobs';

// Git command location (for Git-based tool management)
$CFG->git_command = '/usr/bin/git';

// =============================================================================
// TOOL MANAGEMENT
// =============================================================================

// Folders containing Tsugi tools
$CFG->tool_folders = array("admin", "mod");

// Location for newly installed tools
$CFG->install_folder = $CFG->dirroot.'/mod';

// Hide tools matching this regex from non-admin users
// $CFG->storehide = '/(broken|test)/';

// =============================================================================
// GOOGLE SERVICES (OPTIONAL)
// =============================================================================

// Google OAuth 2.0 credentials
// $CFG->google_client_id = 'your-client-id.apps.googleusercontent.com';
// $CFG->google_client_secret = 'your-client-secret';

// Google Maps API key
// $CFG->google_map_api_key = 'your-maps-api-key';

// Enable Google Translate
// $CFG->google_translate = true;

// =============================================================================
// DATA RETENTION
// =============================================================================

// Number of days to retain data (0 = never expire)
$CFG->expire_pii_days = 0;      // Personally identifiable information
$CFG->expire_user_days = 0;      // User accounts
$CFG->expire_context_days = 0;   // Course/context data

// =============================================================================
// COOKIE SETTINGS
// =============================================================================

$CFG->cookiename = 'TSUGI';
$CFG->cookiepad = '196896';

// =============================================================================
// MAIL SETTINGS
// =============================================================================

// Mail domain for sending emails
$CFG->maildomain = false;

// Line ending for emails
$CFG->maileol = "\n";

// =============================================================================
// LTI SETTINGS
// =============================================================================

// Maximum launch count per session
$CFG->launchactivity = 100;

// Enable event checking
$CFG->eventcheck = true;

// Nonce expiration time in seconds
$CFG->noncetime = 1800;

// Nonce clearing factor
$CFG->noncecheck = 100;

// =============================================================================
// UI CUSTOMIZATION
// =============================================================================

// Bootswatch theme (leave false for default)
// Options: cerulean, cosmo, cyborg, darkly, flatly, journal, litera, lumen,
//          lux, materia, minty, morph, pulse, quartz, sandstone, simplex,
//          sketchy, slate, solar, spacelab, superhero, united, vapor, yeti, zephyr
$CFG->bootswatch = false;
$CFG->bootswatch_color = false;

// =============================================================================
// MAINTENANCE MODE
// =============================================================================

// Set to true to enable maintenance mode
$CFG->upgrading = false;

// =============================================================================
// SSL VERIFICATION
// =============================================================================

// Verify SSL certificates for outgoing connections
// Set to false only for development with self-signed certificates
$CFG->verifypeer = true;

// =============================================================================
// CACHING (OPTIONAL)
// =============================================================================

// Memcache for session storage
// $CFG->memcache = 'tcp://localhost:11211';

// Memcached for session storage
// $CFG->memcached = 'localhost:11211';

// Redis for session storage
// $CFG->redis = 'tcp://localhost:6379';

// =============================================================================
// WEBSOCKET (OPTIONAL)
// =============================================================================

// WebSocket server for real-time features
// $CFG->websocket_url = 'ws://localhost:8080/websocket';
