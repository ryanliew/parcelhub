<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  <!-- <script src="../js/jquery-3.3.1.min.js" type="text/javascript"></script>
  <script src="../js/bootstrap.min.js" type="text/javascript"></script>
  <link rel="stylesheet" href="../css/bootstrap.min.css"> -->
</head>
<body>

<div class="container">
	@yield('body')
    @component('components.alert') @endcomponent
</div>