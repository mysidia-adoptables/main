<html>
    <head>
        <meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1'>
        <title>Mysidia Adoptables v1.3.6 Upgrade Wizard</title>
        <link rel='stylesheet' type='text/css' href='../css/install.css'>
    </head>

    <body>

<?php

//Max Volume Installation Wizard
define("SUBDIR", "Install");
include("../inc/config.php");
$step = (int)preg_replace("/[^a-zA-Z0-9s]/", "", filter_input(INPUT_GET, "step"));

if ($step == 3) {
    try {
        $dsn = "mysql:host=" . DBHOST . ";dbname=" . DBNAME;
        $prefix = constant("PREFIX");
        $adopts = new PDO($dsn, DBUSER, DBPASS);
    } catch (PDOException $pe) {
        die("Could not connect to database, the following error has occurred: <br><b>{$pe->getmessage()}</b>");
    }

    $query = "UPDATE {$prefix}adoptables
          INNER JOIN {$prefix}shops ON {$prefix}shops.shopname = {$prefix}adoptables.shop
          SET {$prefix}adoptables.shop = {$prefix}shops.sid";
    $adopts->query($query);

    $query2 = "ALTER TABLE {$prefix}adoptables MODIFY `shop` INT(11) DEFAULT 0";
    $adopts->query($query2);

    $query3 = "UPDATE {$prefix}alternates
           INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}alternates.adopt
           SET {$prefix}alternates.adopt = {$prefix}adoptables.id";
    $adopts->query($query3);

    $query4 = "ALTER TABLE {$prefix}alternates MODIFY `adopt` INT(11) DEFAULT 0";
    $adopts->query($query4);

    $query5 = "UPDATE {$prefix}breeding
           INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}breeding.offspring
           SET {$prefix}breeding.offspring = {$prefix}adoptables.id";
    $adopts->query($query5);

    $query6 = "ALTER TABLE {$prefix}breeding MODIFY `offspring` INT(11) DEFAULT 0";
    $adopts->query($query6);

    $query7 = "UPDATE {$prefix}breeding
           INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}breeding.parent
           SET {$prefix}breeding.parent = {$prefix}adoptables.id";
    $adopts->query($query7);

    $query8 = "ALTER TABLE {$prefix}breeding MODIFY `parent` INT(11) DEFAULT 0";
    $adopts->query($query8);

    $query9 = "UPDATE {$prefix}breeding
           INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}breeding.mother
           SET {$prefix}breeding.mother = {$prefix}adoptables.id";
    $adopts->query($query9);

    $query10 = "ALTER TABLE {$prefix}breeding MODIFY `mother` INT(11) DEFAULT 0";
    $adopts->query($query10);

    $query11 = "UPDATE {$prefix}breeding
           INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}breeding.father
           SET {$prefix}breeding.father = {$prefix}adoptables.id";
    $adopts->query($query11);

    $query12 = "ALTER TABLE {$prefix}breeding MODIFY `father` INT(11) DEFAULT 0";
    $adopts->query($query12);

    $query13 = "UPDATE {$prefix}content
           INNER JOIN {$prefix}items ON {$prefix}items.itemname = {$prefix}content.item
           SET {$prefix}content.item = {$prefix}items.id";
    $adopts->query($query13);

    $query14 = "ALTER TABLE {$prefix}content MODIFY `item` INT(11) DEFAULT 0";
    $adopts->query($query14);

    $query15 = "UPDATE {$prefix}content SET date = '2021-08-08'";
    $adopts->query($query15);

    $query16 = "UPDATE {$prefix}content
           INNER JOIN {$prefix}groups ON {$prefix}groups.groupname = {$prefix}content.group
           SET {$prefix}content.group = {$prefix}groups.gid";
    $adopts->query($query16);

    $query17 = "ALTER TABLE {$prefix}content MODIFY `group` INT(11) DEFAULT 0";
    $adopts->query($query17);

    $query18 = "UPDATE {$prefix}folders_messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}folders_messages.fromuser
            SET {$prefix}folders_messages.fromuser = {$prefix}users.uid";
    $adopts->query($query18);

    $query19 = "ALTER TABLE {$prefix}folders_messages MODIFY `fromuser` INT(11) DEFAULT 0";
    $adopts->query($query19);

    $query20 = "UPDATE {$prefix}folders_messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}folders_messages.touser
            SET {$prefix}folders_messages.touser = {$prefix}users.uid";
    $adopts->query($query20);

    $query21 = "ALTER TABLE {$prefix}folders_messages MODIFY `touser` INT(11) DEFAULT 0";
    $adopts->query($query21);

    $query22 = "UPDATE {$prefix}friend_requests
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}friend_requests.fromuser
            SET {$prefix}friend_requests.fromuser = {$prefix}users.uid";
    $adopts->query($query22);

    $query23 = "ALTER TABLE {$prefix}friend_requests MODIFY `fromuser` INT(11) DEFAULT 0";
    $adopts->query($query23);

    $query24 = "UPDATE {$prefix}friend_requests
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}friend_requests.touser
            SET {$prefix}friend_requests.touser = {$prefix}users.uid";
    $adopts->query($query24);

    $query25 = "ALTER TABLE {$prefix}friend_requests MODIFY `touser` INT(11) DEFAULT 0";
    $adopts->query($query25);

    $query26 = "ALTER TABLE {$prefix}inventory DROP COLUMN category";
    $adopts->query($query26);

    $query27 = "UPDATE {$prefix}inventory
            INNER JOIN {$prefix}items ON {$prefix}items.itemname = {$prefix}inventory.itemname
            SET {$prefix}inventory.itemname = {$prefix}items.id";
    $adopts->query($query27);

    $query28 = "ALTER TABLE {$prefix}inventory CHANGE `itemname` `item` INT(11) DEFAULT 0";
    $adopts->query($query28);

    $query29 = "UPDATE {$prefix}inventory
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}inventory.owner
            SET {$prefix}inventory.owner = {$prefix}users.uid";
    $adopts->query($query29);

    $query30 = "ALTER TABLE {$prefix}inventory MODIFY `owner` INT(11) DEFAULT 0";
    $adopts->query($query30);

    $query31 = "UPDATE {$prefix}items
            INNER JOIN {$prefix}shops ON {$prefix}shops.shopname = {$prefix}items.shop
            SET {$prefix}items.shop = {$prefix}shops.sid";
    $adopts->query($query31);

    $query32 = "ALTER TABLE {$prefix}items MODIFY `shop` INT(11) DEFAULT 0";
    $adopts->query($query32);

    $query33 = "INSERT INTO {$prefix}items_functions (`function`, `intent`, `description`) VALUES ('Alts2', 'Adoptable', 'This item function defines items that change your adoptable to a random alternate form.')";
    $adopts->query($query33);

    $query34 = "UPDATE {$prefix}levels
            INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}levels.adoptiename
            SET {$prefix}levels.adoptiename = {$prefix}adoptables.id";
    $adopts->query($query34);

    $query35 = "ALTER TABLE {$prefix}levels CHANGE `adoptiename` `adopt` INT(11) DEFAULT 0";
    $adopts->query($query35);

    $query36 = "ALTER TABLE {$prefix}levels CHANGE `thisislevel` `level` INT(11) DEFAULT 0";
    $adopts->query($query36);

    $query37 = "ALTER TABLE {$prefix}messages CHANGE `id` `mid` INT(11) NOT NULL AUTO_INCREMENT";
    $adopts->query($query37);

    $query38 = "UPDATE {$prefix}messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}messages.fromuser
            SET {$prefix}messages.fromuser = {$prefix}users.uid";
    $adopts->query($query38);

    $query39 = "ALTER TABLE {$prefix}messages MODIFY `fromuser` INT(11) DEFAULT 0";
    $adopts->query($query39);

    $query40 = "UPDATE {$prefix}messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}messages.touser
            SET {$prefix}messages.touser = {$prefix}users.uid";
    $adopts->query($query40);

    $query41 = "ALTER TABLE {$prefix}messages MODIFY `touser` INT(11) DEFAULT 0";
    $adopts->query($query41);

    $query42 = "UPDATE {$prefix}modules
            INNER JOIN {$prefix}widgets ON {$prefix}widgets.name = {$prefix}modules.widget
            SET {$prefix}modules.widget = {$prefix}widgets.wid";
    $adopts->query($query42);

    $query43 = "ALTER TABLE {$prefix}modules MODIFY `widget` INT(11) DEFAULT 0";
    $adopts->query($query43);

    $query44 = "UPDATE {$prefix}owned_adoptables
            INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}owned_adoptables.type
            SET {$prefix}owned_adoptables.type = {$prefix}adoptables.id";
    $adopts->query($query44);

    $query45 = "ALTER TABLE {$prefix}owned_adoptables CHANGE `type` `adopt` INT(11) DEFAULT 0";
    $adopts->query($query45);

    $query46 = "UPDATE {$prefix}owned_adoptables
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}owned_adoptables.owner
            SET {$prefix}owned_adoptables.owner = {$prefix}users.uid";
    $adopts->query($query46);

    $query47 = "ALTER TABLE {$prefix}owned_adoptables MODIFY `owner` INT(11) DEFAULT 0";
    $adopts->query($query47);

    $query48 = "ALTER TABLE {$prefix}pounds ADD UNIQUE INDEX(aid)";
    $adopts->query($query48);

    $query49 = "ALTER TABLE {$prefix}pounds CHANGE poid poid int(11)";
    $adopts->query($query49);

    $query50 = "ALTER TABLE {$prefix}pounds DROP COLUMN poid";
    $adopts->query($query50);

    $query51 = "UPDATE {$prefix}pounds
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}pounds.firstowner
            SET {$prefix}pounds.firstowner = {$prefix}users.uid";
    $adopts->query($query51);

    $query52 = "ALTER TABLE {$prefix}pounds MODIFY `firstowner` INT(11) DEFAULT 0";
    $adopts->query($query52);

    $query53 = "UPDATE {$prefix}pounds
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}pounds.lastowner
            SET {$prefix}pounds.lastowner = {$prefix}users.uid";
    $adopts->query($query53);

    $query54 = "ALTER TABLE {$prefix}pounds MODIFY `lastowner` INT(11) DEFAULT 0";
    $adopts->query($query54);

    $query55 = "UPDATE {$prefix}pounds
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}pounds.currentowner
            SET {$prefix}pounds.currentowner = {$prefix}users.uid";
    $adopts->query($query55);

    $query56 = "ALTER TABLE {$prefix}pounds MODIFY `currentowner` INT(11) DEFAULT 0";
    $adopts->query($query56);

    $query57 = "DROP TABLE {$prefix}pound_settings";
    $adopts->query($query57);

    $query58 = "CREATE TABLE {$prefix}pounds_settings (psid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(40))";
    $adopts->query($query58);

    $query59 = "INSERT INTO {$prefix}pounds_settings VALUES (1, 'system', 'yes')";
    $adopts->query($query59);

    $query60 = "INSERT INTO {$prefix}pounds_settings VALUES (2, 'adopt', 'yes')";
    $adopts->query($query60);

    $query61 = "INSERT INTO {$prefix}pounds_settings VALUES (3, 'specieslimit', '')";
    $adopts->query($query61);

    $query62 = "INSERT INTO {$prefix}pounds_settings VALUES (4, 'cost', '50, 100')";
    $adopts->query($query62);

    $query63 = "INSERT INTO {$prefix}pounds_settings VALUES (5, 'costtype', 'percent')";
    $adopts->query($query63);

    $query64 = "INSERT INTO {$prefix}pounds_settings VALUES (6, 'levelbonus', '1')";
    $adopts->query($query64);

    $query65 = "INSERT INTO {$prefix}pounds_settings VALUES (7, 'leveltype', 'multiply')";
    $adopts->query($query65);

    $query66 = "INSERT INTO {$prefix}pounds_settings VALUES (8, 'number', '4, 5')";
    $adopts->query($query66);

    $query67 = "INSERT INTO {$prefix}pounds_settings VALUES (9, 'date', 'yes')";
    $adopts->query($query67);

    $query68 = "INSERT INTO {$prefix}pounds_settings VALUES (10, 'duration', 3)";
    $adopts->query($query68);

    $query69 = "INSERT INTO {$prefix}pounds_settings VALUES (11, 'owner', 'yes')";
    $adopts->query($query69);

    $query70 = "INSERT INTO {$prefix}pounds_settings VALUES (12, 'recurrence', 5)";
    $adopts->query($query70);

    $query71 = "INSERT INTO {$prefix}pounds_settings VALUES (13, 'rename', 'yes')";
    $adopts->query($query71);

    $query72 = "UPDATE {$prefix}promocodes
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}promocodes.user
            SET {$prefix}promocodes.user = {$prefix}users.uid";
    $adopts->query($query72);

    $query73 = "ALTER TABLE {$prefix}promocodes MODIFY `user` INT(11) DEFAULT 0";
    $adopts->query($query73);

    $query74 = "UPDATE {$prefix}promocodes
            INNER JOIN {$prefix}adoptables ON {$prefix}adoptables.type = {$prefix}promocodes.reward
            SET {$prefix}promocodes.reward = {$prefix}adoptables.id WHERE {$prefix}promocodes.type = 'Adopt'";
    $adopts->query($query74);

    $query75 = "UPDATE {$prefix}promocodes
            INNER JOIN {$prefix}items ON {$prefix}items.itemname = {$prefix}promocodes.reward
            SET {$prefix}promocodes.reward = {$prefix}items.id WHERE {$prefix}promocodes.type = 'Item'";
    $adopts->query($query75);

    $query76 = "ALTER TABLE {$prefix}promocodes MODIFY `reward` INT(11) DEFAULT 0";
    $adopts->query($query76);

    $query77 = "UPDATE {$prefix}shoutbox
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}shoutbox.user
            SET {$prefix}shoutbox.user = {$prefix}users.uid";
    $adopts->query($query77);

    $query78 = "ALTER TABLE {$prefix}shoutbox MODIFY `user` INT(11) DEFAULT 0";
    $adopts->query($query78);

    $query79 = "ALTER TABLE {$prefix}systems RENAME TO `{$prefix}systems_settings`";
    $adopts->query($query79);

    $query80 = "ALTER TABLE {$prefix}themes ADD COLUMN `usergroup` INT(11) DEFAULT 0 AFTER `themefolder`,
            ADD COLUMN `fromdate` VARCHAR(15) AFTER `usergroup`, ADD COLUMN `todate` VARCHAR(15) AFTER `fromdate`";
    $adopts->query($query80);

    $query81 = "UPDATE {$prefix}trade
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}trade.sender
            SET {$prefix}trade.sender = {$prefix}users.uid";
    $adopts->query($query81);

    $query82 = "ALTER TABLE {$prefix}trade MODIFY `sender` INT(11) DEFAULT 0";
    $adopts->query($query82);

    $query83 = "UPDATE {$prefix}trade
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}trade.recipient
            SET {$prefix}trade.recipient = {$prefix}users.uid";
    $adopts->query($query83);

    $query84 = "ALTER TABLE {$prefix}trade MODIFY `recipient` INT(11) DEFAULT 0";
    $adopts->query($query84);

    $query85 = "ALTER TABLE {$prefix}users_contacts DROP COLUMN username";
    $adopts->query($query85);

    $query86 = "ALTER TABLE {$prefix}users_profile DROP COLUMN username";
    $adopts->query($query86);

    $query87 = "ALTER TABLE {$prefix}users_status RENAME TO `{$prefix}users_permissions`";
    $adopts->query($query87);

    $query88 = "UPDATE {$prefix}visitor_messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}visitor_messages.fromuser
            SET {$prefix}visitor_messages.fromuser = {$prefix}users.uid";
    $adopts->query($query88);

    $query89 = "ALTER TABLE {$prefix}visitor_messages MODIFY `fromuser` INT(11) DEFAULT 0";
    $adopts->query($query89);

    $query90 = "UPDATE {$prefix}visitor_messages
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}visitor_messages.touser
            SET {$prefix}visitor_messages.touser = {$prefix}users.uid";
    $adopts->query($query90);

    $query91 = "ALTER TABLE {$prefix}visitor_messages MODIFY `touser` INT(11) DEFAULT 0";
    $adopts->query($query91);

    $query92 = "UPDATE {$prefix}vote_voters
            INNER JOIN {$prefix}users ON {$prefix}users.username = {$prefix}vote_voters.username
            SET {$prefix}vote_voters.username = {$prefix}users.uid";
    $adopts->query($query92);

    $query93 = "ALTER TABLE {$prefix}vote_voters CHANGE `username` `userid` INT(11) DEFAULT 0";
    $adopts->query($query93);

    rename("../inc/config.php", "../config.php");
    rename("../inc/config_forums.php", "../config_forums.php");

    // All done, cheers!
    ?>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td height='60px' valign='top' bgcolor='#FF3300'>
                    <div align='left'>
                        <p>
                            <span class='style1'>Mysidia Adoptables Upgrade Wizard <br>
                                <span class='style2'>Successfully upgrade Mysidia Adoptables to version v1.3.6 </span>                                    
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='600px' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>
                        <br>
                        <span class='style2'>
                            Congratulations, you have successfully upgraded to Mysidia Adoptables version v1.3.6! 
                            We strongly advise you to remove this upgrader before working on your site again.
                        </span>
                    </p>
            </tr>
        </table>

<?php
} elseif ($step == 2) {
    $flag = 0;
    ?>
        
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td height='60px' valign='top' bgcolor='#FF3300'>
                    <div align='left'>
                        <p>
                            <span class='style1'>
                                Mysidia Adoptables Upgrade Wizard <br>
                                <span class='style2'>Step 2: Add/Modify database tables </span>                                   
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='600px' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>
                        <br>
                        <span class='style2'>
                            This page will check information provided in your config.php file, which should not be a problem unless you have manually edited it by yourself.  
	                         Please make sure your file config.php is writable and your database information is provided correctly. Then click on the continue button below to proceed.
                        </span>
                    </p>
<?php
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    //Check the file permissions here and echo the results...

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    if (is_writable("../inc/config.php")) {
        echo "<p align='left'><img src='../templates/icons/yes.gif'> <b>PASS:</b>  Your config.php file is writable and is connected to database.<br></p>";
    } else {
        echo "<b><p align='left'><img src='../templates/icons/no.gif'> FAIL:</b> Your config.php file is not writable.  Please CHMOD config.php so that it is executable.<br></p>";
        $flag = 1;
    }

    if (is_dir("../controller/main")) {
        echo "<p align='left'><img src='../templates/icons/yes.gif'> <b>PASS:</b>  Your main site directory exists and is accessible.<br></p>";
    } else {
        echo "<b><p align='left'><img src='../templates/icons/warning.gif'> WARNING:</b> Something is very very wrong with your main site file. Please make sure it exists and CHMOD the directory to 644.<br></p>";
        $flag = 1;
    }

    if (is_dir("../controller/admincp")) {
        echo "<p align='left'><img src='../templates/icons/yes.gif'> <b>PASS:</b>  Your new admincp directory exists and is accessible.<br></p>";
    } else {
        echo "<b><p align='left'><img src='../templates/icons/warning.gif'> WARNING:</b> Something is very very wrong with your new admincp directory. Please make sure it exists and CHMOD the directory to 644.<br></p>";
        $flag = 1;
    }

    if (!is_dir("../admincp")) {
        echo "<p align='left'><img src='../templates/icons/yes.gif'> <b>PASS:</b>  Your old admincp directory is already removed.<br></p>";
    } else {
        echo "<b><p align='left'><img src='../templates/icons/warning.gif'> WARNING:</b> You still have the old admincp directory. Please make sure it is deleted before proceeding or you will not be able to access ACP after upgrading.<br></p>";
        $flag = 1;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //END THE FILE PERMISSIONS CHECKS
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    if ($flag == 0) {
        ?>

                    <br>
                    <p align='right'>
                        <br>
                        <a href='upgrade.php?step=3'><span class='style2'><img src='../templates/icons/yes.gif' border=0> Yes, I wish to Continue</span></a> 
                    </p>
                </td>
                
<?php
    }
    ?>

            </tr>
        </table>
<?php
} else {

    ?>
        <table border='0' cellpadding='0' cellspacing='0'>
            <tr>
                <td height='57' valign='top' bgcolor='#FF3300'>
                    <div align='left'>
                        <p>
                            <span class='style1'>
                                Mysidia Adoptables Upgrade Wizard <br>
                                <span class='style2'>Step 1: Welcome and License Agreement</span>                                    
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='600px' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>
                        <br>
                        <span class='style2'>
                            This upgrader will update your Mysidia Adoptables to version v1.3.6. 
                            Before you upgrade, however, please make sure that your Mysidia Adoptables version is currently at v1.3.5. 
                            Also, you must agree to the Mysidia Adoptables License Agreement as it is outlined below:
                        </span>
                    </p>
                    <p align='left' class='style3'>
                        <u>Mysidia Adoptables License Agreement: </u>
                    </p>
                    <p align='left' class='style4'>
                        Mysidia Adoptables is licensed under a Free for Non-Commercial Use license, terms of this license are interpreted as the following: 
                    </p>
                    <p align='left' class='style4'>
                        ---Commercial use of the product on your server is OK, while the script may not be redistributed in whole or as part of another script. 
                    </p>
                    <p align='left' class='style4'>
                        ---You must post credit to Mysidia Adoptables (http://www.mysidiaadoptables.com) and keep it visible on all pages unless you have created a credits page.  
                    </p>
                    <p align='left' class='style4'>
                        ---You can create modifications of this script (or hire freelancers to create modifications of this script) at any time. 
                    </p>
                    <p align='left' class='style4'>
                        For permissions beyond the scope of this license please Contact (Hall of Famer) at halloffamer@mysidiaadoptables.com.
                    </p>
                    <p align='right' class='style2'>
                        <a href='upgrade.php?step=2'><img src='../templates/icons/yes.gif' border=0> I Agree - Continue Installation</a>  
                    </p>
                </td>
            </tr>
        </table>

<?php
}
?>

    </body>
</html>