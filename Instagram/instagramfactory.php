<?php
require_once("Instagram/instagram.php");

class InstagramFactory {
	/**
	 * ClientID initiated at instagram.com/developer
	 * @var string
	 */
    private static $clientID = '';

    /**
     * Client Secret initiated at instagram.com/developer
     * @var string
     */
    private static $clientSecret = '';

    /**
     * Redirect URI initiated at instagram.com/developer
     * @var string
     */
    private static $redirectURI = '';

    /**
     * What kind of rights the application want the user to accept
     * @var array
     */
    private static $scope = array('likes', 'comments');

    /**
     * Creates an instance of the instagram-class initiated with the saved values
     * @return Instagram class
     */
    public static function create() {
        return(new Instagram(self::$clientID, self::$clientSecret, self::$redirectURI, self::$scope));
    }
}
?>