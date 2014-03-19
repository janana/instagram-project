<?php

class InstagramContentBuilder{
    /**
     * Username from instagram
     * @var string
     */
    private $accountName;

    /**
     * Adds accountname when creating an instance of the class
     * @param string $accountName Username from instagram
     */
    public function __construct($accountName) {
        $this->accountName = $accountName;
    }

	/**
     * Creates bootstrap-adapted Entry list for the posts
     * @param Array of Post objects or stdClasses from database
     * @return string HTML 
     */
    public function createEntries($posts) {
        $html = "";
        foreach($posts as $post) {
            $html = $html . $this->createEntry($post);
        }
        return $html;
    }
	
	/**
     * Creates a bootstrap-adpated UI component for the post
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createEntry($post){
        $html = $this->createMediaBox($post);
        $html .= $this->createCreatingDateTimeLabel($post);
        $html .= $this->createLikesBadge($post);
		$html .= $this->createMediaObjectCaption($post);
        $html .= $this->createLikeButton($post);
        $html .= $this->createCommentsBox($post);
        $html .= $this->createWriteCommentBox($post);
        return ($html . "<br>");
    }
	
	/**
     * Creates a div containing the media object
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createMediaBox($post) {
        if(!isset($post->PostURL)) return "";

        $html = "<div id='image-div{$post->InstagramPostID}'>";
        $html .= "<img src='{$post->PostURL}' alt='{$post->CreatedTime}' class='img-responsive' data-media-id='{$post->InstagramPostID}'>";
        $html .= "</div>\n\n";
        return $html;
    }
	
	/**
     * Creates a like button for the media
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createLikeButton($post) {
        if(!isset($post->UserHasLiked)) return "";

        $likeClass = "show";
        if($post->UserHasLiked == 1) $likeClass = "hide";
        $unlikeClass = "hide";
        if($post->UserHasLiked == 1) $unlikeClass = "show";

        $html = "<button type='button' id='likeButton-{$post->InstagramPostID}' class='btn btn-primary btn-sm {$likeClass}' data-media-id='{$post->InstagramPostID}' data-likes-count='{$post->LikesCount}' data-ui-role='like-button'>Like</button>";
        $html .= "<button type='button' id='unlikeButton-{$post->InstagramPostID}' class='btn btn-primary btn-sm {$unlikeClass}' data-media-id='{$post->InstagramPostID}' data-likes-count='{$post->LikesCount}' data-ui-role='unlike-button'>Unlike</button>";
        
        return $html;
    }

    /**
     * Creates a span containing info on who liked the post
     * @param  Post object or stdClass from database
     * @return String HTML
     */
    public function createLikesBadge($post) {
        $likeString = InstagramContentBuilder::printLikers($post->LikesCount, $post->UserHasLiked, $this->accountName);
        return "<span class='label label-primary' data-ui-role='count-like-badge' data-media-id='{$post->InstagramPostID}' >{$likeString}</span><br/>";
    }

    public static function printLikers($numberOfLikes, $selfLike, $accountName){
        $returnString = "";
        if ($selfLike == 1){
            $returnString = $accountName . ' ';
            if ($numberOfLikes == 2){
                $returnString .= 'and 1 other person likes this';
            } else if ($numberOfLikes>2){
                $returnString .= 'and '.((int)$numberOfLikes-1).' other people likes this';
            } else {
                $returnString .= 'like this';
            }
        } else {
            if ($numberOfLikes == 1){
                $returnString .= $numberOfLikes.' person likes this';
            }
            if ($numberOfLikes > 1){
                $returnString .= $numberOfLikes.' people likes this';
            }
        }
        return $returnString;
    }
	
	/**
     * Creates a div containing the media's caption text
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createMediaObjectCaption($post) {
        if (!isset($post->Text)) return "";

        $html = "<div id='div-caption-{$post->InstagramPostID}' data-ui-role='media-caption' data-media-id='{$post->InstagramPostID}'><h5>{$post->Text}</h5></div>";
        return $html;
    }
	
	/**
     * Creates a media list for the comments of the the object
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createCommentsBox($post) {
        if(!isset($post->Comments) || count($post->Comments) == 0) return "";

        $entries = "";
        foreach($post->Comments as $comment) {
          $entries .= self::createCommentBox($comment, $post->InstagramPostID);
        }

        if(empty($entries)) return "";

        return ("<ul class='media-list' data-media-id='{$post->InstagramPostID}' >" . $entries . "</ul>");
    }
	
	/**
     * Creates a comment entry list item for the object
     * @param Comment object or stdClass from database
     * @return string HTML
     */
    public static function createCommentBox($comment, $instagramPostID) {
        if(!isset($comment)) return "";

        return "<li class='media' data-comment-id='{$comment->InstagramCommentID}' >
                <span class='close pull-right delete-comment-link' data-comment-id='{$comment->InstagramCommentID}' data-media-id='{$instagramPostID}'>&times;</span>
                <img class='media-object pull-left' src='{$comment->ProfilePicURL}'/>
                <div class='media-body'>
                    <h4 class='media-heading'>{$comment->Username}</h4>

                    {$comment->Text}
                    
                    
                </div>
                <span class='label label-default date-time-label time-badge'>{$comment->CreatedTime}</span>
                </li>\n\n";
    }
	
	/**
     * Creates a comment text input for the object
     * @param Post object or stdClass from database
     * @return string HTML
     */
    private function createWriteCommentBox($post){
        if(!isset($post)) return "";

        return "<div class='send-comment-div'><input type='text' class='form-control comment-control' data-media-id='{$post->InstagramPostID}' /><input type='button' class='btn btn-primary btn-s comment-button' value='Comment' id='{$post->InstagramPostID}' data-ui-role='write-comment-button' data-media-id='{$post->InstagramPostID}' /></div>";
    }

    /**
     * Creates a label for date time for media object's creation date & time
     * @param  Post object or stdClass from database
     * @return string HTML
     */
    private function createCreatingDateTimeLabel($post) {
        if (!isset($post->CreatedTime)) return "";

        return "<br/><span class='label label-default date-time-label time-badge'>{$post->CreatedTime}</span>";
    }

    /**
     * Creates a div with information on the Instagram account
     * @param  stdClass object      $accountInfo    containing the information to display 
     * username, profilePic, fullName, website, bio, postCount, followedBy, follows
     * @return string HTML                          
     */
    public static function createAccountInfoBox($accountInfo) {
        $html = "<h4>".$accountInfo->username."</h4>";
        $html .= "<img src='".$accountInfo->profilePic."' />";
        
        $html .= "<div class='account-info-box'>";
        if ($accountInfo->fullName != "") {
            $html .= "<p>".$accountInfo->fullName."</p>";
        }
        if ($accountInfo->website != "") {    
            $html .= "<p><a href='".$accountInfo->website."'>".$accountInfo->website."</a></p>";
        }
        if ($accountInfo->bio != "") {
            $html .= "<p>".$accountInfo->bio."</p>";
        }
        $html .= "<p>Posts: <span>".$accountInfo->postCount."</span></p>";
        $html .= "<p>Followers: <span>".$accountInfo->followedBy."</span></p>";
        $html .= "<p>Follows: <span>".$accountInfo->follows."</span></p>";

        $html .= "</div>";
        return $html;
    }

}
?>