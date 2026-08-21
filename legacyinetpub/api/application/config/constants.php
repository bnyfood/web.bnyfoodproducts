<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/

$storage_root = $_SERVER['DOCUMENT_ROOT'];
$storage_base = dirname($storage_root) . '\\';
$storage_path = $storage_base."storage\bnyfoodproducts";

define('APP_STORE_PATH',$storage_path);

defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code
define('ADMINBYPADDKEY','cVH8dlb8iyot8iyot');
define('KEY_ENCRYPTION','yoouhCyowxFigiupo8ishdbouh');
define('BNY_ESTABLISHDATETIME','2019-01-01T00:00:00+0700');
define('LAZADA_CODE','0_123793_l5bnHWN7l1LnfeAAmN24ry8e58813');
define('PAGINATION_SIZE','100');

define('SALT_PASSWORD','l;ylfu;yoxu.s,jrk2238');
define('DATE_TIME_NOW',date('Y-m-d H:i:s'));
define('DATE_NOW',date('Y-m-d'));

//API
define('API_FRQUENCY_LIMIT',300);
define('API_SAMPLING_LIMIT',1);

define('TOKEN_PERIOD_LIMIT',180);

define('API_TOKEN_KEY','ewrwerwer5wer8786adsadsdfht');
define('API_TOKEN_KEY2','yeripusdflkw478wsgadsfgaeet');
define('API_TOKEN_KEY3','ertepit5asdfu5646rtyyerwyiop');


// MENU ID

define('MENU_ID_SETTING',1);
define('MENU_ID_DASHBOARD',26);
define('MENU_ID_GROUP',30);
define('MENU_ID_EMPLOYEE',31);
define('MENU_ID_SHOP',37);


define('GEN_CODE_KEY','epvm12l');
define('GEN_CODE_KEY2','gtyutyu789k');
define('GEN_CODE_KEY3','uiou8hh');


define('TEMP_GROUP',20);

define('SMS_API_KEY','mGgA8ecmTrEwEw3BU548kNBzhfK_U_');
define('SMS_SECRET_KEY','g3d0NyvfkOHVm2EmVwt5YtO2qjfXOd');

define('COOKIE_PREFIX','cookie_intranet_');
define('SESSION_PREFIX','session_intranet_');
