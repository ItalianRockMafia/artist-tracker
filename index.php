<?php
session_start();
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
 	   <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
			<link rel="stylesheet" href="../global/main.css">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		<script src="https://use.fontawesome.com/c414fc2c21.js"></script>
		<title>IRM - Artist tracker</title>
	</head>
	<body>


	<nav class="navbar navbar-expand-lg navbar-dark bg-danger">
	<a class="navbar-brand" href="https://italianrockmafia.ch">ItalianRockMafia</a>
	  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
		<span class="navbar-toggler-icon"></span>
	  </button>
	<div class="collapse navbar-collapse" id="navbarSupportedContent">
		<ul class="navbar-nav mr-auto">
		<li class="nav-item">
        				<a class="nav-link" href="https://italianrockmafia.ch/main.php">Home</a>
      				</li>
							<li class="nav-item">
        				<a class="nav-link" href="https://italianrockmafia.ch/settings.php">Settings</a>
      				</li>
			  <li class="nav-item">
				<a class="nav-link" href="https://italianrockmafia.ch/meetup">Events</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="../emp">EMP</a>
			  </li>
				<li class="nav-item">
				<a class="nav-link" href="../vinyl">Vinyl</a>
			  </li>
			  <li class="nav-item active">
				<a class="nav-link" href="https://italianrockmafia.ch/artist-tracker">Artist Tracker <span class="sr-only">(current)</span></a>
			  </li>
				</ul>
				<ul class="nav navbar-nav navbar-right">
				<li class="nav-item">
        			<a class="nav-link" href="https://italianrockmafia.ch/login.php?logout=1">Logout</a>
      			</li>
		</ul>
	</div>
</nav>
<div class="topspacer"></div>
<main role="main">
	<div class="container">

<?php
require '../global/functions/apicalls.php';
require '../global/functions/telegram.php';
$config = require "../config.php";
$tg_user = getTelegramUserData();
if ($tg_user !== false) {
	$_SESSION['tgID'] = $tg_user['id'];
	$irm_users = json_decode(getCall($config->api_url . "users?transform=1&filter=telegramID,eq," . $tg_user['id']), true);
	foreach($irm_users['users'] as $user){
		$irm_user['id'] = $user['userID'];
	}
	
	$_SESSION['irmID'] = $irm_user['id'];
$my_events = json_decode(getCall($config->api_url . "eventAttendes?filter=telegramID,eq," . $tg_user['id'] . "&transform=1"), true);
?>
<h1>Artist Statistics</h1>

<?php
$artist_array = array();
foreach($my_events['eventAttendes'] as $my_event){
	$event_artists = json_decode(getCall($config->api_url . "artistEvent?filter=eventIDFK,eq," . $my_event['eventIDFK'] ."&include=artists&transform=1"), true);
	foreach($event_artists['artistEvent'] as $artist){
	$artist_array[$artist['artists'][0]['artist']]++;
	}



}
ksort($artist_array);



echo '<div id="accordion">';
foreach($artist_array as $artist => $times){
	$my_artistsevents = json_decode(getCall($config->api_url . "userArtistEvent?filter[]=telegramID,eq," . $tg_user['id'] . "&filter[]=artist,eq," . $artist . "&transform=1"), true);
	echo "<pre>"; print_r($my_artistsevents); echo "</pre>";
	echo ' <div class="card">
    <div class="card-header" id="'. $artist .'">
      <h5 class="mb-0">
        <button class="btn btn-link collapsed" data-toggle="collapse" data-target="#collapse' . $artist .'" aria-expanded="false" aria-controls="collapse' . $artist .'">
          '. $artist . ' - ' . $times . '
        </button>
      </h5>
    </div>
    <div id="collapse' . $artist .'" class="collapse" aria-labelledby="headingThree" data-parent="#accordion">
      <div class="card-body">
	'; 
	
	foreach($my_artistsevents['userArtistEvent'] as $event){

		echo $event['event_title'] . "<br>";
	}
	echo '      
</div>
    </div>
  </div>
';
}

echo '</div>';
} else {
	echo '
	<div class="alert alert-danger" role="alert">
	<strong>Error.</strong> You need to <a href="https://italianrockmafia.ch/login.php">login</a> first.
  </div>
';
}
?>
		
			</div>
			</main>
			<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
			<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
				</body>
			</html>