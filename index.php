<?php
require './class/_all.php';

session_start();
$DB = new DataBase();
$data['cssVersion'] = CSS_VERSION;
$data['someStuff'] = 'Some data';
$data['arrayExample'] = [
	'name'=> "Ehsan",
	'family' => "Seyedi"
];

// Render View with passing some data
// You can access this data in view file as $data
route('GET', '/', function () {
	return response(
		phtml(__DIR__.'/views/index')
	);
});

// Passing multiple data to view or destination file
route('GET', '/example1', function () {
	global $data;
	$data['title'] = 'Page title';
	$otherData = [
		'h1Text' => "Hello",
		'h2Text' => "This is an example",
		'pText' => "You can pass multiple data to destination file"
	];

	return response(
		phtml(__DIR__.'/views/index', [
			'data' => $data ,
			'otherData' => $otherData
			]
		)
	);
});

// -------------
// API Examples:
// -------------

// Handleing GET request
route('GET','/api/user',function(){
	global $DB;
	return response(
		json_encode(
			$DB->select(
				"SELECT * FROM `users`"
			)
		), // Page content
		200,    // Status Code
		['content-type' => 'application/json']  // Additional response headers
	);
});
route('GET','/api/user/:id',function($args){
	global $DB;
	return response(
		json_encode(
			$DB->selectOne(
				"SELECT * FROM `users` WHERE `id` = ?", //Or `id` = :id
				[ $args['id'] ] // Or [ ':id' => $args['id'] ]
			)
		),
		200,
		['content-type' => 'application/json']
	);
});

// Handleing POST request
route('POST','/api/insertExample',function(){
	global $DB;
	$insertt = [
		'username' => $_POST['username'],
		'email' => $_POST['email'],
		'password' => $_POST['password'],
		'name' => $_POST['name']
	];
	return response(
		json_encode(
			$USER->insert(
				'users',
				$insertt
			)
		),
		200,
		['content-type' => 'application/json']
	);
});


// Utils
// Minify CSS & JS filse in given path
// For sub-directories, you can see this example down here
// any files like style.css will minify and save to style.min.css

// Open this urls, you can remove or comment these lines before release
// /utils/minify/css
// /utils/minify/js
route('GET','/utils/minify/:format',function($args){
	$dirs['css'] = [
		'assets/css',
		'assets/css/snippet'
	];
	$dirs['js'] = [
		'assets/js',
		'assets/js/snippet'
	];
	$Minifier = new Minifier();
	$res = $Minifier->minify($dirs[ $args['format'] ], $args['format']);
	return response(
		json_encode($res),
		200,
		['content-type' => 'application/json']
	);
});

// Handle 404 Page
$notFountURL = '/'.trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
route('GET', $notFountURL, function () {
	global $data;

	return response(
		phtml(__DIR__.'/views/index', ['data' => $data ])
	);
});

dispatch();