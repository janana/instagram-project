<?php
class HTML {
	public static function getFormHTML($isRegistring, $message = "") {
		if ($isRegistring) {
			return "<h3>Register new user</h3>
				<form method='POST'>
					<p>{$message}</p>
					<input type='text' name='registerName' id='name' class='form-control' placeholder='Name' />
					<input type='submit' name='registerButton' value='Register' class='btn btn-lg btn-primary btn-block' />
					<a href='index.php'>Back to login page</a>
				</form>";
		} else {
			return "<h3>Login</h3>
				<form method='POST'>
				<p>{$message}</p>
					<input type='text' name='name' id='name' class='form-control'  placeholder='Name' />
					<input type='submit' value='Login' class='btn btn-lg btn-primary btn-block' />
					<a href='?register=true'>Register new user</a>
				</form>";
		}
	}

	public static function displayHTMLPage($content) {
		echo "<!DOCTYPE html>
			<html>
				<head>
				    <title>Instagram</title>
				    <meta charset='UTF-8'>
				    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
				    <meta http-equiv='Pragma' content='no-cache'>
				    <meta http-equiv='Expires' content='-1'>
				    <link rel='stylesheet' type='text/css' href='//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css'>
				    <link rel='stylesheet' href='css/style.css'>
				</head>
				<body>
					<div id='container' class='container'>
				    	{$content}
				    </div>
				    <script type='text/javascript' src='js/jquery-1.7.min.js'></script>
				    <script type='text/javascript' src='js/jquery.livequery.js'></script>

				</body>
			</html>";
	}
}