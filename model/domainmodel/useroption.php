<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;

class UserOption extends Model
{

    const IDKEY = "uid";
    protected $uid;
    protected $newmessagenotify;
    protected $pmstatus;
    protected $vmstatus;
    protected $tradestatus;
    protected $theme;

    protected $user;

    public function __construct($uid, $dto = null, User $user = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $prefix = constant("PREFIX");
            $dto = $mysidia->db->join("users", "users.uid = users_options.uid")
                ->select("users_options", [], "{$prefix}users.uid = :uid", ["uid" => $uid])->fetchObject();
            if (!is_object($dto)) throw new MemberNotfoundException("The specified user option {$uid} does not exist...");
        }
        parent::__construct($dto);
        $this->user = $user ?: new Member($uid, $dto);
    }

    public function hasNewMessageNotify()
    {
        return $this->newmessagenotify;
    }

    public function getPMStatus()
    {
        return $this->pmstatus;
    }

    public function getVMStatus()
    {
        return $this->vmstatus;
    }

    public function getTradeStatus()
    {
        return $this->tradestatus;
    }

    public function getTheme($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Theme($this->theme);
        return $this->theme;
    }

    public function setTheme($theme, $assignMode = "")
    {
        if ($assignMode == Model::UPDATE) $this->save("theme", $theme);
        $this->theme = $theme;
    }

    public function setPrivacy($pm, $vm, $trade)
    {
        $mysidia = Registry::get("mysidia");
        $this->pmstatus = $pm;
        $this->vmstatus = $vm;
        $this->tradestatus = $trade;
        $mysidia->db->update("users_options", ["pmstatus" => (int)$pm, "vmstatus" => (int)$vm, "tradestatus" => (int)$trade], "uid = '{$this->uid}'");
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("users_options", [$field => $value], "uid = '{$this->uid}'");
    }
}
