<?php

require_once("DAL/DAL.php");
require_once("DAL/UserDAL.php");
require_once("HTMLBuilder.php");

session_start();

if (isset($_POST["name"])) {
	$name = $_POST["name"];
	$userDAL = new UserDAL();
	$user = $userDAL->getUser($name);
	$userDAL->close();
	
	if (empty($user)) {
		HTML::displayHTMLPage(HTML::getFormHTML(false, "A user with that name does not exist")); // login page
	} else { 
		// User is successfully logged in
		$_SESSION["UserID"] = $user[0]->UserID;
		header("Location: demo.php");
	}
} else if (isset($_GET["register"]) && isset($_POST["registerButton"])) {
	if ($_POST["registerName"] != "") {
		$name = $_POST["registerName"];
		$userDAL = new UserDAL();
		$user = $userDAL->getUser($name);
		if (empty($user)) {
			// Save user in database
			$newUserID = $userDAL->addUser($name);
			if ($newUserID != false) {
				header("Location: index.php?registerSuccess=true");
			} else {
				HTML::displayHTMLPage(HTML::getFormHTML(true, "The user could not me created, something went wrong")); // register page
			}
		} else {
			HTML::displayHTMLPage(HTML::getFormHTML(true, "A user with that name already exist")); // register page
		}
	} else {
		HTML::displayHTMLPage(HTML::getFormHTML(true, "Name must be filled")); // register page
	}

} else if (isset($_GET["register"]) && $_GET["register"] == true) {
	HTML::displayHTMLPage(HTML::getFormHTML(true)); // register page

} else if (isset($_GET["registerSuccess"])) {
	HTML::displayHTMLPage(HTML::getFormHTML(false, "User has been registered")); // login page
} else {
	// Display login page
	HTML::displayHTMLPage(HTML::getFormHTML(false));
}