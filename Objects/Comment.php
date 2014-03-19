<?php

class Comment {
	/**
	 * Upper cased first letter because thats how the objects from the database are
	 * @param string 		ID from instagram
	 * @param int 			ID auto incremented from database
	 * @param int 			ID auto incremented from database
	 * @param string 		The comment
	 * @param timestamp 	From instagram - is converted to DateTime
	 * @param string 		Username of commentor
	 * @param string 		URL to the commentor's profile pic
	 */
	public function __construct($instagramCommentID, $postID, $accountID, $text, $createdTime, $username, $profilePicURL) {
		$this->InstagramCommentID = $instagramCommentID;
		$this->PostID = $postID;
		$this->AccountID = $accountID;
		$this->Text = $text;
		$this->CreatedTime = date("Y-m-d H:i:s", $createdTime);
		$this->Username = $username;
		$this->ProfilePicURL = $profilePicURL;
	}
}