<?php

class UserDAL extends DAL {
	/**
     * Adds a user to database
	 * @param  string  name
	 * @return int     the inserted ID in database
     */
    public function addUser($name) {
        $name = SQLFunctions::SQLText($name);
		
        $sql = "INSERT INTO User (Name)
        		VALUES (". $name .")";
        $this->doQuery($sql, false);
		return mysql_insert_id($this->connection);
    }

    /**
     * Gets a user from database if it exists, otherwise return empty array
     * @param  string                                               name
     * @return array of stdClass objects containing one or empty    the User object saved in database
     */
    public function getUser($name) {
        $name = SQLFunctions::SQLText($name);
        
        $sql = "SELECT * FROM User
                WHERE Name = ". $name;
        return $this->doQuery($sql, true);
    }
}