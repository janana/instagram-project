<?php

require_once("DAL/DAL.php");
require_once("DAL/PostDAL.php");
require_once("DAL/CommentDAL.php");
require_once("DAL/AccountDAL.php");
require_once("Objects/Post.php");
require_once("Objects/Comment.php");

class DataHandler {
	/**
	 * @var Instagram class
	 */
	private $instagram;

	/**
	 * @param Instagram class
	 */
	public function __construct(Instagram $instagramClass) {
		$this->instagram = $instagramClass;
	}
	
	/**
	 * Gets the data needed from the account at instagram and saves it in the database
	 * @param 	int 	userID from database
	 * @return 	int 	accountID from database
	 */
	public function saveAccount($userID) {
		$instaID = $this->instagram->getUserId();
		$accessToken = $this->instagram->getAccessToken();
		$profilePic = $this->instagram->getUserProfilePicture();
		$username = $this->instagram->getUserName();
		
		// Save in DataHandler
		$accountDAL = new AccountDAL();
		$accountID = $accountDAL->addAccount($userID, $instaID, $accessToken, $profilePic, $username);
		$accountDAL->close();
		
		return $accountID;
	}

	/**
	 * Updates the accessToken from Instagram in the database via the ID from instagram
	 */
	public function updateAccessToken() {
		$instagramAccountID = $this->instagram->getUserID();
		$accessToken = $this->instagram->getAccessToken();

		$accountDAL = new AccountDAL();
		$accountDAL->updateAccessToken($instagramAccountID, $accessToken);
		$accountDAL->close();
	}

	/**
	 * Returns the user information from Instagram
	 * @return stdClass object 
	 */
	public function getAccountInfo() {
		$data = $this->instagram->getUserInfo();
		$data = $data->data;
		
		$username = $data->username;
		$profilePic = $data->profile_picture;
		$bio = $data->bio;
		$website = $data->website;
		$fullName = $data->full_name;
		$followedByCount = $data->counts->followed_by;
		$followsCount = $data->counts->follows;
		$postCount = $data->counts->media;

		return (object) array("username" => $username,
							"profilePic" => $profilePic,
							"bio" => $bio,
							"website" => $website,
							"fullName" => $fullName,
							"followedBy" => $followedByCount,
							"follows" => $followsCount,
							"postCount" => $postCount);
	}
		
	/**
	 * Gets the posts saved in database
	 * @param int AccountID from database
	 * @return array of Posts (stdClass objects) from database
	 */
	public function getPosts($accountID) {
		$postDAL = new PostDAL();
		$posts = $postDAL->getPosts($accountID);
		$commentDAL = new CommentDAL();
		foreach ($posts as $post) {
			$comments = $commentDAL->getComments($post->PostID);
			$post->Comments = $this->sortByDate($comments, "newestLast"); // order the comments by created date
		}
		$posts = $this->sortByDate($posts, "newestFirst"); // order the posts by created date
		return $posts;
	}

	/**
	 * Gets the 20 last posts from the account and saves them, and their comments in the database
	 * @param int AccountID from database
	 * @return array of Post objects
	 */
	public function savePosts($accountID) {
		$data = $this->instagram->getUserPosts();
		$posts = $data->data;
		$returnPosts = array();
		
		foreach ($posts as $post) {
			$postID = $post->id;
			$likes = $post->likes->count;
			$text = "";
			if (isset($post->caption->text)) {
				$text = $post->caption->text;
			}
			$url = $post->images->standard_resolution->url;
			$userHasLiked = $post->user_has_liked;
			if ($userHasLiked == true) {
				$userHasLiked = 1;
			} else {
				$userHasLiked = 0;
			}
			$p = new Post($postID, $accountID, $post->created_time, $text, $url, $likes, $userHasLiked);
			
			$postDAL = new PostDAL();
			$newPostID = $postDAL->addPost($accountID, $postID, $p->CreatedTime, $text, $url, $likes, $userHasLiked);
			$postDAL->close();
			
			$comments = $this->saveComments($post, $newPostID, $accountID); 
			$p->addComments($comments);
			$returnPosts[] = $p;
		}
		return $returnPosts;
	}

	/**
	 * Saves the posts comments in the database
	 * @param post (stdClass) object from Instagram
	 * @param int PostID from database
	 * @param int AccountID from database
	 * @return array of Comment objects or empty array if none exist in database
	 */
	public function saveComments($post, $postID, $accountID) {
		$comments = $post->comments->data;
		$returnComments = array();
		$commentDAL = new CommentDAL();

		foreach ($comments as $comment) {
			$instaID = $comment->id;
			$text = $comment->text;
			$commentUsername = $comment->from->username;
			$commentProfilePic = $comment->from->profile_picture;
			
			$c = new Comment($instaID, $postID, $accountID, $text, $comment->created_time, $commentUsername, $commentProfilePic);
			$returnComments[] = $c;

			$commentDAL->addComment($accountID, $postID, $instaID, $text, $c->CreatedTime, $commentUsername, $commentProfilePic); 
		}
		
		$commentDAL->close();
		return $returnComments;
	}

	/**
	 * Function to use with php's usort-function for sorting
	 * @param   $a 		   one value
	 * @param   $b 		   another value
	 * @return  tinyint    -1, 0 or 1
	 */
	private function newestFirst($a, $b) {
		if ($a->CreatedTime == $b->CreatedTime) {
			return 0;
		}

		return ($a->CreatedTime < $b->CreatedTime) ? 1 : -1;
	}

	/**
	 * Function to use with php's usort-function for sorting
	 * @param   $a 		   one value
	 * @param   $b 		   another value
	 * @return  tinyint    -1, 0 or 1
	 */
	private function newestLast($a, $b) {
		if ($a->CreatedTime == $b->CreatedTime) {
			return 0;
		}

		return ($a->CreatedTime < $b->CreatedTime) ? -1 : 1;
	}
	
	/**
	 * Sorts an array with objects that have a CreatedTime value
	 * @param  array $array Post or Comment
	 * @return array        Sorted copy of in param
	 */
	private function sortByDate($array, $functionName) {
		usort($array, array($this, $functionName));
		return $array;
	}
	
}
