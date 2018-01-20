<?php 

    require("common.php"); 

    $submitted_email = "";
    $submitted_username = "";

    // potential error message
    $message = "";

    // if statement checks to determine whether the registration form has been submitted yet
    if(!empty($_POST)) 
    { 
        if (strlen($_POST['password']) < 6) 
        {
            $message = "Password must be at least 6 characters."; 
        }
        if(!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) 
        { 
            $message = "Please enter a valid E-Mail Address"; 
        } 
        else 
        {
            // SQL query to verify email address is not already in use
            $query = " 
                SELECT 
                    1 
                FROM users 
                WHERE 
                    email = :email 
            "; 
         
            $query_params = array( 
                ':email' => $_POST['email'] 
            ); 
         
            try 
            { 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                // only output error message for testing --> $ex->getMessage()
                //die("Failed to run query: " . $ex->getMessage()); 
                die("Failed to connect to the database.\n Please contact Asio Security if the problem persists."); 
            } 
         
            $row = $stmt->fetch(); 
         
            if($row) 
            { 
                $message = "This email address has already been registered."; 
            } 
        }

        $username = $_POST['username'];
        if (empty($_POST['username'])) {
            $username = $_POST['email'];
        }

        // check phone number and format it if necessary
       /* $phone_number = $_POST['phone_number'];
        if ($message == "") 
        {
            // eliminate every char except 0-9
            $phone_number = preg_replace("/[^0-9]/", '', $phone_number);
            
            // eliminate leading 1 if its there
            if (strlen($phone_number) == 11) 
            {
                $phone_number = substr($phone_number, 1);
            }
            
            if (strlen($phone_number) != 10)
            {
                $message = "Please use a valid 10 digit phone number.";
            }
        }

        // turns secondary verification on or off depending on submitted checkbox
        $secondary_v = 0;
        if(!empty($_POST['secondary_v'])) {
            $secondary_v = 1;
        } */
        $phone_number = 0;
        $secondary_v = 0;
         
        if ($_POST['password'] != $_POST['password2']) 
        {
            $message = "The passwords do not match!";
        }

        if ($message == "") 
        {
            // INSERT query adds new rows to database table (still uses tokens to prevent SQL injection)
            $query = " 
                INSERT INTO users ( 
                    username, 
                    password, 
                    salt, 
                    email,
                    phone_number,
                    secondary_v
                ) VALUES ( 
                    :username, 
                    :password, 
                    :salt, 
                    :email,
                    :phone_number,
                    :secondary_v
                ) 
            "; 
             
            // a salt is randomly generated here. The following statement generates 
            // a hex (> readability than binary) representation of an 8 byte salt. 
            $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
             
            // this hashes the password with the salt so that it can be stored securely 
            // in database. The output is a 64 byte hex string representing the 
            // 32 byte sha256 hash of the password.
            $password = hash('sha256', $_POST['password'] . $salt); 
             
            // hashes value 65536 more times (hinders bruteforcing - must compute hash 65536 times for each attempt)
            for($round = 0; $round < 65536; $round++) 
            { 
                $password = hash('sha256', $password . $salt); 
            } 
             
            // prepare tokens for insertion into the SQL query. Only stores hashed
            // version of password. Salt is stored in plaintext form (not a risk)
            $query_params = array( 
                ':username' => $username, 
                ':password' => $password, 
                ':salt' => $salt, 
                ':email' => $_POST['email'], 
                ':phone_number' => $phone_number,
                ':secondary_v' => $secondary_v
            ); 
             
            try 
            { 
                // execute the query to create the user 
                $stmt = $db->prepare($query); 
                $result = $stmt->execute($query_params); 
            } 
            catch(PDOException $ex) 
            { 
                // only output error message for testing --> $ex->getMessage()
                //die("Failed to run query: " . $ex->getMessage() . " -- " . $phone_number); 
                die("Failed to connect to the database.\n Please contact Asio Security if the problem persists."); 
            } 

            // redirects the user back to login page after they register 
            header("Location: about.html"); 
             
            // Calling die or exit after performing a redirect using the header function 
            // is critical. This stops the rest of your PHP script from executing
            // and being sent to the user.
            die("Redirecting...");

        }
        else 
        {
            $submitted_email = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8');
            $submitted_username = htmlentities($_POST['username'], ENT_QUOTES, 'UTF-8');
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
<div style="color: #3f0f91; padding: 15px 20px; font-size: 35px;">
    <a href="https://www.asiosecurity.com/" class="noHighlight"><img src="img/full_logo.png" height="100"></a>
</div>
<div class="register">
    <div style="font-size: 27px; color: #3f0f91; padding-top: 30px; padding-right: 10px;">Bait Platform</div>
    <div>
        <div style="font-size: 23px; padding: 20px 0;">Register Your Demo Account</div> 
        <form action="register-demo" method="post"> 
            <input type="text" name="email" placeholder="Email" value="<?php if(isset($_GET['e'])) {echo $_GET['e'];} else {echo $submitted_email;} ?>" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d;" size="30" required/>
            <br />
            <!--<input type="text" name="phone_number" placeholder="Phone Number" value="" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d; margin-top: 6px;" size="30" required/>
            <br />-->
            <!--<i style="font-size: 15px;">Enable secondary phone verification:</i>
            <input type="checkbox" name="secondary_v" value="yes" style="font-size: 15px;" />
            <br />-->
            <input type="password" name="password" placeholder="Password" value="" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d; margin-top: 6px;" size="30" required/> 
            <br />
            <input type="password" name="password2" placeholder="Confirm Password" value="" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d; margin-top: 6px;" size="30" required/> 
            <br />
            <?php 
                if ($message != "") {
                    echo '<div style="color: red; margin-top: 8px;">';
                    echo $message;
                    echo '</div>';
                }
            ?>
            <br /> 
            <input type="submit" value="Register" class="submitBtn" style="font-size: 18px;" /> 
            <input type="hidden" name="username" value="<?php if(isset($_GET['u'])) {echo $_GET['u'];} else {echo $submitted_username;} ?>" />
        </form>
    </div>
</div>
<div id="footer">
    <div style="position:absolute; left:0; bottom:0; padding: 10px;">
        &#32;&#169; Asio Security, 2017
    </div>
</div>
<script type="text/javascript">
    function onLoad() {
        var year = new Date().getFullYear();
        document.getElementById('footer').innerHTML = '<div style="position:absolute; left:0; bottom:0; padding: 10px;">&#32;&#169; Asio Security, ' + year + '</div>';
    }
</script>
</body>
</html>
