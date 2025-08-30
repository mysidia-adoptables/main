<?php

//This file writes the config.php file and then inserts the database info into the database...

define("SUBDIR", "Install");
$post = filter_input_array(INPUT_POST);
$dbhost = $post['dbhost'];
$dbuser = $post['dbuser'];
$dbpass = $post['dbpass'];
$dbname = $post['dbname'];
$domain = $post['domain'];
$scriptpath = $post['scriptpath'];
$prefix = $post['prefix'];


//Check again that config.php is writable...

$filename = "../config.php";

if (!is_writable($filename)) {
    die("Your config.php file is not writable.  Cannot proceed!");
}

if ($dbuser == "" || $dbpass == "" || $dbname == "" || $domain == "" || $prefix == "") {
    die("Something required was left blank. Please go back and try again.");
}

//Begin writing the config.php file...

$configdata = "<?php
//Mysidia Adoptables Site Configuration File

define('DBHOST', '{$dbhost}');             //DB Hostname
define('DBUSER', '{$dbuser}');             //DB Username
define('DBPASS', '{$dbpass}');             //DB Password
define('DBNAME', '{$dbname}');             //Your database name
define('DOMAIN', '{$domain}');             //Your domain name (No http, www or . )
define('SCRIPTPATH', '{$scriptpath}');     //The folder you installed this script in
define('PREFIX', '{$prefix}');
?>";

//Write the config.php file...

$file = fopen('../config.php', 'w');
fwrite($file, $configdata);
fclose($file);

//Connect to the database and insert the default data.....
try {
    $dsn = "mysql:host={$dbhost};dbname={$dbname}";
    $adopts = new PDO($dsn, $dbuser, $dbpass);
} catch (PDOException $pe) {
    die("Could not connect to database, the following error has occurred: <br><b>{$pe->getmessage()}</b>");
}

$query = "CREATE TABLE {$prefix}acp_hooks (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, linktext varchar(150), linkurl varchar(200), pluginname varchar(50), pluginstatus int DEFAULT 0)";
$adopts->query($query);

$query2 = "CREATE TABLE {$prefix}adoptables (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, type varchar(40), class varchar(40), description varchar(300), eggimage varchar(120), whenisavail varchar(50), alternates varchar(10), altoutlevel int DEFAULT 0, shop int DEFAULT 0, cost int DEFAULT 0)";
$adopts->query($query2);

$query3 = "CREATE TABLE {$prefix}adoptables_conditions (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, type varchar(40), whenisavail varchar(50), freqcond varchar(50), number int DEFAULT 0, datecond varchar(50), date varchar(20), adoptscond varchar(20), moreless varchar(20), morelessnum int DEFAULT 0, levelgrle varchar(25), grlelevel int DEFAULT 0)";
$adopts->query($query3);

$query4 = "CREATE TABLE {$prefix}ads (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, adname varchar(50), text varchar(1650), page varchar(50), impressions int DEFAULT 0, actualimpressions int DEFAULT 0, date varchar(50), status varchar(15), user varchar(45), extra varchar(100))";
$adopts->query($query4);

$query5 = "CREATE TABLE {$prefix}alternates (alid int NOT NULL AUTO_INCREMENT PRIMARY KEY, adopt int DEFAULT 0, image varchar(100), level int DEFAULT 0, item int DEFAULT 0, gender varchar(10), lastalt int DEFAULT 0, chance int DEFAULT 0)";
$adopts->query($query5);

$query6 = "CREATE TABLE {$prefix}breeding (bid int NOT NULL AUTO_INCREMENT PRIMARY KEY, offspring int DEFAULT 0, parent int DEFAULT 0, mother int DEFAULT 0, father int DEFAULT 0, probability int DEFAULT 0, survival int DEFAULT 0, level int DEFAULT 0, available varchar(10))";
$adopts->query($query6);


$query7 = "CREATE TABLE {$prefix}breeding_settings (bsid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(40))";
$adopts->query($query7);

$query8 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (1, 'system', 'enabled')";
$adopts->query($query8);

$query9 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (2, 'method', 'advanced')";
$adopts->query($query9);

$query10 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (3, 'species', '')";
$adopts->query($query10);

$query11 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (4, 'interval', 2)";
$adopts->query($query11);

$query12 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (5, 'level', 1)";
$adopts->query($query12);

$query13 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (6, 'capacity', 5)";
$adopts->query($query13);

$query14 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (7, 'number', 2)";
$adopts->query($query14);

$query15 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (8, 'chance', 80)";
$adopts->query($query15);

$query16 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (9, 'cost', 1000)";
$adopts->query($query16);

$query17 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (10, 'usergroup', 'all')";
$adopts->query($query17);

$query18 = "INSERT INTO {$prefix}breeding_settings (bsid, name, value) VALUES (11, 'item', '')";
$adopts->query($query18);


$today = new DateTime();
$query19 = "CREATE TABLE {$prefix}content (cid int NOT NULL AUTO_INCREMENT PRIMARY KEY, page varchar(20), title varchar(75), date varchar(15), content varchar(15000), level varchar(50), code varchar(128), item int DEFAULT 0, time varchar(20), `group` int DEFAULT 0)";
$adopts->query($query19);

$query20 = "INSERT INTO {$prefix}content (cid, page, title, date, content, level, code, item, time, `group`) VALUES (1, 'index', 'This is the index page', '{$today->format('Y-m-d')}', 'This is a sample article.  All of this text you can change in the script admin control panel.', '', '', 0, '', 0)";
$adopts->query($query20);

$query21 = "INSERT INTO {$prefix}content (cid, page, title, date, content, level, code, item, time, `group`) VALUES (2, 'tos', 'This is the Terms of Service Page', '{$today->format('Y-m-d')}', 'Put your terms of service here.  All of this text you can change in the script admin control panel.', '', '', 0, '', 0)";
$adopts->query($query21);


$query22 = "CREATE TABLE {$prefix}daycare_settings (dsid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(40))";
$adopts->query($query22);

$query23 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (1, 'system', 'enabled')";
$adopts->query($query23);

$query24 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (2, 'display', 'random')";
$adopts->query($query24);

$query25 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (3, 'number', 15)";
$adopts->query($query25);

$query26 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (4, 'columns', 5)";
$adopts->query($query26);

$query27 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (5, 'level', 1)";
$adopts->query($query27);

$query28 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (6, 'species', '')";
$adopts->query($query28);

$query29 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (7, 'info', 'Name,CurrentLevel')";
$adopts->query($query29);

$query30 = "INSERT INTO {$prefix}daycare_settings (dsid, name, value) VALUES (8, 'owned', 'yes')";
$adopts->query($query30);


$query31 = "CREATE TABLE {$prefix}filesmap (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, serverpath varchar(150), wwwpath varchar(200), friendlyname varchar(50))";
$adopts->query($query31);

$query32 = "CREATE TABLE {$prefix}folders_messages (mid int NOT NULL AUTO_INCREMENT PRIMARY KEY, fromuser int DEFAULT 0, touser int DEFAULT 0, folder varchar(20), datesent varchar(25), messagetitle varchar(100), messagetext varchar(2500))";
$adopts->query($query32);

$query33 = "CREATE TABLE {$prefix}friend_requests (fid int NOT NULL AUTO_INCREMENT PRIMARY KEY, fromuser int DEFAULT 0, offermessage varchar(1000), touser int DEFAULT 0, status varchar(30))";
$adopts->query($query33);

$query34 = "CREATE TABLE {$prefix}groups (gid int NOT NULL AUTO_INCREMENT PRIMARY KEY, groupname varchar(20) UNIQUE, canadopt varchar(10), canpm varchar(10), cancp varchar(10), canmanageadopts varchar(10), canmanagecontent varchar(10), canmanageads varchar(10), canmanagesettings varchar(10), canmanageusers varchar(10))";
$adopts->query($query34);


$query35 = "INSERT INTO {$prefix}groups VALUES (1, 'rootadmins', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes')";
$adopts->query($query35);

$query36 = "INSERT INTO {$prefix}groups VALUES (2, 'admins', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes', 'yes')";
$adopts->query($query36);

$query37 = "INSERT INTO {$prefix}groups VALUES (3, 'registered', 'yes', 'yes', 'no', 'no', 'no', 'no', 'no', 'no')";
$adopts->query($query37);

$query38 = "INSERT INTO {$prefix}groups VALUES (4, 'artists', 'yes', 'yes', 'yes', 'yes', 'yes', 'no', 'no', 'no')";
$adopts->query($query38);

$query39 = "INSERT INTO {$prefix}groups VALUES (5, 'banned', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no')";
$adopts->query($query39);

$query40 = "INSERT INTO {$prefix}groups VALUES (6, 'visitors', 'no', 'no', 'no', 'no', 'no', 'no', 'no', 'no')";
$adopts->query($query40);


$query41 = "CREATE TABLE {$prefix}inventory (iid int NOT NULL AUTO_INCREMENT PRIMARY KEY, item int DEFAULT 0, owner int DEFAULT 0, quantity int DEFAULT 0, status varchar(40))";
$adopts->query($query41);

$query42 = "CREATE TABLE {$prefix}items (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, category varchar(40), itemname varchar(40), description varchar(200), imageurl varchar(150), `function` varchar(40), target varchar(200), value int DEFAULT 0, shop int DEFAULT 0, price int DEFAULT 0, chance int DEFAULT 0, cap int DEFAULT 0, tradable varchar(30), consumable varchar(30))";
$adopts->query($query42);

$query43 = "CREATE TABLE {$prefix}items_functions (ifid int NOT NULL AUTO_INCREMENT PRIMARY KEY, `function` varchar(40), intent varchar(20), description varchar(200))";
$adopts->query($query43);

$query44 = "INSERT INTO {$prefix}items_functions VALUES (1, 'Key', 'no', 'This item function defines items classified as key items, they cannot be sold or tossed, and exist for various purposes.')";
$adopts->query($query44);

$query45 = "INSERT INTO {$prefix}items_functions VALUES (2, 'Valuable', 'no', 'This item function defines items that do not serve any purposes besides selling for money.')";
$adopts->query($query45);

$query46 = "INSERT INTO {$prefix}items_functions VALUES (3, 'Level1', 'Adoptable', 'This item function defines items that raise your adoptables levels by certain point.')";
$adopts->query($query46);

$query47 = "INSERT INTO {$prefix}items_functions VALUES (4, 'Level2', 'Adoptable', 'This item function defines items that set your adoptables levels to certain values.')";
$adopts->query($query47);

$query48 = "INSERT INTO {$prefix}items_functions VALUES (5, 'Level3', 'Adoptable', 'This item function defines items that reset your adoptables to baby state.')";
$adopts->query($query48);

$query49 = "INSERT INTO {$prefix}items_functions VALUES (6, 'Click1', 'Adoptable', 'This item function defines items that raise your adoptables totalclicks by certain points.')";
$adopts->query($query49);

$query50 = "INSERT INTO {$prefix}items_functions VALUES (7, 'Click2', 'Adoptable', 'This item function defines items that set your adoptables totalclicks to certain values.')";
$adopts->query($query50);

$query51 = "INSERT INTO {$prefix}items_functions VALUES (8, 'Click3', 'Adoptable', 'This item function defines items that reset the clicks of a day.')";
$adopts->query($query51);

$query52 = "INSERT INTO {$prefix}items_functions VALUES (9, 'Breed1', 'Adoptable', 'This item function defines items that enables your adoptables to breed again instantly.')";
$adopts->query($query52);

$query53 = "INSERT INTO {$prefix}items_functions VALUES (10, 'Breed2', 'Adoptable', 'This item function defines items that enable adoptables to overcome class barriers for interspecies breeding.')";
$adopts->query($query53);

$query54 = "INSERT INTO {$prefix}items_functions VALUES (11, 'Alts1', 'Adoptable', 'This item function defines items that change your adoptables alternate form from one to the other.')";
$adopts->query($query54);

$query55 = "INSERT INTO {$prefix}items_functions VALUES (12, 'Alts2', 'Adoptable', 'This item function defines items that change your adoptable to a random alternate form.')";
$adopts->query($query55);

$query56 = "INSERT INTO {$prefix}items_functions VALUES (13, 'Name1', 'Adoptable', 'This item function defines items that allow members to rename their adoptables.')";
$adopts->query($query56);

$query57 = "INSERT INTO {$prefix}items_functions VALUES (14, 'Name2', 'User', 'This item function defines items that allow members to change their usernames.')";
$adopts->query($query57);

$query58 = "CREATE TABLE {$prefix}levels (lvid int NOT NULL AUTO_INCREMENT PRIMARY KEY, adopt int DEFAULT 0, level int DEFAULT 0, requiredclicks int DEFAULT 0, primaryimage varchar(120), rewarduser varchar(10), promocode varchar(25))";
$adopts->query($query58);


$query59 = "CREATE TABLE {$prefix}levels_settings (lsid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(200))";
$adopts->query($query59);

$query60 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (1, 'system', 'enabled')";
$adopts->query($query60);

$query61 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (2, 'method', 'multiple')";
$adopts->query($query61);

$query62 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (3, 'clicks', '5,2')";
$adopts->query($query62);

$query63 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (4, 'maximum', 3)";
$adopts->query($query63);

$query64 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (5, 'number', 10)";
$adopts->query($query64);

$query65 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (6, 'reward', '10,20')";
$adopts->query($query65);

$query66 = "INSERT INTO {$prefix}levels_settings (lsid, name, value) VALUES (7, 'owner', 'enabled')";
$adopts->query($query66);


$query67 = "CREATE TABLE {$prefix}links (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, linktype varchar(15), linktext varchar(150), linkurl varchar(200), linkparent int DEFAULT 0, linkorder int DEFAULT 0)";
$adopts->query($query67);

$query68 = "INSERT INTO {$prefix}links VALUES (1, 'navlink', 'Home', 'index', 0, 0)";
$adopts->query($query68);

$query69 = "INSERT INTO {$prefix}links VALUES (2, 'navlink', 'Adoptables', 'index', 0, 10)";
$adopts->query($query69);

$query70 = "INSERT INTO {$prefix}links VALUES (3, 'navlink', 'User CP', 'index', 0, 20)";
$adopts->query($query70);

$query71 = "INSERT INTO {$prefix}links VALUES (4, 'navlink', 'Explore', 'index', 0, 30)";
$adopts->query($query71);

$query72 = "INSERT INTO {$prefix}links VALUES (5, 'navlink', 'Community', 'index', 0, 40)";
$adopts->query($query72);

$query73 = "INSERT INTO {$prefix}links VALUES (6, 'navlink', 'Adoption Center', 'adopt', 2, 0)";
$adopts->query($query73);

$query74 = "INSERT INTO {$prefix}links VALUES (7, 'navlink', 'Pound Pool', 'pound', 2, 10)";
$adopts->query($query74);

$query75 = "INSERT INTO {$prefix}links VALUES (8, 'navlink', 'My Adopts', 'myadopts', 2, 20)";
$adopts->query($query75);

$query76 = "INSERT INTO {$prefix}links VALUES (9, 'navlink', 'Special Offer', 'promo', 2, 30)";
$adopts->query($query76);

$query77 = "INSERT INTO {$prefix}links VALUES (10, 'navlink', 'Manage Account', 'account', 3, 0)";
$adopts->query($query77);

$query78 = "INSERT INTO {$prefix}links VALUES (11, 'navlink', 'Manage Trade', 'mytrades', 3, 10)";
$adopts->query($query78);

$query79 = "INSERT INTO {$prefix}links VALUES (12, 'navlink', 'Manage Items', 'inventory', 3, 20)";
$adopts->query($query79);

$query80 = "INSERT INTO {$prefix}links VALUES (13, 'navlink', 'Manage PMs', 'messages', 3, 30)";
$adopts->query($query80);

$query81 = "INSERT INTO {$prefix}links VALUES (14, 'navlink', 'Trade', 'trade', 4, 0)";
$adopts->query($query81);

$query82 = "INSERT INTO {$prefix}links VALUES (15, 'navlink', 'Breeding', 'breeding', 4, 10)";
$adopts->query($query82);

$query83 = "INSERT INTO {$prefix}links VALUES (16, 'navlink', 'Daycare', 'levelup/daycare', 4, 20)";
$adopts->query($query83);

$query84 = "INSERT INTO {$prefix}links VALUES (17, 'navlink', 'Market', 'shop', 4, 30)";
$adopts->query($query84);

$query85 = "INSERT INTO {$prefix}links VALUES (18, 'navlink', 'Search', 'search', 4, 40)";
$adopts->query($query85);

$query86 = "INSERT INTO {$prefix}links VALUES (19, 'navlink', 'Shoutbox', 'shoutbox', 5, 0)";
$adopts->query($query86);

$query87 = "INSERT INTO {$prefix}links VALUES (20, 'navlink', 'Forum', 'forum', 5, 10)";
$adopts->query($query87);

$query88 = "INSERT INTO {$prefix}links VALUES (21, 'navlink', 'Members List', 'profile', 5, 20)";
$adopts->query($query88);

$query89 = "INSERT INTO {$prefix}links VALUES (22, 'navlink', 'Stats', 'stats', 5, 30)";
$adopts->query($query89);

$query90 = "INSERT INTO {$prefix}links VALUES (23, 'navlink', 'TOS', 'tos', 5, 40)";
$adopts->query($query90);

$query91 = "INSERT INTO {$prefix}links VALUES (24, 'sidelink', 'Adopt New Pets', 'adopt', 0, 0)";
$adopts->query($query91);

$query92 = "INSERT INTO {$prefix}links VALUES (25, 'sidelink', 'Acquire Pounded Pets', 'pound', 0, 10)";
$adopts->query($query92);

$query93 = "INSERT INTO {$prefix}links VALUES (26, 'sidelink', 'Manage Adoptables', 'myadopts', 0, 20)";
$adopts->query($query93);

$query94 = "INSERT INTO {$prefix}links VALUES (27, 'sidelink', 'Go to My Account', 'account', 0, 30)";
$adopts->query($query94);

$query95 = "INSERT INTO {$prefix}links VALUES (28, 'sidelink', 'Messages', 'messages', 0, 40)";
$adopts->query($query95);

$query96 = "INSERT INTO {$prefix}links VALUES (29, 'sidelink', 'Change Themes', 'changestyle', 0, 50)";
$adopts->query($query96);

$query97 = "INSERT INTO {$prefix}links VALUES (30, 'sidelink', 'Logout', 'login/logout', 0, 60)";
$adopts->query($query97);


$query98 = "CREATE TABLE {$prefix}messages (mid int NOT NULL AUTO_INCREMENT PRIMARY KEY, fromuser int DEFAULT 0, touser int DEFAULT 0, status varchar(20), datesent varchar(25), messagetitle varchar(100), messagetext varchar(2500))";
$adopts->query($query98);

$query99 = "CREATE TABLE {$prefix}modules (moid int NOT NULL AUTO_INCREMENT PRIMARY KEY, widget int DEFAULT 0, name varchar(20), subtitle varchar(40), userlevel varchar(20), html text, php text, `order` int DEFAULT 0, status varchar(10))";
$adopts->query($query99);

$query100 = "INSERT INTO {$prefix}modules VALUES (1, 4, 'MoneyBar', '', 'member', '', '', 0, 'enabled')";
$adopts->query($query100);

$query101 = "INSERT INTO {$prefix}modules VALUES (2, 4, 'LoginBar', '', 'visitor', '', '', 0, 'enabled')";
$adopts->query($query101);

$query102 = "INSERT INTO {$prefix}modules VALUES (3, 4, 'LinksBar', '', 'member', '', '', 10, 'enabled')";
$adopts->query($query102);

$query103 = "INSERT INTO {$prefix}modules VALUES (4, 4, 'WolBar', '', 'user', '', '', 20, 'enabled')";
$adopts->query($query103);

$query104 = "INSERT INTO {$prefix}modules VALUES (5, 5, 'Ads', '', 'user', '', '', 0, 'enabled')";
$adopts->query($query104);

$query105 = "INSERT INTO {$prefix}modules VALUES (6, 5, 'Credits', '', 'user', '', '', 10, 'enabled')";
$adopts->query($query105);


$query106 = "CREATE TABLE {$prefix}online (username varchar(40), ip varchar(60), session char(100), time int DEFAULT 0)";
$adopts->query($query106);

$query107 = "CREATE TABLE {$prefix}owned_adoptables (aid int NOT NULL AUTO_INCREMENT PRIMARY KEY, adopt int DEFAULT 0, name varchar(40), owner int DEFAULT 0, currentlevel int DEFAULT 0, totalclicks int DEFAULT 0, code varchar(15), imageurl varchar(120), alternate varchar(10), tradestatus varchar(15), isfrozen varchar(10), gender varchar(10), offsprings int DEFAULT 0, lastbred int DEFAULT 0)";
$adopts->query($query107);

$query108 = "CREATE TABLE {$prefix}passwordresets (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, username varchar(30), email varchar(50), code varchar(70), ip varchar(30), date varchar(20))";
$adopts->query($query108);


$query109 = "CREATE TABLE {$prefix}pounds (aid int DEFAULT 0 UNIQUE, firstowner int DEFAULT 0, lastowner int DEFAULT 0, currentowner int DEFAULT 0, recurrence int DEFAULT 0, datepound varchar(20), dateadopt varchar(20))";
$adopts->query($query109);

$query110 = "CREATE TABLE {$prefix}pounds_settings (psid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(40))";
$adopts->query($query110);

$query111 = "INSERT INTO {$prefix}pounds_settings VALUES (1, 'system', 'yes')";
$adopts->query($query111);

$query112 = "INSERT INTO {$prefix}pounds_settings VALUES (2, 'adopt', 'yes')";
$adopts->query($query112);

$query113 = "INSERT INTO {$prefix}pounds_settings VALUES (3, 'specieslimit', '')";
$adopts->query($query113);

$query114 = "INSERT INTO {$prefix}pounds_settings VALUES (4, 'cost', '50, 100')";
$adopts->query($query114);

$query115 = "INSERT INTO {$prefix}pounds_settings VALUES (5, 'costtype', 'percent')";
$adopts->query($query115);

$query116 = "INSERT INTO {$prefix}pounds_settings VALUES (6, 'levelbonus', '1')";
$adopts->query($query116);

$query117 = "INSERT INTO {$prefix}pounds_settings VALUES (7, 'leveltype', 'multiply')";
$adopts->query($query117);

$query118 = "INSERT INTO {$prefix}pounds_settings VALUES (8, 'number', '4, 5')";
$adopts->query($query118);

$query119 = "INSERT INTO {$prefix}pounds_settings VALUES (9, 'date', 'yes')";
$adopts->query($query119);

$query120 = "INSERT INTO {$prefix}pounds_settings VALUES (10, 'duration', 3)";
$adopts->query($query120);

$query121 = "INSERT INTO {$prefix}pounds_settings VALUES (11, 'owner', 'yes')";
$adopts->query($query121);

$query122 = "INSERT INTO {$prefix}pounds_settings VALUES (12, 'recurrence', 5)";
$adopts->query($query122);

$query123 = "INSERT INTO {$prefix}pounds_settings VALUES (13, 'rename', 'yes')";
$adopts->query($query123);


$query124 = "CREATE TABLE {$prefix}promocodes (pid int NOT NULL AUTO_INCREMENT PRIMARY KEY, type varchar(20), user int DEFAULT 0, code varchar(200), availability int DEFAULT 0, fromdate varchar(20), todate varchar(20), reward int DEFAULT 0)";
$adopts->query($query124);

$query125 = "CREATE TABLE {$prefix}settings (name varchar(20), value varchar(350))";
$adopts->query($query125);

$query126 = "CREATE TABLE {$prefix}shops (sid int NOT NULL AUTO_INCREMENT PRIMARY KEY, category varchar(40), shopname varchar(40), shoptype varchar(20), description varchar(200), imageurl varchar(150), status varchar(40), restriction varchar(80), salestax int DEFAULT 0)";
$adopts->query($query126);

$query127 = "CREATE TABLE {$prefix}shoutbox (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, user int DEFAULT 0, date varchar(30), comment varchar(2500))";
$adopts->query($query127);

$query128 = "CREATE TABLE {$prefix}systems_settings (name varchar(20), value varchar(350))";
$adopts->query($query128);

$query129 = "INSERT INTO {$prefix}systems_settings VALUES ('site', 'enabled')";
$adopts->query($query129);

$query130 = "INSERT INTO {$prefix}systems_settings VALUES ('adopts', 'enabled')";
$adopts->query($query130);

$query131 = "INSERT INTO {$prefix}systems_settings VALUES ('friends', 'enabled')";
$adopts->query($query131);

$query132 = "INSERT INTO {$prefix}systems_settings VALUES ('items', 'enabled')";
$adopts->query($query132);

$query133 = "INSERT INTO {$prefix}systems_settings VALUES ('messages', 'enabled')";
$adopts->query($query133);

$query134 = "INSERT INTO {$prefix}systems_settings VALUES ('online', 'enabled')";
$adopts->query($query134);

$query135 = "INSERT INTO {$prefix}systems_settings VALUES ('promo', 'enabled')";
$adopts->query($query135);

$query136 = "INSERT INTO {$prefix}systems_settings VALUES ('register', 'enabled')";
$adopts->query($query136);

$query137 = "INSERT INTO {$prefix}systems_settings VALUES ('shops', 'enabled')";
$adopts->query($query137);

$query138 = "INSERT INTO {$prefix}systems_settings VALUES ('shoutbox', 'enabled')";
$adopts->query($query138);

$query139 = "INSERT INTO {$prefix}systems_settings VALUES ('vmessages', 'enabled')";
$adopts->query($query139);

$query140 = "CREATE TABLE {$prefix}themes (id int NOT NULL AUTO_INCREMENT PRIMARY KEY, themename varchar(150), themefolder varchar(200), usergroup int DEFAULT NULL, fromdate varchar(15), todate varchar(15))";
$adopts->query($query140);

$query141 = "INSERT INTO {$prefix}themes VALUES (1, 'Main', 'main', 0, '', '')";
$adopts->query($query141);

$query142 = "INSERT INTO {$prefix}themes VALUES (2, 'Elements', 'elements', 0, '', '')";
$adopts->query($query142);

$query143 = "INSERT INTO {$prefix}themes VALUES (3, 'Green', 'green', 0, '', '')";
$adopts->query($query143);

$query144 = "CREATE TABLE {$prefix}trade (tid int NOT NULL AUTO_INCREMENT PRIMARY KEY, type varchar(15), sender int DEFAULT 0, recipient int DEFAULT 0, adoptoffered varchar(40), adoptwanted varchar(40), itemoffered varchar(40), itemwanted varchar(40), cashoffered int DEFAULT 0, message varchar(100), status varchar(20), date varchar(20))";
$adopts->query($query144);

$query145 = "CREATE TABLE {$prefix}trade_associations (taid int NOT NULL AUTO_INCREMENT PRIMARY KEY, publicid int DEFAULT 0, privateid int DEFAULT 0)";
$adopts->query($query145);


$query146 = "CREATE TABLE {$prefix}trade_settings (tsid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(20), value varchar(40))";
$adopts->query($query146);

$query147 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (1, 'system', 'enabled')";
$adopts->query($query147);

$query148 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (2, 'multiple', 'enabled')";
$adopts->query($query148);

$query149 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (3, 'partial', 'enabled')";
$adopts->query($query149);

$query150 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (4, 'public', 'enabled')";
$adopts->query($query150);

$query151 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (5, 'species', '')";
$adopts->query($query151);

$query152 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (6, 'interval', 1)";
$adopts->query($query152);

$query153 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (7, 'number', 3)";
$adopts->query($query153);

$query154 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (8, 'duration', 5)";
$adopts->query($query154);

$query155 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (9, 'tax', 300)";
$adopts->query($query155);

$query156 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (10, 'usergroup', 'all')";
$adopts->query($query156);

$query157 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (11, 'item', '')";
$adopts->query($query157);

$query158 = "INSERT INTO {$prefix}trade_settings (tsid, name, value) VALUES (12, 'moderate', 'disabled')";
$adopts->query($query158);


$query159 = "CREATE TABLE {$prefix}users (uid int NOT NULL AUTO_INCREMENT PRIMARY KEY, username varchar(30) UNIQUE, salt varchar(20), password varchar(200), session varchar(100), email varchar(60), ip varchar(60), usergroup int DEFAULT 0, birthday varchar(40), membersince varchar(20), money int DEFAULT 0, friends varchar(500))";
$adopts->query($query159);

$query160 = "CREATE TABLE {$prefix}users_contacts (uid int NOT NULL AUTO_INCREMENT PRIMARY KEY, website varchar(80), facebook varchar(80), twitter varchar(80),aim varchar(80), yahoo varchar(80), msn varchar(80), skype varchar(80))";
$adopts->query($query160);

$query161 = "CREATE TABLE {$prefix}users_options (uid int NOT NULL AUTO_INCREMENT PRIMARY KEY, newmessagenotify varchar(10), pmstatus int DEFAULT 0, vmstatus int DEFAULT 0, tradestatus int DEFAULT 0, theme varchar(20))";
$adopts->query($query161);

$query162 = "CREATE TABLE {$prefix}users_permissions (uid int NOT NULL AUTO_INCREMENT PRIMARY KEY, canlevel varchar(10), canvm varchar(10), canfriend varchar(10), cantrade varchar(10), canbreed varchar(10), canpound varchar(10), canshop varchar(10))";
$adopts->query($query162);

$query163 = "CREATE TABLE {$prefix}users_profile (uid int NOT NULL AUTO_INCREMENT PRIMARY KEY, avatar varchar(120), bio varchar(500), color varchar(20), about varchar(200), favpet int DEFAULT 0, gender varchar(10), nickname varchar(40))";
$adopts->query($query163);

$query164 = "CREATE TABLE {$prefix}visitor_messages (vid int NOT NULL AUTO_INCREMENT PRIMARY KEY, fromuser int DEFAULT 0, touser int DEFAULT 0, datesent varchar(25), vmtext varchar(500))";
$adopts->query($query164);

$query165 = "CREATE TABLE {$prefix}vote_voters (void int NOT NULL AUTO_INCREMENT PRIMARY KEY, adoptableid int DEFAULT 0, userid int DEFAULT 0, ip varchar(50), date varchar(30))";
$adopts->query($query165);


$query166 = "CREATE TABLE {$prefix}widgets (wid int NOT NULL AUTO_INCREMENT PRIMARY KEY, name varchar(40), controller varchar(20), `order` int DEFAULT 0, status varchar(20))";
$adopts->query($query166);

$query167 = "INSERT INTO {$prefix}widgets VALUES (1, 'header', 'all', 0, 'enabled')";
$adopts->query($query167);

$query168 = "INSERT INTO {$prefix}widgets VALUES (2, 'menu', 'main', 10, 'enabled')";
$adopts->query($query168);

$query169 = "INSERT INTO {$prefix}widgets VALUES (3, 'document', 'all', 20, 'enabled')";
$adopts->query($query169);

$query170 = "INSERT INTO {$prefix}widgets VALUES (4, 'sidebar', 'all', 30, 'enabled')";
$adopts->query($query170);

$query171 = "INSERT INTO {$prefix}widgets VALUES (5, 'footer', 'all', 40, 'enabled')";
$adopts->query($query171);


//Now we output a form so they can create an admin user...

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
                <td width='750' height='57' valign='top' bgcolor='#a0a0f9'>
                    <div align='left'>
                        <p>
                            <span class='style1'>
                                Mysidia Adoptables v1.3.6 Installation Wizard <br>
                                <span class='style2'>Step 4: Create Admin User </span>                                    
                            </span>
                        </p>
                    </div>
                </td>
            </tr>
            <tr>
                <td height='643' valign='top' bgcolor='#FFFFFF'>
                    <p align='left'>
                        <br>
                        <span class='style2'>
                            This page allows you to set up an admin user account for your installation of Mysidia Adoptables. This account will allow you to administer your site in the built-in Admin CP.
                        </span>
                    </p>
                    <form name='form1' method='post' action='createadmin.php'>
                        <p class='style2'>
                            Admin Username: 
                            <input name='username' type='text' id='username' maxlength='20'>
                        </p>
                        <p class='style2'>
                            The username may contain letters, numbers and spaces ONLY and can be up to 20 characters long. 
                        </p>
                        <p class='style2'>
                            Admin Password: 
                            <input name='pass1' type='password' id='pass1'>
                        </p>
                        <p>
                            <span class='style2'>
                                The password may contain letters, numbers and special characters and can be up to 20 characters long.
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                Confirm Password: 
                                <input name='pass2' type='password' id='pass2'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                Admin Birthday:(mm/dd/yyyy)
                                <input name='birthday' type='date' id='birthday'>
                            </span>
                        </p>
                        <p>
                            <span class='style2'>
                                Admin Email Address: 
                                <input name='email' type='text' id='email'>
                            </span>
                        </p>
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