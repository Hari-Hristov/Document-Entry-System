<?php

$routes = [

    'auth' => [
        'loginForm' => 'AuthController@loginForm',
        'login' => 'AuthController@login',
        'logout' => 'AuthController@logout',
        'register' => 'AuthController@register',
    ],

    'document' => [
        'uploadForm' => 'DocumentController@uploadForm',
        'upload' => 'DocumentController@upload',
        'search' => 'DocumentController@search',
        'status' => 'DocumentController@status',
    ],

    'admin' => [
        'dashboard' => 'AdminController@dashboard',
        'archive' => 'AdminController@archive',
        'togglePriority' => 'AdminController@togglePriority',
        'togglePause' => 'AdminController@togglePause',
    ],
    
    'responsible' => [
        'dashboard' => 'ResponsibleController@dashboard',
        'accept' => 'ResponsibleController@accept',
        'reject' => 'ResponsibleController@reject',
    ],

];
