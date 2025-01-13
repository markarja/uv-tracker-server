<!DOCTYPE html>
<html>
	<head>
		<title>Feedback for UV radiation now</title>
		<meta name="viewport" content="width=device-width,height=device-height,user-scalable=no" />
	    <meta name="apple-mobile-web-app-capable" content="yes">    
	   	<link rel="stylesheet" href="style.css" type="text/css">
	</head>
	<body class="feedback" onload="document.getElementById('nickname').focus();">
		<form action="/feedback.php" method="post">
			<div class="feedback-form">
				<p>Please give us your valuable feedback!</p>
				<p><input class="feedback-input-element" name="nickname" id="nickname" type="text" value="" placeholder="nickname" /></p>
				<p><input class="feedback-input-element name="email" id="email" type="text" value="" placeholder="email address (optional)" /></p>
				<textarea rows="10" class="feedback-input-element"></textarea>
				<button class="buttons feedback-button">Send</button>
			</div>
		</form>
	</body>
</html>