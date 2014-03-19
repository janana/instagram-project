<?php

/**
 * Functions to call on params before sending them to db to avoid SQL injections
 */
class SQLFunctions {
	/**
	 * Text
	 * @param 	string
	 * @return 	string escaped version of input
	 */
	public static function SQLText($theText){
		return '\'' . mysql_real_escape_string($theText) . '\'';
	}
	/**
	 * Date (Same as previous function?)
	 * @param 	date string
	 * @return 	date string escaped version of input
	 */
	public static function SQLDate($theDate){
		return '\'' . mysql_real_escape_string($theDate). '\'';
	}
	/**
	 * Number
	 * @param 	int
	 * @return 	int escaped version of input
	 */
	public static function SQLNumber($theNumber){
		if (empty($theNumber)) {
			return 0;
		}
		else {
			return mysql_real_escape_string($theNumber);
		}
	}
}
