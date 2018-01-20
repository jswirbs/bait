<?php 

    require("common.php"); 
     
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
         
        die("Redirecting to login.php"); 
    }  

    $campaign_list = "clients/" . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') . "/Campaigns.php";
    $campaignArr = array();
    $campaignArr = file($campaign_list, FILE_IGNORE_NEW_LINES);
    $campaignLen = count($campaignArr);

    $name = "";
    $initArr = array();
    if(!empty($_POST)){
    	$filename = "clients/" . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8') . "/" . $_POST['campaign'] . ".php";
    	$name = $_POST['campaign'];
    	$initArr = file($filename, FILE_IGNORE_NEW_LINES);
    }     
    $initLen = count($initArr);

    if(!file_exists("clients/" . htmlentities($_SESSION['user']['username'], ENT_QUOTES, 'UTF-8'))) {
    	$campaign_list = "clients/demo/Campaigns.php";
    	$campaignArr = file($campaign_list, FILE_IGNORE_NEW_LINES);
    	$campaignLen = count($campaignArr);
    	if(!empty($_POST)){
    		$filename = "clients/demo/" . $_POST['campaign'] . ".php";
    		$initArr = file($filename, FILE_IGNORE_NEW_LINES);
    	}     
    	$initLen = count($initArr);
    }

    $splitInitArr = explode(",", $initArr[2]);
    $numEmployees = count($splitInitArr);
    $employees = array();
    for ($i = 0; $i < $numEmployees; $i++) {
    	$employees[$i] = array($splitInitArr[$i], "", "", "");
    }

    $numClicked = 0;
    $numEntered = 0;
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
<nav id="sidenav" class="sidenav" style="background-color: #2d2d2d;">
	<a href="overview.php" style="margin-top: 7px;">Overview</a>
	<a href="javascript:void(0);" onclick="dropdown();"><div id="campaigns" class="container"><div>Campaigns</div><div style="text-align: right; padding-right: 15px;"><b class="arrow up"></b></div></div></a>
	<div id="dropdown">
		<?php 
            for ($i = 1; $i < $campaignLen; $i++) {
                $split = explode(",", $campaignArr[$i]);
                echo '<form action="campaigns" method="post"><button type="submit" name="campaign" value="' . $split[0] . '" class="subsidenav"><b class="arrow right" style="margin-right:8px;"></b>' . $split[0] . ' </button></form>';
            } 
        ?>
	</div>
	<a href="customize.php">Customize</a>
</nav>


<!-- MAIN PAGE -->
<div id="page" class="page">
	
	<!-- TITLE -->
	<div id="title" style="font-size: 45px; color: #2d2d2d; font-family: 'Raleway', sans-serif;">
		<?php
			echo $name;
		?>
	</div>
	<div style="font-size: 10px;"> 
		<?php
			for ($i = 0; $i < $campaignLen; $i++) {
				$split = explode(",", $campaignArr[$i]);
				if ($split[0] == $name) {
					echo $split[1];
				}
			}
		?>
	</div>

	<!-- CIRCLES -->
	<div id="circles" class="container" style="margin-top: 20px;">
		<div class="square">
			<svg height="150" width="150">
  				<g id="emailsSent">
  					<circle class="circle100" cx="75" cy="75" r="70" />
  					<text x="56" y="60" font-family="Verdana" font-size="30" fill="#3f0f91"></text>
  					<text x="32" y="90" font-family="Verdana" font-size="15" fill="black">emails sent</text>
  				</g>
			</svg>
		</div>
		<div class="square">
			<svg height="150" width="150">
  				<g id="clickedLink">
  					<circle class="circle0" cx="75" cy="75" r="70" />
  					<text x="54" y="60" font-family="Verdana" font-size="30" fill="#3f0f91"></text>
  					<text x="34" y="90" font-family="Verdana" font-size="15" fill="black">clicked link</text>
  				</g>
			</svg>
		</div>
		<div class="square">
			<svg height="150" width="150">
  				<g id="enteredCredentials">
  					<circle class="circle0" cx="75" cy="75" r="70" />
  					<text x="54" y="60" font-family="Verdana" font-size="30" fill="#3f0f91"></text>
  					<text x="46" y="84" font-family="Verdana" font-size="15" fill="black">entered</text>
  					<text x="34" y="102" font-family="Verdana" font-size="15" fill="black">credentials</text>
  				</g>
			</svg>
		</div>
	</div>

	<!-- TABLE -->
	<div style="margin-top: 35px;">
		<table id="table">
			<?php
				for ($i = 0; $i < $initLen; $i++) {
					$split = explode(" ", $initArr[$i]);
					for ($j = 0; $j < $numEmployees; $j++) {
						if ($split[0] == $employees[$j][0]) {
							if ($split[1] == "c") {
								$employees[$j][1] = "c";
								//$numClicked++;
							} else if ($split[1] == "e") {
								$employees[$j][2] = "e";
								//$numEntered++;
							}
						}
					}
				}

				$tableStr = "<tr><th colspan='2'>Sent to</th><th colspan='2'>Clicked Link</th><th colspan='2'>Entered Credentials</th></tr>";
				for ($i = 0; $i < $numEmployees; $i++) {
					if ($employees[$i][2] == "e") {
						$tableStr = $tableStr . "<tr><td colspan='2'>" . $employees[$i][0] . "</td><td colspan='3' style='background-color:#3f0f91;'></td>";
						$numClicked++;
						$numEntered++;
					} elseif ($employees[$i][1] == "c") {
						$tableStr = $tableStr . "<tr><td colspan='2'>" . $employees[$i][0] . "</td><td colspan='1' style='background-color:#335c81;'></td>";
						$numClicked++;
					} else {
						$tableStr = $tableStr . "<tr><td colspan='2'>" . $employees[$i][0];
					}
				}
				echo $tableStr;
			?>
		</table>
	</div>

	<!-- DESCRIPTION -->
	<div id="description" class="description">
	  	<?php	
			echo $initArr[1];
		?>
	</div>

</div>

<!-- FOOTER -->
<div id="footer" class="footer">
	<div style="padding: 15px;">
		&#32;&#169; Asio Security, 2017
	</div>
</div>

<script type="text/javascript">
	var numEmployees = <?php echo $numEmployees;?>;
	var numClicked = <?php echo $numClicked;?>;
	var numEntered = <?php echo $numEntered;?>;
	var perClicked = 0;
	var perEntered = 0;
	
	function onLoad() {
		perClicked = numClicked / numEmployees;
		perClicked = Math.round(perClicked*100);
		perEntered = numEntered / numEmployees;
		perEntered = Math.round(perEntered*100);
		var offsetSent = 56;
		var offsetClicked = 56;
		var offsetEntered = 56;
		if (numEmployees < 10) {offsetSent = 64;}
		if (numClicked < 10) {offsetClicked = 64;}
		if (numEntered < 10) {offsetEntered = 64;}

		document.getElementById('emailsSent').innerHTML =
		'<circle class="circle100" cx="75" cy="75" r="70" /><text x="' + offsetSent + '" y="60" font-family="Verdana" font-size="30" fill="#3f0f91">' + numEmployees + '</text><text x="32" y="90" font-family="Verdana" font-size="15" fill="black">emails sent</text>';
		document.getElementById('clickedLink').innerHTML =
		'<circle style="stroke-dashoffset: ' + ((100-perClicked)*4.4) + ';animation: show' + perClicked + ' 2.5s;" cx="75" cy="75" r="70" /><text x="' + offsetClicked + '" y="60" font-family="Verdana" font-size="30" fill="#3f0f91">' + numClicked + '</text><text x="34" y="90" font-family="Verdana" font-size="15" fill="black">clicked link</text>';
		document.getElementById('enteredCredentials').innerHTML =
		'<circle style="stroke-dashoffset: ' + ((100-perEntered)*4.4) + ';animation: show' + perEntered + ' 2.5s;" cx="75" cy="75" r="70" /><text x="' + offsetEntered + '" y="60" font-family="Verdana" font-size="30" fill="#3f0f91">' + numEntered + '</text><text x="46" y="84" font-family="Verdana" font-size="15" fill="black">entered</text><text x="34" y="102" font-family="Verdana" font-size="15" fill="black">credentials</text>';

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