<?php

//This file creates a new admin user for Max Volume
define("SUBDIR", "Install");
include("../config.php");
require("../resource/native/objective.php");
require("../resource/native/mysobject.php");
require("../resource/utility/password.php");

//Now connecting to the adoptables database
try {
    $dsn = "mysql:host=" . DBHOST . ";dbname=". DBNAME;
    $prefix = constant("PREFIX");
    $adopts = new PDO($dsn, DBUSER, DBPASS) or die("Cannot connect to database.");
} catch (PDOException $pe) {
    die("Could not connect to database, the following error has occurred: <br><b>{$pe->getmessage()}</b>");
}

//The grabanysetting function needs to be defined here

$post = filter_input_array(INPUT_POST);
$theme = $post["theme"];
$browsertitle = $post["browsertitle"];
$sitename = $post["sitename"];
$slogan = $post["slogan"];
$peppercode = $post["peppercode"];
$securityquestion = $post["securityquestion"];
$securityanswer = $post["securityanswer"];
$usealtbbcode = $post["usealtbbcode"];
$cost = $post["cost"];
$startmoney = (int)$post["startmoney"];
$pagination = (int)$post["pagination"];
$username = $post["username"];

$stmt = $adopts->query("SELECT username, password, email FROM {$prefix}users WHERE username = '{$username}'");
$admin = $stmt->fetchObject();
$password = new Resource\Utility\Password();
$encryptpass = $password->hash($admin->password);

// Update system settings
if ($theme == "" || $browsertitle == "" || $sitename == "" || $slogan == "" || $cost == "" || $startmoney == 0) {
    die("Something important was left blank.  Please try again!");
}

$query = "INSERT INTO {$prefix}settings (name, value) VALUES ('theme', '{$theme}')";
$adopts->query($query);

$query2 = "INSERT INTO {$prefix}settings (name, value) VALUES ('browsertitle', '{$browsertitle}')";
$adopts->query($query2);

$query3 = "INSERT INTO {$prefix}settings (name, value) VALUES ('sitename', '{$sitename}')";
$adopts->query($query3);

$query4 = "INSERT INTO {$prefix}settings (name, value) VALUES ('slogan', '{$slogan}')";
$adopts->query($query4);

$query5 = "INSERT INTO {$prefix}settings (name, value) VALUES ('systemuser', '{$admin->username}')";
$adopts->query($query5);

$query6 = "INSERT INTO {$prefix}settings (name, value) VALUES ('systememail', '{$admin->email}')";
$adopts->query($query6);

$query7 = "INSERT INTO {$prefix}settings (name, value) VALUES ('admincontact', '{$admin->email}')";
$adopts->query($query7);

$query8 = "INSERT INTO {$prefix}settings (name, value) VALUES ('peppercode', '{$peppercode}')";
$adopts->query($query8);

$query9 = "INSERT INTO {$prefix}settings (name, value) VALUES ('securityquestion', '{$securityquestion}')";
$adopts->query($query9);

$query10 = "INSERT INTO {$prefix}settings (name, value) VALUES ('securityanswer', '{$securityanswer}')";
$adopts->query($query10);

$query11 = "INSERT INTO {$prefix}settings (name, value) VALUES ('gdimages', 'yes')";
$adopts->query($query11);

$query12 = "INSERT INTO {$prefix}settings (name, value) VALUES ('usealtbbcode', '{$usealtbbcode}')";
$adopts->query($query12);

$query13 = "INSERT INTO {$prefix}settings (name, value) VALUES ('cashenabled', 'yes')";
$adopts->query($query13);

$query14 = "INSERT INTO {$prefix}settings (name, value) VALUES ('cost', '{$cost}')";
$adopts->query($query14);

$query15 = "INSERT INTO {$prefix}settings (name, value) VALUES ('startmoney', '{$startmoney}')";
$adopts->query($query15);

$query16 = "INSERT INTO {$prefix}settings (name, value) VALUES ('pagination', '{$pagination}')";
$adopts->query($query16);

$query17 = "UPDATE {$prefix}users SET money = '{$startmoney}' WHERE username = '{$username}'";
$adopts->query($query17);

$query18 = "UPDATE {$prefix}users SET password = '{$encryptpass}' WHERE username = '{$username}'";
$adopts->query($query18);

//We are DONE with the install!  Yay!!!!!!!!!!
?>

<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        <title>Mysidia Adoptables v1.3.6 Installation Wizard</title>
        <link rel='stylesheet' type='text/css' href='../css/install.css'>
    </head>

    <body>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td height='60px' valign='top' bgcolor='#FF3300'>
                    <div align='left'>
                        <p>
                            <span class='style1'>
                                Mysidia Adoptables v1.3.6 Installation Wizard <br>
                                <span class='style2'>Step 6: Installation Complete! </span>                                   
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='600px' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>&nbsp;</p>
                    <p align='left'><span class='style2'>Hey There <?php echo $username; ?></span></p>
                    <p align='left'>Mysidia Adoptables v1.3.6 has been installed on your site and is ready for your use! Before you get going, there's a few things you should know: </p>
                    <blockquote>
                        <p align='left'>
                            1. Your install of Mysidia Adoptables is located at: <strong><?php echo DOMAIN.SCRIPTPATH; ?></strong>
                        </p>
                        <p align='left'>
                            2. Your Admin CP is located at: <strong><?php echo DOMAIN.SCRIPTPATH; ?>/admincp</strong>
                        </p>
                        <p align='left'>
                            You will need to <a href='../login'>Log In</a> to your installation of Mysidia Adoptables before you can access the Admin CP.
                        </p>
                        <p align='left'>
                            3. You should really CHMOD config.php back to 644 so that it is not writable.
                        </p>
                        <p align='left'>
                            4. You MUST delete the install directory for security. You wouldn't want someone installing over your site, now would you?
                        </p>
                        <p align='left'>
                            5. For official script support you can visit <strong><a href='https://forums.mysidiaadoptables.com' target='_blank'>https://forums.mysidiaadoptables.com</a></strong> or just click on the Script Support link in your Admin CP for quick support.
                        </p>
                        <p align='left'>
                            6. You should log in to your Admin CP and click on the <strong>Site Settings</strong> option right away to customize your installation of Mysidia Adoptables . Right now your site is running just the default data which doesn't look that flattering to the outside world. Spice it up! 
                        </p>
                        <p align='center'>
                            Thank you for installing Mysidia Adoptables, a proud product from <a href='https://mysidiaadoptables.com' target='_blank'>Mysidia RPG Inc.</a>. 
                        </p>
                        <p align='center' class='style4'><a href='../'>View Your Website</a></p>     
                    </blockquote>
                </td>
            </tr>
        </table>
    </body>
</html>