<?php

namespace Controller\AdminCP;

use PDOException;
use Model\DomainModel\ACPHook;
use Model\Settings\PoundSettings;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Database;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\Boolean;
use Resource\Native\MysString;
use Service\ApplicationService\MyBBService;

class SettingsController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage promocodes.");
        }
    }

    public function globals()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $settings = ['theme', 'sitename', 'browsertitle', 'cost',  'slogan', 'admincontact', 'systemuser', 'systememail', 'startmoney', 'pagination'];
            foreach ($settings as $name) {
                if ($mysidia->input->post($name) != ($mysidia->settings->{$name})) {
                    $mysidia->db->update("settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);
                }
            }
        }
    }

    public function system()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $systems = ['site', 'adopts', 'friends', 'items',  'messages', 'online', 'promo', 'register', 'shops', 'shoutbox', 'vmessages'];
            foreach ($systems as $name) {
                $mysidia->db->update("systems_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);
            }
            return;
        }
    }

    public function theme()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit") == "install") {
            if (!$mysidia->input->post("themename") || !$mysidia->input->post("themefolder")) {
                throw new InvalidActionException("themes_install_failed");
            }
            $mysidia->db->insert("themes", ["id" => null, "themename" => $mysidia->input->post("themename"), "themefolder" => $mysidia->input->post("themefolder")]);
        } elseif ($mysidia->input->post("submit") == "update" && $mysidia->input->post("theme") != "none") {
            $dto = $mysidia->db->select("themes", [], "themefolder = :theme", ["theme" => $mysidia->input->post("theme")]);
            if (!$dto) {
                throw new InvalidIDException("themes_update_failed");
            }
            $mysidia->db->update("settings", ["value" => $mysidia->input->post("theme")], "name = 'theme'");
        }
    }

    public function pound()
    {
        $mysidia = Registry::get("mysidia");
        $poundsettings = new PoundSettings($mysidia->db);
        if ($mysidia->input->post("submit")) {
            $settingNames = ['system', 'adopt', 'specieslimit', 'cost', 'costtype', 'levelbonus', 'leveltype', 'number', 'date', 'duration', 'owner', 'recurrence', 'rename'];
            foreach ($settingNames as $name) {
                if ($mysidia->input->post($name) != ($poundsettings->{$name})) {
                    $mysidia->db->update("pounds_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);
                }
            }
        }
        $this->setField("poundsettings", $poundsettings);
    }

    public function plugin($id = null, $actionMethod = null)
    {
        $mysidia = Registry::get("mysidia");
        if ($id && $actionMethod) {
            $plugin = new ACPHook($id);
            if (!in_array($actionMethod, ["enable", "disable"])) {
                throw new InvalidActionException("plugins_action");
            }
            $plugin->$actionMethod();
            $this->setField("action", new MysString($actionMethod));
            return;
        }
        $stmt = $mysidia->db->select("acp_hooks");
        if ($stmt->rowCount() == 0) {
            throw new InvalidIDException("no_plugins");
        }
        $plugins = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $plugins->add(new ACPHook($dto->id, $dto));
        }
        $this->setField("plugins", $plugins);
    }

    public function forum()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            try {
                $forumDB = new Database(
                    $mysidia->input->post("mybbname"),
                    $mysidia->input->post("mybbhost"),
                    $mysidia->input->post("mybbuser"),
                    $mysidia->input->post("mybbpass"),
                    $mysidia->input->post("mybbprefix")
                );
                if ($mysidia->db->nextAutoID("users") != $forumDB->nextAutoID("users")) {
                    throw new InvalidIDException("forum_integration_idsync");
                }
                $mybbenabled = ($mysidia->input->post("mybbenabled") == 1) ? 1 : 0;
                $configdata =
"<?php
//Forum Integration Info: MyBB

define('MYBB_ENABLED', {$mybbenabled});
define('MYBB_HOST', '{$mysidia->input->post("mybbhost")}');
define('MYBB_USER', '{$mysidia->input->post("mybbuser")}');
define('MYBB_PASS', '{$mysidia->input->post("mybbpass")}');
define('MYBB_NAME', '{$mysidia->input->post("mybbname")}');
define('MYBB_PATH', '{$mysidia->input->post("mybbpath")}');
define('MYBB_PREFIX', '{$mysidia->input->post("mybbprefix")}');
define('MYBB_REMEMBER', -1);
?>";
                $file = fopen('config_forums.php', 'w');
                fwrite($file, $configdata);
                fclose($file);
                return;
            } catch (PDOException) {
                throw new InvalidActionException("forum_integration_invalid");
            }
        }
        $mybbService = new MyBBService();
        $this->setField("enabled", new Boolean($mybbService->isEnabled()));
    }
}
