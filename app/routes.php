<?php

$Route->get('/', 'Home@index');
$Route->prefix('admin')->group(function() use($Route){
	$Route->get('/', function(){
		echo 'Home';
	});
	
	$Route->get('/list', function(){
		echo 'List';
	});
});
$Route->get('/users', 'Home@index');
$Route->notfound('/', function(){
	return "404";
});