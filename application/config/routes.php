<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'main';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Route for our service
$route['artworks/(:num)'] = 'artworks/detail/$1';
$route['artworks/upload'] = 'artworks/edit';
$route['artworks/(:num)/edit'] = 'artworks/edit/$1';
$route['artworks/(:num)/delete'] = 'artworks/delete/$1';

$route['places/(:num)'] = 'places/detail/$1';
$route['places/upload'] = 'places/edit';
$route['places/(:num)/edit'] = 'places/edit/$1';
$route['places/(:num)/delete'] = 'places/delete/$1';
$route['places/(:num)/exhibitions'] = 'places/exhibitions/$1';

$route['exhibitions/(:num)/apply'] = 'exhibitions/apply/$1';

$route['users/(:any)'] = 'users/detail/$1';
$route['users/(:any)/picks'] = 'users/picks';
$route['users/(:any)/apply_status'] = 'users/apply_status';

$route['api/comments'] = 'commentApi';
$route['api/comments/delete'] = 'commentApi/delete';
$route['api/comments/insert'] = 'commentApi/insert';
$route['api/comments/update'] = 'commentApi/update';

$route['api/contents/list'] = 'main/api';
$route['api/contents/change_comment_option'] = 'contentsApi/change_comment_option';
$route['api/contents/delete_extra_images']['POST'] = 'contentsApi/delete_extra_images';

$route['api/pick']['POST'] = 'pickApi';

$route['api/login']['POST'] = 'usersApi/login';
$route['api/users/register']['POST'] = 'usersApi/register';
$route['api/users/verify'] = 'usersApi/verify';
$route['api/users/check_username'] = 'usersApi/check_username';
$route['api/users/check_email'] = 'usersApi/check_email';
$route['api/users/update_image']['POST'] = 'usersApi/update_profile_image';
$route['api/users/change_password']['POST'] = 'usersApi/change_password';
$route['api/users/reset_password']['POST'] = 'usersApi/reset_password';

$route['api/exhibition/accept']['POST'] = 'exhibitionApi/accept';
$route['api/exhibition/create']['POST'] = 'exhibitionApi/create';
$route['api/exhibition/delete']['POST'] = 'exhibitionApi/delete';
$route['api/exhibition/update']['POST'] = 'exhibitionApi/update';
