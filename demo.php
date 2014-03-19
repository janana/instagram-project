<?php
    session_start();
    if (!isset($_SESSION["UserID"])) {
        header("Location: error.html");
    }
    $_SESSION["antiForgeryToken"] = md5(uniqid(mt_rand(), true));
?>
<!DOCTYPE html>
<html>
<head>
    <title>Instagram</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="-1">
    <!-- style tags start here -->
    <link rel="stylesheet" type="text/css" href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- style tags end here -->
</head>
<body>
    <input type="hidden" id="userID" value="<?php echo $_SESSION['UserID']; ?>" />
    <input type="hidden" id="antiForgeryToken" value="<?php echo $_SESSION['antiForgeryToken']; ?>"/>
    <div id="container" class="container"> <!-- container div begin-->
        <input type='button' class='btn hide' id='account-info' value='Account information' />
        <div id='account-box' class='hide'></div>
        <div id='accounts-div'></div>
        <div id="form_row" class="row">
            <div id="entry-box" class="col-md-12">
                <!-- All the posts are gonna be displayed here -->
            </div>

        </div> <!-- div row end -->


    </div> <!-- container div end -->

    <!-- script tags start here -->
    <script type="text/javascript" src="js/jquery-1.7.min.js"></script>
    <script type="text/javascript" src="js/jquery.livequery.js"></script>
    <script type="text/javascript" src="ajax.js"></script>

</body>
</html>