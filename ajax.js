$(document).ready(function(){
    var userID = $("#userID").val(); // UserID from database
    
    var accountID = getQueryString();
    if (accountID != "") {
        showAccount(accountID);
    } else {
        // Display the manage account page
        $.ajax({
            type: "post",
            url: "ajax.php",
            data: { "function": "getAccounts", "userID": userID }
        }).success(function(data) {
            if (data != "Error") {
                try {
                    var json = JSON.parse(data);
                    if (json[0] != null) {
                        var html = "";
                        for (var i = 0; i < json.length; i++) {
                            html += "<a href='#' class='pull-left account-link' data-account-id='"+json[i].AccountID+"'><img src='"+json[i].ProfilePicURL+"' /><p>"+json[i].Username+"</p></a>";
                        }
                        $("#accounts-div").append(html);

                        // Bind the account links
                        $(".account-link").livequery('click', function (e) {
                            $("#accounts-div").empty();
                            accountID = e.currentTarget.attributes["data-account-id"].value;
                            // Set the accessData
                            $.ajax({
                                type: "post",
                                url: "ajax.php",
                                data: { "function": "setAccessData", "accountID": accountID }
                            });
                            showAccount(accountID);
                            e.preventDefault();
                        });
                    }
                    // Add a button for adding a new Instagram account
                    $("#accounts-div").append("<input type='button' value='Add new Instagram account' id='add-account-button' class='btn btn-lg btn-primary btn-block' /><p class='text-muted'>(If you are already online on Instagram in your browser, that is the account that will appear)</p>");

                    // Bind click on the add-account-button
                    $("#add-account-button").livequery('click', function (e) {
                        $.ajax({
                            type: "post",
                            url: "ajax.php",
                            data: { "function": "getInstagramLink" }
                        }).success(function(data) {
                            window.location = data;
                        });
                    });
                } catch (e) {
                    console.log(e);
                }
                
            } else {
                console.log(data);
            }
            
        });
    }
});


/**
 * Displays the posts associated to the accountID that are saved in the database
 * Inits all the buttons for liking/unliking/commenting...
 * @param  {int} accountID from database
 */
function showAccount(accountID) {
    // Display the accounts posts and information
    $("#container").prepend("<input type='button' id='back-button' value='Back' class='btn btn-primary' />");
    $("#back-button").livequery('click', function() {
        window.location = "?";
    });
    getContent(accountID); 
    $("#account-info").removeClass('hide');
    // Bind account info box 
    $("#account-info").livequery('click', function() {
        $("#account-box").toggleClass('hide');
    });

    // Bind like button click-event
    $("[type*='button'][data-ui-role='like-button']").livequery('click', function () {
        var postID = $(this).attr('data-media-id');
        updateLike(postID, 1);
    });

    // Bind the unlike button
    $("[type*='button'][data-ui-role='unlike-button']").livequery('click', function () {
        var postID = $(this).attr('data-media-id');
        updateLike(postID, 0);
    });

    // Bind comment-button
    $("[type*='button'][data-ui-role='write-comment-button']").livequery('click', function () {
       var postID = $(this).attr('data-media-id');
       var commentText = $("[type*='text'][data-media-id='"+postID+"']").val();
       if(commentText == "") return;

       commentPost(postID, commentText);
    });

    // Bind the delete-comment-link
    $(".delete-comment-link").livequery('click', function () {
        var postID = $(this).attr('data-media-id');
        var commentID = $(this).attr('data-comment-id');

        if (postID != "" && commentID != "") {
            // User has to confirm before comment is being deleted
            var confirmed = confirm("Are you sure you want to delete this comment?");
            if (confirmed) {
                deleteComment(postID, commentID);
            }
        }
    });

    // Get the account info from instagram via ajax
    $.ajax({
        type: "post",
        url: "ajax.php",
        data: { "function": "getAccountInfo" }
    }).success(function(data) {
        if (data != "Error") {
            $("#account-box").append(data);
        } 
    });
    
}

/**
 * Ajax function for getting the HTML of the Instagram posts
 * displays the HTML in the div with id='entry-box'
 * @param  {int} userID ID from database
 */
function getContent(accountID) {
    $.ajax({
        type: "POST",
        url: "ajax.php",
        data: { "function": "getContent", "accountID": accountID }
    }).success(function (data) {
        $("#entry-box").empty();
        $("#entry-box").append(data);
        setQueryString("account="+accountID);
    });
}

/**
 * Ajax function for liking and unliking a post on Instagram
 * @param  {string}  postID ID from Instagram
 * @param  {tinyint} like   1 if post is going to be liked, 0 if unlike
 */
function updateLike(postID, like){
    var antiForgeryToken = $("#antiForgeryToken").val();
    $.ajax({
        type: "post",
        url: "ajax.php",
        data: { "function": "updateLike", "postID": postID, "like": like, "antiForgeryToken": antiForgeryToken }
    }).success(function(data) {
        if (data != "Error") {
            if (like == 1) {
                hideLikeButton(postID);
                showUnlikeButton(postID);
            } else {
                hideUnlikeButton(postID);
                showLikeButton(postID);
            }
            $("[data-ui-role='count-like-badge'][data-media-id='"+postID+"']").html(data);
        }
    });
}

/**
 * Ajax function for commenting a post on Instagram
 * @param  {string} postID      ID from Instagram
 * @param  {string} commentText Text to comment
 */
function commentPost(postID, commentText) {
    var antiForgeryToken = $("#antiForgeryToken").val();
    $.ajax({
        type: "post",
        url: "ajax.php",
        data: { "function": "commentPost", "postID": postID, "text": commentText, "antiForgeryToken": antiForgeryToken }
    }).success(function(data) { // Waiting for Instagram to approve access for this to work
        if (data != "Error") {
            $(".media-list[data-media-id='"+postID+"']").append(data);
        } else {
            alert("Could not comment post in Instagram");
        }
    });
}

/**
 * Ajax function for deleting a comment on a post on Instagram
 * @param  {string} postID      ID from Instagram
 * @param  {string} commentID   ID from Instagram
 */
function deleteComment(postID, commentID) {
    var antiForgeryToken = $("#antiForgeryToken").val();
    $.ajax({
        type: "post",
        url: "ajax.php",
        data: { "function": "deleteComment", "postID": postID, "commentID": commentID, "antiForgeryToken": antiForgeryToken }
    }).success(function(data) {
        if (data == "succeeded") {
            // Remove the comment from the page
            $(".delete-comment-link[data-media-id='"+postID+"'][data-comment-id='"+commentID+"']").parent().remove();
        }
    });
}

/**
 * Returns the URL query string if it is set
 * @return {string} the query string from the URL
 */
function getQueryString() {
    var query = window.location.search;
    query = query.match(/\?(.)*/);
    if (query != null) {
        query = query[0].replace("?", "");
        if (/account/.test(query)) {
            return query.replace("account=", "");
        }
    }
    return "";
}
/**
 * Sets the URL query string
 * @param {string} query    the string to set in URL as query
 */
function setQueryString(query) {
    var url = "?"+query;
    history.pushState("", "Instagram", url); // Session/cookie data | Page title to display in history | URL
}

//Show the like button on the page
function showLikeButton(mediaId){
    $("[type*='button'][data-ui-role='like-button'][data-media-id='" + mediaId + "']").removeClass('hide').addClass('show');
}

//Hide the like button from the page
function hideLikeButton(mediaId){
    $("[type*='button'][data-ui-role='like-button'][data-media-id='" + mediaId + "']").removeClass('show').addClass('hide');
}

//Show the unlike button on the page
function showUnlikeButton(mediaId){
    $("[type*='button'][data-ui-role='unlike-button'][data-media-id='" + mediaId + "']").removeClass('hide').addClass('show');
}

//Hide the unlike button from the page
function hideUnlikeButton(mediaId){
    $("[type*='button'][data-ui-role='unlike-button'][data-media-id='" + mediaId + "']").removeClass('show').addClass('hide');
}