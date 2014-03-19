<?php

class AccountDAL extends DAL {
	/**
     * Function for adding an account in database, if the InstagramAccountID exists, it updates the other values
	 * @param   int     userID      From database
     * @param   string  instagramAccountID
     * @param   string  accessToken
     * @param   string  profilePicURL
     * @param   string  username
     * @return  int     AccountID   From the inserted row in database. If updated, returns the affected ID
	 */
	public function addAccount($userID, $instagramAccountID, $accessToken, $profilePicURL, $username) {
        $userID = SQLFunctions::SQLNumber($userID);
        $instagramAccountID = SQLFunctions::SQLText($instagramAccountID);
        $accessToken = SQLFunctions::SQLText($accessToken);
        $profilePicURL = SQLFunctions::SQLText($profilePicURL);
        $username = SQLFunctions::SQLText($username);

		$lastUpdated = date('Y-m-d H:i:s'); // Todays date in mysql DateTime format
        $sql = "INSERT INTO Instagram_Account (InstagramAccountID, AccessToken, LastUpdated, ProfilePicURL, Username) 
                VALUES (". $instagramAccountID . ", " . $accessToken . ", NOW(), " . $profilePicURL . ", " . $username .") 
                ON DUPLICATE KEY UPDATE AccessToken = " . $accessToken . ", LastUpdated = NOW(), ProfilePicURL = " . $profilePicURL . ", Username = " . $username;
        $this->doQuery($sql, false);
		
		$lastID = mysql_insert_id($this->connection);
		
        $sql = "INSERT INTO Instagram_Account_User 
        		VALUES (" . $userID . ", " . mysql_insert_id($this->connection) . ")
        		ON DUPLICATE KEY UPDATE UserID = " . $userID;
        $this->doQuery($sql, false);
		
		if ($lastID == 0) {
			$id = $this->doQuery("SELECT AccountID FROM Instagram_Account WHERE InstagramAccountID = ". $instagramAccountID, true);
			return $id[0]->AccountID;
		}
		return $lastID;
	}
    /**
     * Function for updating the accessToken from Instagram om an account in database
     * @param   string    instagramAccountID
     * @param   string    accessToken
     */
    public function updateAccessToken($instagramAccountID, $accessToken) {
        $instagramAccountID = SQLFunctions::SQLText($instagramAccountID);
        $accessToken = SQLFunctions::SQLText($accessToken);

        $sql = "UPDATE Instagram_Account SET AccessToken = " . $accessToken . ", LastUpdated = NOW() WHERE InstagramAccountID = " . $instagramAccountID;
        $this->doQuery($sql, false);
    }
    
	/**
     * Function for getting an account from database from an AccountID
     * @param   int                                          accountID
     * @return  array of objects containing one, or empty    from database
     */
    public function getAccount($accountID) {
        $accountID = SQLFunctions::SQLNumber($accountID);
        $sql = "SELECT * FROM Instagram_Account WHERE AccountID = " . $accountID;
        return $this->doQuery($sql, true);
    }
    /**
     * Gets all the accounts from a user in database
     * @param   int     userID  from database
     * @return  array   of objects from database
     */
    public function getUserAccounts($userID) {
        $userID = SQLFunctions::SQLNumber($userID);
        $sql = "SELECT * FROM Instagram_Account INNER JOIN Instagram_Account_User ON Instagram_Account.AccountID = Instagram_Account_User.AccountID AND Instagram_Account_User.UserID = " . $userID;
        $result = $this->doQuery($sql, true);
        return $result;
    }
}