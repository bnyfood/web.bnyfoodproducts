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
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

$storage_root = $_SERVER['DOCUMENT_ROOT'];
$storage_base = dirname($storage_root) . '\\';
$storage_path = $storage_base."storage\bnyfoodproducts";

define('APP_STORE_PATH',$storage_path);

define('DATE_TIME_NOW',date('Y-m-d H:i:s'));
define('DATE_NOW',date('Y-m-d'));

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
define('BNY_ESTABLISHDATETIME','2019-01-01');
define('SHOPEE_START_DATE','2021-07-20T00:00:00+0700');
define('BNY_ESTABLISHDATE','2019-04-19');
define('LAZADA_CODE','0_123793_l5bnHWN7l1LnfeAAmN24ry8e58813');
define('SHOPEE_PATNERKEY','5413e433a5b83c2bc82f06b4384bcab0c9595e09bcf6566d5ee43f85abaa5250');
define('SHOPEE_PATNERID',2001584);
define('BNY_SUBSCRIPTION_SHOPID',123456);
define('SHOPEE_APIURL','https://partner.shopeemobile.com');
define('BNY_LAZ_CUT_ORDER_TIME','15:30');

define('TIKTOK_API_URL','https://open-api.tiktokglobalshop.com');
define('TIKTOK_KEY','6d2girb78e6hg');
define('TIKTOK_SECRET','f2101657ef5c119fb6343dfa1fbc2cf0844529f8');
define('TIKTOK_SHOP_CIPHER','ROW_1jx59wAAAAC6uXC_JINwLhHPC1xsyhWH');

define('PAGINATION_SIZE','100');

define('BNY_API_URL','https://api.bnyfoodproducts.com/');
define('SALT_PASSWORD','l;ylfu;yoxu.s,jrk2238');
define('API_TOKEN_KEY','ewrwerwer5wer8786adsadsdfht');
define('API_TOKEN_KEY2','yeripusdflkw478wsgadsfgaeet');
define('API_TOKEN_KEY3','ertepit5asdfu5646rtyyerwyiop');

define('USER_GROUP_DEFULT_GROUP',3);
define('USER_GROUP_USERTMP',2);

//----menu id----
define('MENU_DASHBOARD',26);	
define('MENU_ACCOUNT',29);
define('MENU_CONFIG_MENU',28);	
define('MENU_CONFIG_USERGROUP',30);	
define('MENU_CONFIG_USER',31);	
define('MENU_MANUFACTURE_BRAND',41);	
define('MENU_MANUFACTURE_SUPPLIER',42);	

define('MENU_ACCOUNT_TAXINVOICE',34);	
define('MENU_ACCOUNT_SALETAXREPORT',43);	
define('MENU_ACCOUNT_CREDITNOTE',44);	
define('MENU_ACCOUNT_CREDITREPORT',45);	
define('MENU_ACCOUNT_TRACKINGREPORT',46);	
//----menu id-----

define('GEN_CODE_KEY','epvm12l');
define('GEN_CODE_KEY2','gtyutyu789k');
define('GEN_CODE_KEY3','uiou8hh');

//mulit upload
define('IS_AJAX', isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

//cache
define('CAHCH_SEC',3600);	

//SMS API

define('SMS_API_KEY','mGgA8ecmTrEwEw3BU548kNBzhfK_U_');
define('SMS_SECRET_KEY','g3d0NyvfkOHVm2EmVwt5YtO2qjfXOd');

//Google key

define('GOOGLE_Client_ID','324318261967-nc4bbh638m1d4m5hb0cacp3oq6rl0mbj.apps.googleusercontent.com');
define('GOOGLE_SECRET_KEY','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');
define('GOOGLE_LOGIN_REDIRECT','https://www.bnyfoodproducts.com/users/login_with_google');
define('GOOGLE_LOGIN_SUCCESS_REDIRECT','https://www.bnyfoodproducts.com/users/logined_with_google');
define('CONTENT_PER_PAGE',10);


//google captcha
define('GOOGLE_CAPTCHA_SITEKEY','6Ld30ZUpAAAAAOcOHhquWryugAWhhUggqn-enjl7');
define('GOOGLE_CAPTCHA_SECRETKEY','6Ld30ZUpAAAAAB0c_U1wju23b5ail1SGo005Ursq'); 

//Cookie
define('COOKIE_PREFIX','cookie_bnyfood_');
define('SESSION_PREFIX','session_bnyfood_');
