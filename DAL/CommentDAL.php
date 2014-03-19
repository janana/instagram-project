<?php

class CommentDAL extends DAL {
	/**
     * Adds a comment in database
     * @param  int      accountID           from database
     * @param  int      postID              from database
	 * @param  string   instagramCommentID  
     * @param  string   text
	 * @param  datetime createdTime      
	 * @param  string   username
     * @param  string   profilePicURL
     */
    public function addComment($accountID, $postID, $instagramCommentID, $text, $createdTime, $username, $profilePicURL) {
		$accountID = SQLFunctions::SQLNumber($accountID);
        $postID = SQLFunctions::SQLNumber($postID);
		$instagramCommentID = SQLFunctions::SQLText($instagramCommentID);
		$text = SQLFunctions::SQLText($text);
		$createdTime = SQLFunctions::SQLDate($createdTime);
		$username = SQLFunctions::SQLText($username);
		$profilePicURL = SQLFunctions::SQLText($profilePicURL);
		
        $sql = "INSERT INTO Instagram_Comment (InstagramCommentID, PostID, AccountID, Text, CreatedTime, Username, ProfilePicURL) 
        		VALUES (" . $instagramCommentID . ", " . $postID . ", " . $accountID . ", " . $text . ", " . $createdTime . ", " . $username . ", " . $profilePicURL . ")
        		ON DUPLICATE KEY UPDATE Text = " . $text . ", Username = ". $username .", ProfilePicURL = " . $profilePicURL;
        $this->doQuery($sql, false);
    }

    public function addSelfComment($instagramPostID, $instagramCommentID, $text, $createdTime, $username, $profilePicURL) {
        
        $instagramPostID = SQLFunctions::SQLText($instagramPostID);
        $instagramCommentID = SQLFunctions::SQLText($instagramCommentID);
        $text = SQLFunctions::SQLText($text);
        $createdTime = SQLFunctions::SQLDate($createdTime);
        $username = SQLFunctions::SQLText($username);
        $profilePicURL = SQLFunctions::SQLText($profilePicURL);

        $IDs = $this->doQuery("SELECT AccountID, PostID FROM Instagram_Post WHERE InstagramPostID = " . $instagramPostID, true);
        $accountID = $IDs[0]->AccountID;
        $postID = $IDs[0]->PostID;

        $sql = "INSERT INTO Instagram_Comment (InstagramCommentID, PostID, AccountID, Text, CreatedTime, Username, ProfilePicURL) 
                VALUES (" . $instagramCommentID . ", " . $postID . ", " . $accountID . ", " . $text . ", " . $createdTime . ", " . $username . ", " . $profilePicURL . ")
                ON DUPLICATE KEY UPDATE Text = " . $text . ", Username = ". $username .", ProfilePicURL = " . $profilePicURL;
        $this->doQuery($sql, false);
    }

	/**
     * Gets the comments on a post from database
     * @param   int     postID
     * @return  array of objects from database (comments)
     */
    public function getComments($postID) {
        $postID = SQLFunctions::SQLNumber($postID);
        $sql = "SELECT * FROM Instagram_Comment WHERE PostID = " . $postID;
        $result = $this->doQuery($sql, true);
        return $result;
    }

    /**
     * Deletes the comments on a post from database
     * @param   string     instagramCommentID
     */
    public function deleteComment($instagramCommentID) {
        $instagramCommentID = SQLFunctions::SQLText($instagramCommentID);
        $sql = "DELETE FROM Instagram_Comment WHERE InstagramCommentID = " . $instagramCommentID;
        $this->doQuery($sql, false);
    }
}