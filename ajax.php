<?php

require_once("Instagram/instagramfactory.php");
require_once("Instagram/instagramcontentbuilder.php");
require_once("DAL/DataHandler.php");

session_start();

$instagram = InstagramFactory::create();

if ($_POST["function"] == "getContent") {
	$accessData = $_SESSION['ACCESSDATA'];
	$instagram->setAccessData($accessData);

	$accountID = $_POST["accountID"]; // AccountID from database 

	$dataHandler = new DataHandler($instagram);

	$posts = $dataHandler->getPosts($accountID); 
	
	$icb = new InstagramContentBuilder($instagram->getUserName());
	echo $icb->createEntries($posts); 

} else if ($_POST["function"] == "getAccountInfo") {
	$accessData = $_SESSION['ACCESSDATA'];
	$instagram->setAccessData($accessData);

	$dataHandler = new DataHandler($instagram);
	$info = $dataHandler->getAccountInfo();
	echo InstagramContentBuilder::createAccountInfoBox($info);

} else if ($_POST["function"] == "getAccounts") {
	$accountDAL = new AccountDAL();
	$accounts = $accountDAL->getUserAccounts($_POST["userID"]);
	$accountDAL->close();
	$_SESSION["userID"] = $_POST["userID"];

	echo json_encode($accounts);
} else if ($_POST["function"] == "setAccessData") {
	$accountID = $_POST["accountID"];
	$accountDAL = new AccountDAL();
	$account = $accountDAL->getAccount($accountID);
	$accountDAL->close();
	if ($account != false) {
		$account = $account[0];
		// Account exists and accessData will be saved in session.
		$accessData = (object) array("access_token" => $account->AccessToken,
												"user" => (object) array("username" => $account->Username,
																		"profile_picture" => $account->ProfilePicURL,
																		"id" => $account->InstagramAccountID));
		$_SESSION["ACCESSDATA"] = $accessData;
	} else {
		echo "Error";
	}
} else if ($_POST["function"] == "getInstagramLink") {
	echo $instagram->getOauthURI();
} else {
	if (isset($_SESSION["antiForgeryToken"]) &&
		isset($_POST["antiForgeryToken"]) &&
		$_SESSION["antiForgeryToken"] == $_POST["antiForgeryToken"]) {

		$accessData = $_SESSION['ACCESSDATA'];
		$instagram->setAccessData($accessData);

		$postID = $_POST["postID"]; // InstagramPostID - not from database

		if ($_POST["function"] == "updateLike") {
			$data = "";
			if ($_POST["like"] == 1) {
				$data = $instagram->likePost($postID); // InstagramPostID - not from database
			} else {
				$data = $instagram->unlikePost($postID); // InstagramPostID - not from database
			}
			
			if ($data->meta->code == 200) {
				// Update succeeded - save in database
				$postDAL = new PostDAL();
				$likesCount = $postDAL->updateLike($postID, $_POST["like"]); // like = 1/0
				$postDAL->close();
				$likeCountHTML = InstagramContentBuilder::printLikers($likesCount, $_POST["like"], $instagram->getUserName());
				echo $likeCountHTML;
			} else {
				echo "Error";
			}
		} else if ($_POST["function"] == "commentPost") {
			$text = $_POST["text"];

			// Try to comment post on Instagram
			$data = $instagram->commentPost($postID, $text); // InstagramPostID - not from database
			
			if ($data->meta->code == 200) { // The commenting was successful, check the post from Instagram if comment has been commented
				$post = $instagram->getPost($postID);
				if ($post->meta->code == 200) { // The post was loaded successfully
					$comments = $post->data->comments->data;
					$comment = "";
					foreach ($comments as $c) {
						if ($c->text == $text && $c->from->username == $instagram->getUserName()) {
							$comment = $c;
							break;
						}
					}
					if ($comment != "") {
						// Save comment in database
						$c = new Comment($comment->id, $postID, 0, $comment->text, $comment->created_time, $instagram->getUserName(), $instagram->getUserProfilePicture());
						
						$commentDAL = new CommentDAL();
						$commentDAL->addSelfComment($c->PostID, $c->InstagramCommentID, $c->Text, $c->CreatedTime, $c->Username, $c->ProfilePicURL);
						$commentDAL->close();

						// Get the HTML-boxes for the new comment
						$html = InstagramContentBuilder::createCommentBox($c, $postID);
						echo $html;
					} else { // The comment did not exist on the post on Instagram
						echo "Error";
					}
				}
			} else { // Could not comment the post on Instagram
				echo "Error";
			}
		} else if ($_POST["function"] == "deleteComment") {
			$instagramCommentID = $_POST["commentID"];
			$data = $instagram->deletePostComment($postID, $instagramCommentID);
			if ($data->meta->code == 200) {
				// Delete succeeded - delete in database
				$commentDAL = new CommentDAL();
				$commentDAL->deleteComment($instagramCommentID);
				$commentDAL->close();

				echo "succeeded";
			} else {
				var_dump($data);
			}
		}
	} else {
		echo "Request denied";
	}
}