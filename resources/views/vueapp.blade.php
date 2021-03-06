<!doctype html>
<html lang="{{ app()->getLocale() }}">
	<head>
		<meta charset="utf-8">
		<meta name="description" content="My portfolio website">
		<meta name="keywords" content="portfolio, HTML, CSS, Bootstrap, JavaScript, Vue">
		<meta name="author" content="John Doe">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="csrf-token" content="{{ csrf_token() }}">

		<title>RpgGame SPA</title>

		<link rel="canonical" href="https://www.webpageNameHere4930394829.net/" />
		<link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">
		<link rel="stylesheet" type="text/css" href="css/app.css">
	</head>
	<body>
		<div id="app">
			<app></app>
		</div>
	<script type="text/javascript" src="{{ asset('js/app.js') }}"></script>
	</body>
</html>