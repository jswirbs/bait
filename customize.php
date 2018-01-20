<?php 

    require("common.php"); 
     
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 

        die("Redirecting to login.php"); 
    }  

    $campaign_list = "clients/" . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') . "/Campaigns.php";
    $campaignArr = file($campaign_list, FILE_IGNORE_NEW_LINES);
    $campaignLen = count($campaignArr);

    if(!file_exists("clients/" . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'))) {
    	$campaign_list = "clients/demo/Campaigns.php";
    	$campaignArr = file($campaign_list, FILE_IGNORE_NEW_LINES);
    	$campaignLen = count($campaignArr);
    }


    $thanks = "";
    if(!empty($_POST["campaignname"])) {
    	$username = htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8');
    	$email = htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8');
    	date_default_timezone_set("America/New_York");
    	$message = "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~\n" . date("h:i:sa d/m/Y") . "\nRequest from: " . $username . " - " . $email . "\n Campaign Name: " . $_POST["campaignname"] . "\n Description: " . $_POST["campaigndescription"] . "\n";

    	$filename = 'requests.php';

    	file_put_contents($filename, $message, FILE_APPEND);

    	$thanks = "Thanks for your request! \r\nWe will get back to you shortly!";
    }

?> 
<!DOCTYPE html>
<html>
<head>
	<title>Bait Platform</title>
	<link rel="icon" href="img/owl_icon.png">
	<link rel="stylesheet" type="text/css" href="main.css">
	<link rel="stylesheet" type="text/css" href="circles.css">
	<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Raleway" rel="stylesheet">
</head>
<body onload="onLoad();">

<!-- HEADER BAR -->
<div class="header container">
	<div style="text-align: left; padding: 10px;"><div style="padding-left:46px;"><a href="https://www.asiosecurity.com" class="logo">Asio Security</a></div></div>
	<div style="padding: 10px;">Bait Platform</div>
	<div>
		<div class="myAccount">
			<button class="myAccountButton"><?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?><b class="arrow down" style="margin-left: 10px;"></b></button>
			<div class="myAccountContent">
				<a href="edit_account.php">Edit Account</a>
				<a href="logout.php">Log out</a>
			</div>
		</div>
	</div>
</div>


<!-- SIDE NAVIGATION BAR -->
<nav class="sidenav" style="background-color: #2d2d2d;">
	<a href="overview.php" style="margin-top: 7px;">Overview</a>
	<a href="javascript:void(0);" onclick="dropdown();"><div id="campaigns" class="container"><div>Campaigns</div><div style="text-align: right; padding-right: 15px;"><b class="arrow down"></b></div></div></a>
	<div id="dropdown"></div>
	<a href="customize.php">Customize</a>
</nav>


<!-- MAIN PAGE -->
<div id="page" class="page">
	
	<!-- TITLE -->
	<div id="title" style="font-size: 45px; color: #2d2d2d; font-family: 'Raleway', sans-serif;">
		Customize
	</div>

	<!-- CUSTOMIZE FORM -->
	<div class="container" style="padding-top: 40px;">
		<div style="margin-right: 10px;">
			<form action="customize" method="post">
				<input type="text" name="campaignname" placeholder="Campaign Name" style="width: 90%; font-size: 16px;" required><br>
				<textarea name="campaigndescription" placeholder="Campaign Description" style="width: 90%; margin-top: 3px; font-size: 16px; border: 1px solid #ccc;" rows="12" required></textarea>
				<input type="Submit" name="campaignsubmit" value="Submit" class="submitBtn">
			</form>
		</div>
		<div style="text-align: justify; margin-right: 10px;">
			<div style="font-size: 20px; padding-bottom: 5px; text-align: center;">Customize a Campaign</div>
			Submit a request for a customized campaign. Please include a detailed description of what you want in the mock attack, including the company/service and the website to be spoofed if applicable. If you have any questions, concerns, or would rather correspond via email, please contact <a href="mailto:jswirbul@asiosecurity.com">jswirbul@asiosecurity.com</a>.

		</div>
	</div>
	<?php 
		if ($thanks != "") {
			echo '<div style="margin: 0 20px; margin-top: 20px; background-color: pink;">';
			echo $thanks;
			echo '</div>';
			$thanks = "";
		}
	?>
</div>

<!-- FOOTER -->
<div id="footer" class="footer">
	<div style="padding: 15px;">
		&#32;&#169; Asio Security, 2017
	</div>
</div>


<script type="text/javascript">
	function onLoad() {
		var year = new Date().getFullYear();
		document.getElementById('footer').innerHTML = '<div style="padding: 15px;">&#32;&#169; Asio Security, ' + year + '</div>';

		document.getElementById('page').style.opacity='1';
		document.getElementById('footer').style.opacity='1'
	}

	function dropdown() {
		if (document.getElementById('dropdown').innerHTML == '') {
			document.getElementById('dropdown').innerHTML = 
			'<?php 
				for ($i = 1; $i < $campaignLen; $i++) {
					$split = explode(",", $campaignArr[$i]);
					echo '<form action="campaigns" method="post"><button type="submit" name="campaign" value="' . $split[0] . '" class="subsidenav"><b class="arrow right" style="margin-right:8px;"></b>' . $split[0] . ' </button></form>';
				} 
			?>';
			
			document.getElementById('campaigns').innerHTML = '<div>Campaigns</div><div style="text-align: right; padding-right: 15px;"><b class="arrow up"></b></div>';
		} else {
				document.getElementById('dropdown').innerHTML = '';
				document.getElementById('campaigns').innerHTML = '<div>Campaigns</div><div style="text-align: right; padding-right: 15px;"><b class="arrow down"></b></div>';
		}
	}
</script>
</body>
</html>