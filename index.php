<?php
/**
 * Copyright (c) Martin Srank, http://smasty.net
 * Licensed under the terms of the MIT license - http://opensource.org/licenses/mit-license
 */


try{

	require_once __DIR__ . '/neevo/neevo.php';

	// Configure database connection
	$db = new Neevo(array(
		'driver' => 'sqlite3',
		'file' => __DIR__ . '/urly.sqlite'
	));
	define('TABLE', 'urly');

	function isUrl($url){
		return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0-9_-]*(?:\.[A-Z0-9][A-Z0-9_-]*)+):?(\d+)?\/?/i', $url);
	}


	// Redirect
	if(isset($_GET['q']) && ($url = $db->select(':url', TABLE)
		->where(':id = %i', base_convert($_GET['q'], 36, 10))
		->fetchSingle()) !== false)
	{
		header("Location: $url");
	}


	// Save URL
	elseif(isset($_POST['url']) && isUrl(($url = $_POST['url']))){
		if(($id = $db->select(':id', TABLE)->where('url', $url)->fetchSingle()) === false){
			$id = $db->insert(TABLE, array('url' => $url))->insertId();
		}
		if($id){
			$id = base_convert($id, 10, 36);
			$path = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]$id";
			echo "Your short URL is <a href=\"$path\">$path</a>";
		} else{
			throw new Exception;
		}
	}


	// Show <form>
	else{
		echo
<<<FORM
<form action="" method=post style="width:400px;margin:3em auto">
	Your long URL:<br>
	<input name=url size=50>
	<input type=submit>
</form>
FORM;
	}


// Catch all possible errors.
} catch(Exception $e){
	die("Error processing your request. Try again later.");
}