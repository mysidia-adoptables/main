<?php

namespace Controller\Main;

use Model\DomainModel\Member;
use Model\DomainModel\OwnedAdoptable;
use Model\Settings\LevelSettings;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\LevelupException;
use Resource\Exception\NoPermissionException;
use Resource\Native\Integer;
use Resource\Utility\Curl;
use Resource\Utility\Date;
use Service\ApplicationService\DaycareService;

class LevelupController extends AppController
{

    private $adopt;
    private $settings;

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        $this->settings = new LevelSettings($mysidia->db);
        if ($mysidia->user instanceof Member && !$mysidia->user->hasPermission("canlevel")) {
            throw new InvalidActionException("banned");
        }
    }

    public function index(): never
    {
        throw new InvalidActionException("global_action");
    }

    public function click($aid)
    {
        $mysidia = Registry::get("mysidia");
        $date = new Date;
        $ip = $mysidia->secure($_SERVER['REMOTE_ADDR']);
        $this->adopt = new OwnedAdoptable($aid);

        if ($this->settings->system != "enabled") throw new NoPermissionException("disabled");
        elseif ($this->adopt->hasVoter($mysidia->user, $date)) {
            $message = ($mysidia->user instanceof Member) ? "already_leveled_member" : "already_leveled_guest";
            throw new LevelupException($message);
        } elseif ($this->adopt->isFrozen() == "yes") throw new LevelupException("frozen");
        elseif ($mysidia->user->getVotes() > $this->settings->number) throw new LevelupException("number");
        elseif ($this->settings->owner == "disabled" and $this->adopt->isOwner($mysidia->user)) {
            throw new LevelupException("owner");
        } else {
            $newClicks = $this->adopt->getTotalClicks() + 1;
            $this->adopt->setTotalClicks($newClicks, "update");
            $mysidia->db->insert("vote_voters", ["void" => null, "date" => $date->format('Y-m-d'), "userid" => $mysidia->user->getID(), "ip" => $ip, "adoptableid" => $aid]);
            if ($this->adopt->hasNextLevel()) {
                $nextLevel = $this->adopt->getNextLevel();
                $requiredClicks = $nextLevel->getRequiredClicks();
                if ($requiredClicks && $newClicks >= $requiredClicks) $this->adopt->setCurrentLevel($nextLevel->getLevel(), "update");
            }

            $this->setField("adopt", $this->adopt);
            if ($mysidia->user instanceof Member) {
                $reward = mt_rand($this->settings->reward[0], $this->settings->reward[1]);
                $mysidia->user->changeMoney($reward);
                $this->setField("reward", new Integer($reward));
            }
        }
    }

    public function siggy($aid)
    {
        $mysidia = Registry::get("mysidia");
        // The adoptable is available, let's collect its info
        $usingimage = "no";
        $this->adopt = new OwnedAdoptable($aid);
        $image = $this->adopt->getImage();

        $usegd = $mysidia->settings->gdimages;
        $imageinfo = @getimagesize($image);
        $imagemime = $imageinfo["mime"];

        if (function_exists('imagegif') && $usegd == "yes" && $imagemime == "image/gif") {
            $usingimage = "yes"; //Turn the template system off
            $type = $this->adopt->getType();
            [$width, $height, $type, $attr] = getimagesize($image); // The size of the original adoptable image

            // Lets create the new target image, with a size big enough for the text for the adoptable
            $newheight = $height + 72;
            $newwidth = ($newwidth < 250) ? 250 : $width;
            $img_temp = imagecreatetruecolor($newwidth, $newheight);

            // Lets create the image and save its transparency
            $img_old = @imagecreatefromgif($image);
            imagealphablending($img_old, true);
            imagesavealpha($img_old, true);

            // Lets copy the old image into the new image with
            imagecopyresampled($img_temp, $img_old, 0, 0, 0, 0, $width, $height, $width, $height);
            $textheight = $width + 2;
            $image = $img_temp;
            $bgi = imagecreatetruecolor($newwidth, $newheight);
            $color = imagecolorallocate($bgi, 51, 51, 51);

            // Build text for siggy
            $str1 = "Name: {$this->adopt->getName()}";
            $str2 = "Owner: {$this->adopt->getOwner(Model::MODEL)->getUsername()}";
            $str3 = "Click Here to Feed Me!";
            $str4 = "More Adopts at: ";
            $str5 = "www." . constant("DOMAIN");

            // Renger Image
            imagestring($image, 12, 0, $textheight, $str1, $color);
            imagestring($image, 12, 0, $textheight + 13, $str2, $color);
            imagestring($image, 12, 0, $textheight + 26, $str3, $color);
            imagestring($image, 12, 0, $textheight + 42, $str4, $color);
            imagestring($image, 12, 0, $textheight + 55, $str5, $color);
            $background = imagecolorallocate($image, 0, 0, 0);
            imagecolortransparent($image, $background);

            // At the very last, let's clean up temporary files
            header("Content-Type: image/GIF");
            imagegif($image);
            imagedestroy($image);
            imagedestroy($img_temp);
            imagedestroy($img_old);
            imagedestroy($bgi);

        } else {
            $extList = ["image/gif", "image/jpeg", "image/jpeg", "image/png"];
            //Define the output file type
            $contentType = "Content-type: {$imageinfo['mime']}";
            if (!in_array($imageinfo['mime'], $extList)) throw new InvalidIDException("The file Extension is not allowed!");
            header($contentType);
            $curl = new Curl($image);
            $curl->setHeader();
            $curl->exec();
            $curl->close();
        }
    }

    public function daycare()
    {
        $daycare = new DaycareService;
        $adopts = $daycare->getAdopts();
        $this->setField("daycare", $daycare);
        $this->setField("adopts", $adopts);
    }
}
