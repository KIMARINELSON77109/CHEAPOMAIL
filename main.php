<?php
require "connect.php";
require "sanitize.php";

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') 
{
     
//###########################user to be added###################################

    $fname = $_POST["firstname"];
    $lname = $_POST["lastname"];
    $uname = $_POST["username"];
    $pword = md5($_POST["password"]);
    
//#################################login info###################################

    $LoginName = check_input($_POST["LoginName"]);
    $LoginPwd = check_input(md5($_POST["LoginPwd"]));
    $login = $_POST["login"];

//#######################message details to be sent############################
    $subj = $_POST["subject"];
    $recps = $_POST["recipients"];
    $body = $_POST["body"];
    
//##############################id of message read##############################

    $read_id = $_POST["read_id"];
    $getmail = $_POST["getmail"];
    
//##############################indicate logout is true#########################

    $Logout = $_POST["logout"];
    
//######################check login info in db and respose too ajax request############################################################

    if(isset($LoginName) && isset($LoginPwd) && isset($login)){
        $sql = "SELECT * FROM users WHERE username = '$LoginName' AND password_digest = '$LoginPwd';";
        $stmt = $db->query($sql);
        $res = $stmt->fetch();
        
        if($res != null)
        {
            $_SESSION["username"] = $res["username"];
            $_SESSION["user_id"] = $res["id"];
            echo "home.html";
        }
        else
        {
            echo "No User Found";
        }
    }
    
//#############################add a user to db and response to ajax saying user is added################################################

    if (isset($uname) && isset($pword) && isset($fname) && isset($lname) && !isset($login))
    {
        $fname = check_input($fname);$lname = check_input($lname);$uname = check_input($uname);$pword = check_input($pword);
        
        $sql = "INSERT INTO users(firstname, lastname, username, password_digest) VALUES('$fname', '$lname', '$uname', '$pword');";
        $res = $db->query($sql);
        
        if(res == true)
        {
            echo 'Successfully Added User';
        }
    }
    
//#########################add a message to db and response to ajax saying message is sent################################################
    if (isset($recps) && isset($subj) && isset($body))
    {
//##############################get id of sender################################
        $sender_id = $_SESSION["user_id"];
        
//###################get current date and time##################################
        $sent_date = date("Y-m-d h:i:s");
        
//##############################split strings by comma##########################  
        $recps = explode(",", $recps); 
    
        //insert message for each recipient
        foreach($recps as $recp){
            
            //get id of recipient
            $stmt_2 = $db->query("SELECT id FROM users WHERE username = '$recp'");
            $res_ = $stmt_2->fetch();
            $recp_id = $res_["id"];
    
            
            $sql = "INSERT INTO messages (recipient_ids, user_id, subject, body, date_sent) VALUES('$recp_id', '$sender_id', '$subj', '$body', '$sent_date');";
            $db->exec($sql);
        }
        echo 'Message Sent';
    }
    
//###############################get messages from db for user logged in###################################

    if (isset($getmail)) 
    {

        //use user id of login user to retrieve messages
            $recp = $_SESSION["user_id"];
            $sql1 = "SELECT * FROM messages WHERE recipient_ids = '$recp' ORDER BY date_sent LIMIT 10;";
            $stmt = $db->query($sql1);
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $sql2 = "SELECT message_id FROM messages_read;";
            $stmt2 = $db->query($sql2);
            $res2 = $stmt2->fetchAll(PDO::FETCH_COLUMN, 0);
            
            if(count($res) == 0)
            {
                echo "<h2>No Mail Found</h2>";
            }
            
            else
            {
                foreach($res as $mail)
                {
                    
                    $sql = "SELECT username FROM users WHERE id = '" . $mail["user_id"] . "';";
                    $new = $db->query($sql);
                    $sendr = $new->fetch();
                    
                    if (in_array($mail["id"], $res2)){
                        echo '<div class="mail read">';
                        echo '<p>------Message Read------</p>';
                    }
                    else
                    {
                        echo '<div class="mail unread">';
                    }
                    
                    echo '<p>FROM: ' . $sendr["username"] . '       ,      ' .'SUBJECT: ' . $mail["subject"] .'</p>';
                    echo '<p class="recv">MESSAGE: ' . $mail["body"] . '</p>';
                    echo '<input type="submit" class="showbutton" value="Read"/>';
                    echo '<p class="hide">' . $mail["id"] . '</p>';
                    echo '</div>';
                }
            }
        }
    
//##########################################add read messages to the message_read table###########################################
    if(isset($read_id)){
        $read_date = date("Y/m/d");
        $userid = $_SESSION["user_id"];
        
        $stat = $db->query("SELECT message_id FROM messages_read;");
        $arr = $stat->fetchAll(PDO::FETCH_COLUMN, 0);
        
        //so that message is only added to messages_read once
        if (in_array($read_id, $arr) == false){
            $sql = "INSERT INTO messages_read(message_id, reader_id, date_read) VALUES('$read_id', '$userid', '$read_date');";
            $db->exec($sql);
        
            echo "Read";
        }
    }
    
//#################################logout#######################################
    if($Logout == "true")
    {
        session_unset();
        session_destroy();
    }
}
?>