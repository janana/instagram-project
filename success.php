<?php
require_once("Instagram/instagramfactory.php");
require_once("DAL/DataHandler.php");
session_start();

if (!isset($_GET['error']) && isset($_GET['code'])) {
	$code = $_GET['code'];
	$instagram = InstagramFactory::create();

	$accessData = $instagram->getAccessData($code);
	$_SESSION['ACCESSDATA'] = serialize($accessData);

	$dataHandler = new DataHandler($instagram);
	$accountID = $dataHandler->saveAccount($_SESSION["userID"]);
	$dataHandler->savePosts($accountID);

	if(isset($_SESSION['ACCESSDATA'])) header('Location: demo.php');
} else {
	// The user was denied access from Instagram - Show error-site
	header('Location: error.html');
}

