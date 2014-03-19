<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once("instagramfactory.php");
require_once("DAL/db.php");
session_start();

if (!isset($_GET['error']) && isset($_GET['code'])) {
	$code = $_GET['code'];
	$instagram = InstagramFactory::create();

	$accessData = $instagram->getAccessData($code);
	$_SESSION['ACCESSDATA'] = serialize($accessData);

	$db = new db($instagram);
	$accountID = $db->saveAccount($_SESSION["userID"]);
	$db->savePosts($accountID);

	if(isset($_SESSION['ACCESSDATA'])) header('Location: demo.php');
} else {
	// The user was denied access from Instagram - Show error-site
	header('Location: error.html');
}

