<?php

//This file creates a new admin user for Max Volume
define("SUBDIR", "Install");
include("../config.php");

//Now connecting to the adoptables database
try{
    $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
    $prefix = constant("PREFIX");
    $adopts = new PDO($dsn, DBUSER, DBPASS);
}
catch(PDOException $pe){
    die("Could not connect to database, the following error has occurred: <br><b>{$pe->getmessage()}</b>");  
}


$username = preg_replace("/[^a-zA-Z0-9\\040.]/", "", filter_input(INPUT_POST, "username"));
$pass1 = filter_input(INPUT_POST, "pass1");
$pass2 = filter_input(INPUT_POST, "pass2");
$birthday = filter_input(INPUT_POST, "birthday");
$email = preg_replace("/[^a-zA-Z0-9@._-]/", "", filter_input(INPUT_POST, "email"));

if($username == "" || $pass1 == "" || $pass2 == "" || $email == ""){
    die("Something important was left blank.  Please try again!");
}

if($pass1 != $pass2){
    die("Passwords do not match.  Please go back and correct this.");
}

$date = date('Y-m-d');
$adopts->query("INSERT INTO {$prefix}users (uid, username, salt, password, email, ip, usergroup, birthday, membersince, money, friends)
VALUES (NULL, '{$username}', '', '{$pass1}', '{$email}', '{$_SERVER['REMOTE_ADDR']}', '1', '{$birthday}', '{$date}', '1000', '')");

$stmt = $adopts->query("SELECT uid FROM {$prefix}users WHERE username = '{$username}'");
$uid = $stmt->fetchColumn();
if(!$uid) die("Error creating admin user.");
$adopts->query("INSERT INTO {$prefix}users_contacts (uid, website, facebook, twitter, aim, yahoo, msn, skype)
VALUES ({$uid}, '', '', '', '', '', '', '')");

$adopts->query("INSERT INTO {$prefix}users_options (uid, newmessagenotify, pmstatus, vmstatus, tradestatus, theme)
VALUES ({$uid}, '1', '0', '0', '0', 'main')");
	
$adopts->query("INSERT INTO {$prefix}users_permissions (uid, canlevel, canvm, canfriend, cantrade, canbreed, canpound, canshop)
VALUES ({$uid}, 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes')");

$adopts->query("INSERT INTO {$prefix}users_profile (uid, avatar, bio, color, about, favpet, gender, nickname)
VALUES ({$uid}, 'templates/icons/default_avatar.gif', '', '', '', '0', 'unknown', '')");

//Now it's time for our new admin to configure their basic site settings...
?>

<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        <title>Mysidia PHP Adoptables Installation Wizard</title>
        <link rel='stylesheet' type='text/css' href='../css/install.css'>
    </head>

    <body>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td height='60px' valign='top' bgcolor='#FF3300'>
                    <div align='left'>
                        <p>
                            <span class='style1'>
                                Mysidia Adoptables Installation Wizard <br>
                                <span class='style2'>Step 5: Configure Site Settings </span>                                    
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='600px' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>
                        <br>
                        <span class='style2'>This page allows you to configure basic site settings such as site name, slogan and default theme...</span>
                    </p>
                    <form name='form1' method='post' action='sitesetting.php'>
                        <p class='style2'>
                            Default Theme: 
                            <select id='theme' name='theme'>
                                <option value='main' selected>Main</option>
                                <option value='green'>Green</option>
                                <option value='elements'>Elements</option>
                            </select>
                        </p>
                        <p class='style2'>
                            The default theme can be changed anytime through the usage of admincp, you may also do this with Style switcher.
                        </p>
                        <p class='style2'>
                            Browser Title: 
                            <input name='browsertitle' type='text' id='browsertitle' maxlength='50' value='Mysidia Adoptables v1.3.6'>
                        </p>
                        <p>
                            <span class='style2'>Now it is time to give your site a brand new name!</span>
                        </p>
                        <p>
                            <span class='style2'>
                                Site Name: 
                                <input name='sitename' type='text' id='sitename' maxlength='50' value='My Adoptables Site'>
                            </span>
                        </p>
                        <p>                            
                            <span class='style2'>
                                The Slogan of your site is: 
                                <input name='slogan' type='text' id='slogan' maxlength='50' value='Your Site Slogan'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                The Pepper Code of your site is:<br>
                                <b>Note this feature is deprecated and will be removed in Mys v1.4.0!</b> <br>
                                <input name='peppercode' type='text' id='peppercode' maxlength='50' value='6QoE5En82U8I91N'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                Security Question:(This can be used to stop bots from registering!) 
                                <input name='securityquestion' type='text' id='securityquestion' maxlength='50' value='2+1=?'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                The Answer of Security Question is: 
                                <input name='securityanswer' type='text' id='securityanswer' maxlength='50' value='3'>
                            </span>
                        </p>
                        <p>
                            <input name='usealtbbcode' type='checkbox' id='usealtbbcode' value='yes' checked> 
                            Enable Alternative bbcodes on your Site 
                        </p>
                        <p>
                            <span class='style2'>
                                The Cash Name of your site: 
                                <input name='cost' type='text' id='cost' maxlength='50' value='Mysidian dollar'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                User's Starting Money: 
                                <input name='startmoney' type='text' id='startmoney' maxlength='6' value='1000'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                # Rows for Pagination: 
                                <input name='pagination' type='text' id='startmoney' maxlength='6' value='10'>
                            </span>
                        </p>
                        <input name='username' type='hidden' id='username' value='<?php echo $username; ?>'>
                        <input name='pass1' type='hidden' id='pass1' value='<?php echo $pass1; ?>'>		
                        <p>
                            <input type='submit' name='Submit' value='Submit and Continue Installation'>
                        </p>
                        <p>&nbsp;</p>
                    </form>      
                    <p align='left'>&nbsp;</p>
                    <p align='right'><br></p>
                </td>
            </tr>
        </table>
    </body>
</html>