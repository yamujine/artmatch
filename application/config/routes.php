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
$route['artworks/edit/(:num)'] = 'artworks/edit/$1';

$route['places/(:num)'] = 'places/detail/$1';
$route['places/upload'] = 'places/edit';
$route['places/edit/(:num)'] = 'places/edit/$1';

$route['users/me'] = 'users/me'; // Don't change ordering
$route['users/(:any)'] = 'users/detail/$1'; // Don't change ordering

$route['api/comments'] = 'commentapi';
$route['api/comments/delete'] = 'commentapi/delete';
$route['api/comments/insert'] = 'commentapi/insert';
$route['api/comments/update'] = 'commentapi/update';

$route['api/artworks'] = 'artworkapi';
$route['api/places'] = 'placeapi';

$route['api/pick']['POST'] = 'pickapi';

$route['api/login']['POST'] = 'usersapi/login';
$route['api/users/register']['POST'] = 'usersapi/register';
$route['api/users/verify'] = 'usersapi/verify';
$route['api/users/check_username'] = 'usersapi/check_username';
$route['api/users/check_email'] = 'usersapi/check_email';
$route['api/users/update_image']['POST'] = 'usersapi/update_profile_image';
$route['api/users/change_password']['POST'] = 'usersapi/update_password';
$route['api/users/reset_password'] = 'usersapi/reset_password';
$route['api/landing'] = 'landingapi';
