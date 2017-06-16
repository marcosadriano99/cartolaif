<?php

	session_start();
	require_once 'Facebook/autoload.php';
	//https://github.com/facebook/php-graph-sdk
	
	$fb = new \Facebook\Facebook([
	'app_id' => '432522120456704', //id do app lá na conf.
	'app_secret' => 'e72b728d7ab28970abd1b38813ade1f7', //chave secreta
	'default_graph_version' => 'v2.9',
	
	//'default_access_token' => '{access-token}', // optional
]);

?>