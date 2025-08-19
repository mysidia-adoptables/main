<?php

namespace Service\ApplicationService;

use ArrayObject, Exception;
use Model\DomainModel\Member;
use Model\DomainModel\MemberNotfoundException;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Native\MysObject;
use Resource\Utility\Date;
use Resource\Utility\Password;
use Service\ApplicationService\MyBBService;

class AccountService extends MysObject
{

    private $mybbService;

    public function __construct(private readonly Password $password)
    {
        $this->mybbService = new MyBBService;
    }

    public function register(ArrayObject $form)
    {
        $mysidia = Registry::get("mysidia");
        $today = new Date;
        $username = $form['username'];
        $password = $this->password->hash($form["password"]);
        $mysidia->db->insert("users", ["uid" => null, "username" => $username, "salt" => null, "password" => $password, "session" => null, "email" => $form["email"], "ip" => $form["ip"],
            "usergroup" => 3, "birthday" => $form["birthday"], "membersince" => $today->format('Y-m-d'), "money" => $mysidia->settings->startmoney, "friends" => null]);
        $uid = $mysidia->db->select("users", ["uid"], "username = :username", ["username" => $username])->fetchColumn();

        $mysidia->db->insert("users_contacts", ["uid" => $uid, "website" => null, "facebook" => null, "twitter" => null, "aim" => null, "yahoo" => null, "msn" => null, "skype" => null]);
        $mysidia->db->insert("users_options", ["uid" => $uid, "newmessagenotify" => 1, "pmstatus" => 0, "vmstatus" => 0, "tradestatus" => 0, "theme" => $mysidia->settings->theme]);
        $mysidia->db->insert("users_permissions", ["uid" => $uid, "canlevel" => 'yes', "canvm" => 'yes', "canfriend" => 'yes', "cantrade" => 'yes', "canbreed" => 'yes', "canpound" => 'yes', "canshop" => 'yes']);
        $mysidia->db->insert("users_profile", ["uid" => $uid, "avatar" => $form["avatar"], "bio" => $form["bio"], "color" => $form["color"], "about" => null, "favpet" => 0, "gender" => $form["gender"], "nickname" => $form["nickname"]]);

        if ($this->mybbService->isEnabled()) {
            $this->mybbService->register($username, $password, $form["email"], $form["avatar"], $form["birthday"]);
            $this->mybbService->rebuildStats($username);
        }
    }

    public function login($username)
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->session->clientip != $_SERVER['REMOTE_ADDR']) throw new Exception('Your IP has changed since last session, please log in again.');
        else {
            $mysidia->cookies->setcookies($username);
            $mysidia->db->update("users", ["session" => $mysidia->cookies->getcookies("myssession")], "username = :username", ["username" => $username]);
            if ($this->mybbService->isEnabled()) $this->mybbService->login($username);
            return true;
        }
    }

    public function logout()
    {
        $mysidia = Registry::get("mysidia");
        $mysidia->session->destroy();
        $uid = $mysidia->cookies->getcookies("mysuid");
        $session = $mysidia->cookies->getcookies("myssession");
        if ($uid && $session) {
            $mysidia->cookies->deletecookies();
            if ($this->mybbService->isEnabled()) $this->mybbService->logout($uid);
        } else throw new AuthenticationException("loggedout");
    }

    public function authenticate($username, $password)
    {
        try {
            $user = new Member($username);
            if (!$this->password->verify($password, $user->getPassword())) {
                $this->authenticateLegacy($user, $username, $password);
            }
            return $user;
        } catch (MemberNotfoundException $mne) {
            throw new AuthenticationException($mne->getMessage());
        }
    }

    private function authenticateLegacy(Member $user, $username, $password)
    {
        if ($user->getPassword() != $this->encryptPasswordLegacy($username, $password, $user->getSalt())) {
            throw new AuthenticationException("password_current");
        }
        $this->updatePassword($user, $password);
    }

    private function encryptPasswordLegacy($username, $password, $salt)
    {
        $mysidia = Registry::get("mysidia");
        $pepper = $mysidia->settings->peppercode;
        $newpassword = sha1($username . md5((string) $password));
        return hash('sha512', $pepper . $newpassword . $salt);
    }

    public function updatePassword(Member $user, $password)
    {
        $passwordHash = $this->password->hash($password);
        $user->setPassword($passwordHash, Model::UPDATE);
    }

    public function isValidEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) return false;
        $mysidia = Registry::get("mysidia");
        $existingEmail = $mysidia->db->select("users", ["email"], "email = :email", ["email" => $email])->fetchColumn();
        return $existingEmail ? false : true;
    }
}
