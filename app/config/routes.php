<?php
// Basic routing configuration
$routes = [
    '/' => 'HomeController@index',
    '/login' => 'AuthController@login',
    '/register' => 'AuthController@register',
    '/logout' => 'AuthController@logout',
    '/forgot-password' => 'AuthController@forgotPassword',
    '/verify-otp' => 'AuthController@verifyOtp',
    '/reset-password' => 'AuthController@resetPassword',
    '/client/dashboard' => 'ClientController@dashboard',
    '/organizer/dashboard' => 'OrganizerController@dashboard',
];
