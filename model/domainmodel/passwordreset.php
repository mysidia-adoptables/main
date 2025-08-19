<?php

namespace Model\DomainModel;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidCodeException;
use Resource\Utility\Date;

class PasswordReset extends Model
{

    protected $id;
    protected $username;
    protected $email;
    protected $code;
    protected $ip;
    protected $date;

    public function __construct($id = null, $username = null, $dto = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$dto) {
            $whereClause = $username ? "username = :username" : "id = :id";
            $values = $username ? ["username" => $username] : ["id" => $id];
            $dto = $mysidia->db->select("passwordresets", [], $whereClause, $values)->fetchObject();
            if (!is_object($dto)) throw new InvalidCodeException("The password reset code does not exist...");
        }
        parent::__construct($dto);
    }

    protected function createFromDTO($dto)
    {
        parent::createFromDTO($dto);
        $this->date = new Date($dto->date);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getUser($fetchMode = "")
    {
        if ($fetchMode == Model::MODEL) return new Member($this->username);
        else return $this->username;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function getDate($format = null)
    {
        return $format ? $this->date->format($format) : $this->date;
    }

    public function sendResetEmail(Member $user = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$user) $user = $this->getUser(Model::MODEL);
        $headers = "From: {$mysidia->settings->systememail}";
        $message = "Hello there {$user->getUsername()}:\n\nOur records indicate that you requested a password reset for your account.  Below is your reset code:\n
                    Reset Code: {$this->code}\n\nTo have your password changed please visit the following URL:\n
                    {$mysidia->path->getAbsolute()}forgotpass/reset 
                    \n\nIf you did NOT request a password reset then please ignore this email to keep your current password.\n\n
                    Thanks,\nThe {$mysidia->settings->sitename} team.";
        mail((string) $user->getEmail(), "Password Reset Request for {$user->getUsername()}", $message, $headers);
    }

    protected function save($field, $value)
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->db->update("passwordresets", [$field => $value], "id='{$this->id}'");
    }
}
