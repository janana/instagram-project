<?php

require_once("config.php");
require_once("SQLFunctions.php");

class DAL {
	/**
	 * @var Mysql connection
	 */
	protected $connection;
	
	/**
	 * connects to database
	 */
	public function __construct() {
		$this->connection = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD) or die('Could not connect: ' . mysql_error());
		mysql_select_db(DB_NAME, $this->connection) or die('Could not select database');
	}

	/**
	 * Performs a query in current database, returns the result if getResult is true
	 * @param string sql query string
	 *		 (Already escaped params in sql string)
	 * @param boolean getResult if function is expecting return value
	 * @throws Exception if SQL query fails
	 * @return array of objects from database
	 */
	public function doQuery($query, $getResult) {
		try {
			$result = mysql_query($query);
			if (!$result) {
				throw new Exception('Query failed: ' . mysql_error());
			}
			
			$return = array();
			if ($getResult) {
				while ($row = mysql_fetch_object($result)) {
					$return[] = $row;
				}
				
				mysql_free_result($result);
				return $return;
			}
		} catch (Exception $e) {
			echo $e; // Handle exception
		}
		
	}

	/**
	 * Closes the connection to the database
	 */
	public function close() {
		mysql_close($this->connection);
	}
}
