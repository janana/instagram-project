<?php

require_once("DAL/DAL.php");
require_once("DAL/UserDAL.php");
require_once("HTMLBuilder.php");
session_start();
error_reporting(E_ALL);


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

/*

$accountDAL = new AccountDAL();
$accounts = $accountDAL->getUserAccounts(1);
$accountDAL->close();

$html = "";
if (!empty($accounts)) {
	$account = $accounts[1];
	// AccessData to send to Instagram-class in the same format as sent from Instagrams API (without some excess information on the user)
	$accessData = (object) array("access_token" => $account->AccessToken,
									"user" => (object) array("username" => $account->Username,
															"profile_picture" => $account->ProfilePicURL,
															"id" => $account->InstagramAccountID));
	$instagram = InstagramFactory::Create();
	$instagram->setAccessData($accessData);
	$db = new db($instagram);
	$posts = $db->getPosts($account->AccountID); // From database
	$icb = new InstagramContentBuilder($account->Username);
	echo $icb->createEntries($posts);
	/*foreach ($accounts as $account) {
		$accessData = (object) array("access_token" => $account->AccessToken,
									"user" => (object) array("username" => $account->Username,
															"profile_picture" => $account->ProfilePicURL,
															"id" => $account->InstagramAccountID));
		var_dump($accessData);
		$html .= "<div data-account-id='{$account->InstagramAccountID}'>
					
				</div>";
	}
} else {
	// Inits a class with the correct settings, IDs and secret values
	$instagram = InstagramFactory::create();
	$html = "<a href='". $instagram->getOAuthURI() ."'>Login with Instagram</a>";
	
}
echo $html;*/



// object(stdClass)#2 (2) { 
//	 ["access_token"]=> string(50) "202332745.0a05b73.a1f1de23ae4f48fabcf87ceb0acdcacc" 
//	 ["user"]=> object(stdClass)#3 (6) { 
//	 		["username"]=> string(8) "jbananiz" 
//	 		["profile_picture"]=> string(77) "http://images.ak.instagram.com/profiles/profile_202332745_75sq_1366827264.jpg" 
//	 		["id"]=> string(9) "202332745" 
//   }
// }