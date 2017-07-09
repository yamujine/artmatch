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

/*
|--------------------------------------------------------------------------
| Pick Art You Constants
|--------------------------------------------------------------------------
|
| These constants are used in Pick Art You web services
| 아래 상수들은 MY_Controller.php 내 Twig global variables와 동기화 필요
*/
defined('USER_TYPE_ARTIST') OR define('USER_TYPE_ARTIST', '0');
defined('USER_TYPE_PLACE_OWNER') OR define('USER_TYPE_PLACE_OWNER', '1');

defined('TYPE_ARTWORKS') OR define('TYPE_ARTWORKS', 'artworks');
defined('TYPE_PLACES') OR define('TYPE_PLACES', 'places');

/** 작품/장소 공통 */
defined('TYPE_USE_COMMENT') OR define('TYPE_USE_COMMENT', '1');
defined('TYPE_NO_COMMENT') OR define('TYPE_NO_COMMENT', '0');

/** 작품 */
defined('ARTWORK_STATUS_NEED_SPACE') OR define('ARTWORK_STATUS_NEED_SPACE', '0');
defined('ARTWORK_STATUS_NOT_NEED_SPACE') OR define('ARTWORK_STATUS_NOT_NEED_SPACE', '1');
defined('ARTWORK_FOR_SALE') OR define('ARTWORK_FOR_SALE', '1');
defined('ARTWORK_NOT_FOR_SALE') OR define('ARTWORK_NOT_FOR_SALE', '0');

/** 장소 */
defined('PLACE_STATUS_NEED_ARTWORK') OR define('PLACE_STATUS_NEED_ARTWORK', '0');
defined('PLACE_STATUS_NOT_NEED_ARTWORK') OR define('PLACE_STATUS_NOT_NEED_ARTWORK', '1');

/** 전시 */
defined('EXHIBITION_FOR_FREE') OR define('EXHIBITION_FOR_FREE', '1');
defined('EXHIBITION_PAID') OR define('EXHIBITION_PAID', '0');

/** 페이스북 로그인 */
defined('FACEBOOK_NOT_GRANTED_EMAIL_PERMISSION') OR define('FACEBOOK_NOT_GRANTED_EMAIL_PERMISSION', '301');
defined('FACEBOOK_NOT_JOINED_USER') OR define('FACEBOOK_NOT_JOINED_USER', '302');
defined('FACEBOOK_NOT_DEFINED_EXCEPTION') OR define('FACEBOOK_NOT_DEFINED_EXCEPTION', '303');

/** 지원 */
defined('APPLY_STATUS_IN_REVIEW') OR define('APPLY_STATUS_IN_REVIEW', '0');
defined('APPLY_STATUS_REJECTED') OR define('APPLY_STATUS_REJECTED', '1');
defined('APPLY_STATUS_ACCEPTED') OR define('APPLY_STATUS_ACCEPTED', '2');
