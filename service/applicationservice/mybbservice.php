<?php

namespace Service\ApplicationService;
use Resource\Core\Database;
use Resource\Native\MysObject;

class MyBBService extends MysObject{ 
    
    private $enabled = FALSE;
    private $forumDB;
    private $forumPrefix;
    private $seeded = FALSE;
    private $obfuscator = 0;
    
    public function __construct(){
        include_once "config_forums.php";
        if(defined("MYBB_ENABLED") && MYBB_ENABLED == 1){ 
            $this->enabled = TRUE;
            $this->forumDB = new Database(MYBB_NAME, MYBB_HOST, MYBB_USER, MYBB_PASS, MYBB_PREFIX);
            $this->forumPrefix = MYBB_PREFIX;
        }
    }
    
    public function isEnabled(){ 
        return $this->enabled;
    }
    
    public function register($username, $password, $email, $avatar, $birthday = NULL){ 
        $salty = $this->randomCode(8);
        $loginkey = $this->randomCode(50);
        $md5pass = md5($password);
        $fpass = md5(md5($salty).$md5pass); 
        $ip = $_SERVER['REMOTE_ADDR'];
        if($_SERVER['HTTPS'] && strpos($avatar, "https://") === false) $avatar = "https://" . DOMAIN.SCRIPTPATH . "/" . $avatar;
        elseif(!$_SERVER['HTTPS'] && strpos($avatar, "http://") === false) $avatar = "http://" . DOMAIN.SCRIPTPATH . "/" . $avatar;
        $query = "INSERT INTO {$this->forumPrefix}users (uid, username, password, salt, loginkey, email, postnum, threadnum, avatar, avatardimensions, avatartype, usergroup, additionalgroups, displaygroup, usertitle, regdate, lastactive, lastvisit, lastpost, website, icq, skype, google, birthday, birthdayprivacy, signature, allownotices, hideemail, subscriptionmethod, invisible, receivepms, receivefrombuddy, pmnotice, pmnotify, buddyrequestspm, buddyrequestsauto, threadmode, showimages, showvideos, showsigs, showavatars, showquickreply, showredirect, ppp, tpp, daysprune, dateformat, timeformat, timezone, dst, dstcorrection, buddylist, ignorelist, style, away, awaydate, returndate, awayreason, pmfolders, notepad, referrer, referrals, reputation, regip, lastip, language, timeonline, showcodebuttons, totalpms, unreadpms, warningpoints, moderateposts, moderationtime, suspendposting, suspensiontime, suspendsignature, suspendsigtime, coppauser, classicpostbit, loginattempts, usernotes, sourceeditor) VALUES ('', '$username', '$fpass','$salty','$loginkey', '$email', '1', '1', '$avatar', '', '0', '2', '', '0', '', 'time()', 'time()', 'time()', 'time()', '0', '', '', '', '$birthday', 'all', '', '1', '0', '0', '0', '1', '0', '1', '1', '1', '0', '', '1', '1', '1', '1', '1', '1', '0', '0', '0', '', '', '0', '0', '0', '', '', '0', '0', '0', '', '', '1**Inbox$%%$2**Sent Items$%%$3**Drafts$%%$4**Trash Can', '', '0', '0', '0', '$ip', '$ip', '', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '0', '1', '', '0')";
        $this->forumDB->query($query) or die("Failed to create forum account");

        // Now set the cookie for user on MyBB        
        $mybbuser = $this->forumDB->select("users", ["uid", "loginkey"], "username = :username", ["username" => $username])->fetchObject();
        $cookiesettings = [];
        $cookiesettings['cookiedomain'] = $this->forumDB->select("settings", ["value"], "name = 'cookiedomain'")->fetchColumn();
        $cookiesettings['cookiepath'] = $this->forumDB->select("settings", ["value"], "name = 'cookiepath'")->fetchColumn();
        $cookiesettings['cookieprefix'] = $this->forumDB->select("settings", ["value"], "name = 'cookieprefix'")->fetchColumn();
        $this->setCookie("mybbuser", $mybbuser->uid. "_" . $mybbuser->loginkey, NULL, TRUE, $cookiesettings);
        $this->setCookie("sid", $this->randomString(32), -1, TRUE);         
    }
    
    public function login($username){
        $mybbuser = $this->forumDB->select("users", ["uid", "loginkey"], "username = :username", ["username" => $username])->fetchObject();
        $cookiesettings = [];
        $cookiesettings['cookiedomain'] = $this->forumDB->select("settings", ["value"], "name = 'cookiedomain'")->fetchColumn();
        $cookiesettings['cookiepath'] = $this->forumDB->select("settings", ["value"], "name = 'cookiepath'")->fetchColumn();
        $cookiesettings['cookieprefix'] = $this->forumDB->select("settings", ["value"], "name = 'cookieprefix'")->fetchColumn();
        $this->setCookie("mybbuser", $mybbuser->uid . "_" . $mybbuser->loginkey, NULL, TRUE, $cookiesettings);
        $this->setCookie("sid", $this->randomString(32), -1, TRUE);         
    }
    
    public function logout($uid){ 
        $this->unsetCookie("mybbuser");
        $this->unsetCookie("sid");
        $loginkey = $this->randomCode(50);
        $lastvisit = time() - 900;
        $lastactive = time();
        $this->forumDB->update("users", ["loginkey" => $loginkey, "lastvisit" => $lastvisit, "lastactive" => $lastactive], "uid = '{$uid}'");
        $this->forumDB->delete("sessions", "uid = '{$uid}'");        
    }
    
    public function rebuildStats($username = NULL){ 
        if(!$username) return FALSE;
        $oldstats = $this->forumDB->select("datacache", ["cache"], "title = 'stats'")->fetchColumn();
        $stats = unserialize($oldstats); 
        $uid = $this->forumDB->select("users", ["uid"], "username = :username", ["username" => $username])->fetchColumn();
    
        if($stats['lastuid'] == $uid) return FALSE;
        $stats['numusers']++;
        $stats['lastuid'] = $uid;
        $stats['lastusername'] = $username;
        $newstats = serialize($stats);
    
        $this->forumDB->update("datacache", ["cache" => $newstats], "title = 'stats'");
        $this->forumDB->delete("stats");
        $this->forumDB->insert("stats", ["dateline" => time(), "numusers" => $stats['numusers'], "numthreads" => $stats['numthreads'], "numposts" => $stats['numposts']]);
        return TRUE;        
    }
    
    private function setCookie($name, $value = "", $expires = "", $httponly = FALSE, $cookiesettings = []){ 
        if(!isset($cookiesettings['cookiepath'])) $cookiesettings['cookiepath'] = "/";

        if($expires == -1) $expires = 0;
        elseif($expires == "" || $expires == null) $expires = time() + (60*60*24*365); // Make the cookie expire in a years time
        else $expires = time() + intval($expires);

        $cookiesettings['cookiepath'] = str_replace(["\n","\r"], "", $cookiesettings['cookiepath']);
        $cookiesettings['cookiedomain'] = str_replace(["\n","\r"], "", isset($cookiesettings['cookiedomain']) ? $cookiesettings['cookiedomain'] : "");
        $cookiesettings['cookieprefix'] = str_replace(["\n","\r", " "], "", isset($cookiesettings['cookieprefix']) ? $cookiesettings['cookieprefix'] : "");

        // Versions of PHP prior to 5.2 do not support HttpOnly cookies and IE is buggy when specifying a blank domain so set the cookie manually
        $cookie = "Set-Cookie: {$cookiesettings['cookieprefix']}{$name}=".urlencode($value);

        if($expires > 0) $cookie .= "; expires=" . @gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expires);
        if(!empty($cookiesettings['cookiepath'])) $cookie .= "; path={$cookiesettings['cookiepath']}";
        if(!empty($cookiesettings['cookiedomain'])) $cookie .= "; domain={$cookiesettings['cookiedomain']}";
        if($httponly == true) $cookie .= "; HttpOnly";
    
        $cookiesettings[$name] = $value;
        header($cookie, false);  
    }
    
    private function unsetCookie($name){
        $this->setCookie($name, "", -3600);           
    }
    
    private function seedRNG($count = 8){
        $output = "";
    
        // Try the unix/linux method
        if(@is_readable('/dev/urandom') && ($handle = @fopen('/dev/urandom', 'rb'))){
            $output = @fread($handle, $count);
            @fclose($handle);
        }
    
        // Didn't work? Do we still not have enough bytes? Use our own (less secure) rng generator
        if(strlen($output) < $count){
            $output = "";
        
            // Close to what PHP basically uses internally to seed, but not quite.
            $unique_state = microtime() . @getmypid();
        
            for($i = 0; $i < $count; $i += 16){
                $unique_state = md5(microtime().$unique_state);
                $output .= pack('H*', md5($unique_state));
            }
        }
    
        // /dev/urandom and openssl will always be twice as long as $count. base64_encode will roughly take up 33% more space but crc32 will put it to 32 characters
        return hexdec(substr(dechex(crc32(base64_encode($output))), 0, $count));      
    }
    
    private function randomNumber($min = NULL, $max = NULL, $forceSeed = FALSE){ 
        if($this->seeded == FALSE || $forceSeed == TRUE){
            mt_srand($this->seedRNG());
            $this->seeded = TRUE;
            $this->obfuscator = abs((int)($this->seedRNG()));       
            // Ensure that $obfuscator is <= mt_getrandmax() for 64 bit systems.
            if($this->obfuscator > mt_getrandmax()) $this->obfuscator -= mt_getrandmax();
        }

        if($min !== NULL && $max !== NULL){
            $distance = $max - $min;
            if ($distance > 0) return $min + (int)((float)($distance + 1) * (float)(mt_rand() ^ $this->obfuscator) / (mt_getrandmax() + 1));
            else return mt_rand($min, $max);
        }
        else{
            $val = mt_rand() ^ $this->obfuscator;
            return $val;
        }        
    }
    
    private function randomString($length = 8){ 
        $set = ["a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9"];
        $str = "";
        for($i = 1; $i <= $length; ++$i){
            $ch = $this->randomNumber(0, count($set)-1);
            $str .= $set[$ch];
        }
        return $str;        
    }
    
    private function randomCode($length){
	    $set = ["a","A","b","B","c","C","d","D","e","E","f","F","g","G","h","H","i","I","j","J","k","K","l","L","m","M","n","N","o","O","p","P","q","Q","r","R","s","S","t","T","u","U","v","V","w","W","x","X","y","Y","z","Z","1","2","3","4","5","6","7","8","9"];
	    $str = "";
	    for($i = 1; $i <= $length; ++$i){
		    $ch = mt_rand(0, count($set)-1);
		    $str .= $set[$ch];
	    }
	    return $str;
    }
}