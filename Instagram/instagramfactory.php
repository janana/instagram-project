<?php
require_once("instagram.php");

class InstagramFactory {
    private static $clientID = '';
    private static $clientSecret = '';
    private static $redirectURI = '';
    private static $scope = array('likes', 'comments');

    public static function create() {
        return(new Instagram(self::$clientID, self::$clientSecret, self::$redirectURI, self::$scope));
    }
}
?>