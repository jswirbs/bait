<?php 

    require("common.php"); 

    if(!empty($_SESSION['user'])) 
    { 
        header("Location: overview.php"); 

        die("Redirecting to overview.php"); 
    }  
     
    // stores username incase password is typed incorrectly
    $submitted_email = ''; 

    // potential error message
    $message = "";
     
    if(!empty($_POST)) 
    { 
        // this query retreives the user's information from the database using 
        // their email. 
        $query = " 
            SELECT 
                id, 
                username, 
                password, 
                salt, 
                email 
            FROM users 
            WHERE 
                email = :email 
        "; 
         
        // the parameter values 
        $query_params = array( 
            ':email' => $_POST['email'] 
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
         
        // has user successfully loged in or not
        $login_ok = false; 
         
        // retrieves the user data from the database.  If $row is false, then 
        // the username they entered is not registered. 
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
            else 
            {
                $message = "Password incorrect.";
            }
        } 
        else 
        {
            $message = "Email not found.";
        }
         
        
        if($login_ok) 
        { 
            // removes salt and password values from $row array before it is
            // stored in the $_SESSION (not necessary as it's on server side, but why not remove sensitive data)
            unset($row['salt']); 
            unset($row['password']); 
             
            // stores the user's data into the session at the index 'user'. 
            // We check this index on private pages to determine whether 
            // or not the user is logged in, and/or retrieve the user's details. 
            $_SESSION['user'] = $row; 
             
            // redirect 
            header("Location: overview.php"); 
            die("Redirecting to your page."); 
        } 
        else 
        { 
            // show them username again so they only have to re-enter password.  
            // uses htmlentities (on user submitted value) prevents XSS attacks.  
            $submitted_email = htmlentities($_POST['email'], ENT_QUOTES, 'UTF-8'); 
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
<div class="login">
    <div style="font-size: 27px; color: #3f0f91; padding-top: 30px; padding-right: 10px;">Bait Platform</div>
    <div>
    <br>
        <form action="login" method="post">  
            <input type="text" name="email" placeholder="Email" value="<?php echo $submitted_email?>" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d;" size="30" required/>
            <br />
            <input type="password" name="password" placeholder="Password" value="" style="font-size: 18px; border: none; border-bottom: 1px solid #2d2d2d; margin-top: 5px;" size="30" required/> 
            <br />
            <?php
                if ($message != "") {
                    echo '<div style="color: red; margin-top: 8px;">';
                    echo $message;
                    echo '</div>';
                }
            ?>
            <br />
            <input type="submit" value="Login" class="submitBtn" style="font-size: 18px;" /> 
            <br />
            <br />
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