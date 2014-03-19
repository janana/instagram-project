<?php

class Post {
	/**
	 * Upper cased first letter because thats how the objects from the database are
	 * @param string 		ID from instagram
	 * @param int 			ID auto incremented from database
	 * @param timestamp 	From instagram - is converted to DateTime
	 * @param int 			Count
	 * @param string 		Comment from user in pic. can be empty
	 * @param string 		Image/video URL
	 * @param tinyint		1 if the user has liked the post, else 0
	 */
	public function __construct($instagramPostID, $accountID, $createdTime, $text, $postURL, $likesCount, $userHasLiked) {
		$this->InstagramPostID = $instagramPostID;
		$this->AccountID = $accountID;
		$this->CreatedTime = date("Y-m-d H:i:s", $createdTime);
		$this->Text = $text;
		$this->PostURL = $postURL;
		$this->LikesCount = $likesCount;
		$this->UserHasLiked = $userHasLiked;
		$this->Comments = array();
	}
	/**
	 * @param array of comment objects
	 */
	public function addComments($comments) {
		$this->Comments = $comments;
	}
	public function addID($databaseID) {
		$this->PostID = $databaseID;
	}
}