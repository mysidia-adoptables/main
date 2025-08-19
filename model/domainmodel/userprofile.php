<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\GUI\Component\Image;

class UserProfile extends Model
{

    const IDKEY = "uid";
    protected $uid;
    protected $avatar;
    protected $bio;
    protected $color;
    protected $about;
    protected $favpet;
    protected $gender;
    protected $nickname;

    protected $user;

    public function __construct($uid, $dto = null, User $user = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $prefix = constant("PREFIX");
            $dto = $mysidia->db->join("users", "users.uid = users_profile.uid")
                ->select("users_profile", [], "{$prefix}users.uid = :uid", ["uid" => $uid])->fetchObject();
            if (!is_object($dto)) throw new MemberNotfoundException("The specified user profile {$uid} does not exist...");
        }
        parent::__construct($dto);
        $this->user = $user ?: new Member($uid, $dto);
    }

    public function getAvatar($fetchMode = "")
    {
        if ($fetchMode == Model::GUI) return new Image($this->avatar);
        return $this->avatar;
    }

    public function getBio()
    {
        return stripslashes((string) $this->bio);
    }

    public function getColor()
    {
        return $this->color;
    }

    public function getAbout()
    {
        return $this->about;
    }

    public function getFavpet($fetchMode = "")
    {
        if (!$this->favpet) return null;
        elseif ($fetchMode == Model::MODEL) return new OwnedAdoptable($this->favpet);
        else return $this->getFavpetID();
    }

    public function getFavpetID()
    {
        return $this->favpet;
    }

    public function getFavpetInfo()
    {
        return $this->about;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getNickname()
    {
        return $this->nickname;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function isUser(User $user = null)
    {
        if (!$user || !($user instanceof Member)) return false;
        return ($this->uid == $user->getID());
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("users_profile", [$field => $value], "uid='{$this->uid}'");
    }
}
