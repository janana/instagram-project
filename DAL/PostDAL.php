<?php

class PostDAL extends DAL {
	/**
     * Adds a post in database, if instagramPostID already exists, it updates the other values. Returns the new ID from database, or the affected ID if updated.
     * @param  int      accountID   from database
	 * @param  string   instagramPostID
	 * @param  datetime createdTime
	 * @param  string   text
	 * @param  string   postURL
     * @param  int      likesCount
	 * @param  tinyint  userHasLiked
	 * @return int      the inserted posts ID in database
     */
    public function addPost($accountID, $instagramPostID, $createdTime, $text, $postURL, $likesCount, $userHasLiked) {
        $accountID = SQLFunctions::SQLNumber($accountID);
		$instagramPostID = SQLFunctions::SQLText($instagramPostID);
		$createdTime = SQLFunctions::SQLDate($createdTime);
		$text = SQLFunctions::SQLText($text);
		$postURL = SQLFunctions::SQLText($postURL);
        $likesCount = SQLFunctions::SQLNumber($likesCount);
		$userHasLiked = SQLFunctions::SQLNumber($userHasLiked);
		
        $sql = "INSERT INTO Instagram_Post (InstagramPostID, AccountID, CreatedTime, Text, PostURL, LikesCount, UserHasLiked) 
        		VALUES (" . $instagramPostID . ", " . $accountID . ", " . $createdTime . ", " . $text . ", " . $postURL . ", " . $likesCount . ", " . $userHasLiked . ")
        		ON DUPLICATE KEY UPDATE Text = " . $text . ", LikesCount = " . $likesCount . ", UserHasLiked = " . $userHasLiked;
        $this->doQuery($sql, false);
		if (mysql_insert_id($this->connection) == 0) {
			$id = $this->doQuery("SELECT PostID FROM Instagram_Post WHERE InstagramPostID = ". $instagramPostID, true);
			return $id[0]->PostID;
		}
		return mysql_insert_id($this->connection);
    }

    /**
     * Updates the UserHasLiked-field on post in database
     * @param  string   $instagramPostID  ID from instagram
     * @param  tinyint  $liked            1 if user has liked, 0 if user has disliked their like
     * @return int                        The new count of likes set
     */
    public function updateLike($instagramPostID, $liked) {
        $instagramPostID = SQLFunctions::SQLText($instagramPostID);
        $liked = SQLFunctions::SQLNumber($liked);

        $addOrSubtract = "+";
        if ($liked == 0) {
            $addOrSubtract = "-";
        }
        $sql = "UPDATE Instagram_Post SET UserHasLiked = " . $liked . ", LikesCount = LikesCount ". $addOrSubtract ."1 WHERE InstagramPostID = " . $instagramPostID;
        $this->doQuery($sql, false);

        $sql = "SELECT LikesCount FROM Instagram_Post WHERE InstagramPostID = " . $instagramPostID;
        $count = $this->doQuery($sql, true);
        return $count[0]->LikesCount;
    }

	/**
     * Gets all the posts from an account in database
     * @param   int     accountID
     * @return  array   of objects from database (Posts)
     */
    public function getPosts($accountID) {
        $accountID = SQLFunctions::SQLNumber($accountID);
        $sql = "SELECT * FROM Instagram_Post WHERE AccountID = " . $accountID;
        $result = $this->doQuery($sql, true);
        return $result;
    }

    /**
     * Gets the database IDs from the Instagram post ID
     * @param   string     instagramPostID
     * @return  array      of objects from database (AccountID and PostID)
     */
    public function getPostIDs($instagramPostID) {
        $instagramPostID = SQLFunctions::SQLText($instagramPostID);
        $sql = "SELECT PostID, AccountID FROM Instagram_Post WHERE InstagramPostID = " . $instagramPostID;
        $result = $this->doQuery($sql, true);
        return $result[0];
    }

}