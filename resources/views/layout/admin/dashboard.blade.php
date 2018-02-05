<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
  <script src="../js/bootstrap.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="../css/bootstrap.min.css">
</head>
<body>

<div class="container">
	@if(session('success'))
		<h2>{{session('success')}}</h2>
	@endif
	@yield('body')  
</div>