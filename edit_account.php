 <?php 

    require("common.php"); 
     
    if(empty($_SESSION['user'])) 
    { 
        header("Location: login.php"); 
         
        // necessary so non-users can't see private pages
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

    
    $message = "";

    // checks if edit form has been submitted yet
    if(!empty($_POST)) 
    { 
        // this query retreives the user's information from the database using 
        // their username. 
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                email,
                phone_number,
                secondary_v
            FROM users 
            WHERE 
                username = :username 
        "; 
         
        // the parameter values 
        $query_params = array( 
            ':username' => $_SESSION['user']['username']
        ); 
         
        try 
        { 
            // execute the query against the database 
            $stmt = $db->prepare($query); 
            $result = $stmt->execute($query_params); 
        } 
        catch(PDOException $ex) 
        { 
            // only output error message for testing --> $ex->getMessage()
            //die("Failed to run query: " . $ex->getMessage()); 
            die("Failed to connect to the database.\n Please contact Asio Security if the problem persists."); 
        } 
         
        // has user enteref correct current password
        $login_ok = false; 
         
        // retrieves the user data from the database.  If $row is false, then the
        // username they entered is not registered. 
        $row = $stmt->fetch(); 
        if($row) 
        { 
            // hashes entered password and compares it to what's stored in database
            $check_password = hash('sha256', $_POST['password'] . $row['salt']); 
            for($round = 0; $round < 65536; $round++) 
            { 
                $check_password = hash('sha256', $check_password . $row['salt']); 
            } 
             
            if($check_password === $row['password']) 
            {  
                $login_ok = true; 
            } 
        } 
         
        
        if($login_ok) 
        { 
            // removes salt and password values from $row array before it is
            // stored in the $_SESSION (not necessary as it's on server side, but why not remove sensitive data)
            unset($row['salt']); 
            unset($row['password']); 
        } 
        else 
        { 
            $message = "Password incorrect!";
        }



        if(($message == "") && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            //die("Invalid E-Mail Address"); 
            $message = "Invalid E-Mail Address!";
        } 
        else 
        {
            // if changing email, check that it's unique in the db table
            if(($message == "") && ($_POST['email'] != $_SESSION['user']['email'])) 
            { 
                 // define our SQL query 
                $query = " 
                    SELECT 
                        1 
                    FROM users 
                    WHERE 
                        email = :email 
                "; 
             
                // define our query parameter values 
                $query_params = array( 
                    ':email' => $_POST['email'] 
                ); 
                 
                try 
                { 
                    // execute the query 
                    $stmt = $db->prepare($query); 
                    $result = $stmt->execute($query_params); 
                } 
                catch(PDOException $ex) 
                { 
                    // only output error message for testing --> $ex->getMessage()
                    //die("Failed to run query: " . $ex->getMessage()); 
                die("Failed to connect to the database.\n Please contact Asio Security if the problem persists."); 
                } 
             
                // retrieve results (if any) 
                $row = $stmt->fetch(); 
                if($row) 
                { 
                    //die("This E-Mail address is already in use"); 
                    $message = "This E-Mail address is already in use!";
                } 
            } 
        }
         
        
        if (strlen($_POST['password2']) < 6) 
        {
            $message = "Password must be at least 6 characters."; 
        }
         
        // If the user entered a new password, we need to hash it and generate a fresh salt
        if(($message == "") && (!empty($_POST['password2']))) 
        { 
            if($_POST['password2'] == $_POST['password3']) {
                $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
                $password = hash('sha256', $_POST['password2'] . $salt); 
                for($round = 0; $round < 65536; $round++) 
                { 
                    $password = hash('sha256', $password . $salt); 
                } 
            }
            else 
            {
                $password = null; 
                $salt = null; 
                //die("The passwords do not match!");
                $message = "The passwords do not match!";
            }
        } 
        else 
        { 
            $password = null; 
            $salt = null; 
        } 
         

        if($message == "") {
            // initial query parameter values 
            $query_params = array( 
                ':email' => $_POST['email'], 
                ':user_id' => $_SESSION['user']['id'], 
            ); 
         
            // if the user is changing their password, then we need parameter values 
            // for the new password hash and salt too. 
            if($password !== null) 
            { 
                $query_params[':password'] = $password; 
                $query_params[':salt'] = $salt; 
            } 
         
            // first half of the necessary update query
            $query = " 
                UPDATE users 
                SET 
                    email = :email 
            "; 
         
            // if the user is changing their password, then extend the SQL query 
            // to include the password and salt columns and parameter tokens too. 
            if($password !== null) 
            { 
                $query .= " 
                    , password = :password 
                    , salt = :salt 
                "; 
            } 
         
            // finish the update query by specifying that we only wish 
            // to update the one record for the current user. 
            $query .= " 
                WHERE 
                    id = :user_id 
            "; 
         
            try 
            { 
                // execute the query 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                // only output error message for testing --> $ex->getMessage()
                //die("Failed to run query: " . $ex->getMessage()); 
                die("Failed to connect to the database.\n Please contact Asio Security if the problem persists."); 
            } 
         
            // update data stored in $_SESSION
            $_SESSION['user']['email'] = $_POST['email'];

            $message = "Your account has been updated!";
        }        
        
    } 
     
?> 
<!DOCTYPE html>
<html>
<head>
    <title>Bait Platform</title>
    <link rel="icon" href="img/owl_icon.png">
    <link rel="stylesheet" type="text/css" href="main.css">
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
        Edit Account
    </div>
    <form action="edit_account" method="post" style="margin-top: 30px;"> 
        <div style="padding-bottom: 16px; font-size: 10px;"><i>You must verify your password in order to change your account information.</i></div>
        <div>Current Password: <input type="password" size="28" name="password" value="" /><br /></div><br />
        <svg width="600" height="3" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 600 4">
                <line x1="0" y1="0" x2="600" y2="0" style="stroke:#3f0f91;stroke-width:3" />
            </svg><br /><br />
        <div style="padding-left: 24px;">E-Mail Address: <input type="text" size="28" name="email" value="<?php echo htmlentities($_SESSION['user']['email'], ENT_QUOTES, 'UTF-8'); ?>" /> </div>
        <br />
        <div style="padding-left: 24px;">New Password: <input type="password" size="28" name="password2" value="" /><br />
        <div style="font-style: italic; font-size: 10px;">(leave blank if you do not want to change your password) </div></div>
        <br /> 
        <div style="padding-right: 3px;">Confirm Password: <input type="password" size="28" name="password3" value="" /></div>
        <br /><br />
        <input type="submit" name="updated" value="Update Account" class="submitBtn" /> 
    </form>
    <br /><br />

    <?php 
        // display message -> confirmation or error
        if($message != "") {
            echo '<div style="margin: 0 40px; margin-top: 40px; background-color: pink;">';
            echo $message;
            echo '</div>';
        }
    ?>
</div>

<!-- FOOTER -->
<div id="footer" class="footer">
    <div style="padding: 15px;">
        &#32;&#169; Asio Security Bait Platform
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