<?php

namespace View\AdminCP;

use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Resource\Native\MysString;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\PluginTableHelper;

class SettingsView extends View
{
    public function globals()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->basic_update_title);
            $document->addLangvar($this->lang->basic_update);
            return;
        }

        $document->setTitle($this->lang->basic_title);
        $document->addLangvar($this->lang->basic);
        $globalsForm = new FormBuilder("globalsform", "globals", "post");
        $globalsForm->buildComment("Default Theme:   ", false)->buildDropdownList("theme", "ThemeList", $mysidia->settings->theme)
            ->buildComment("Site Name:   ", false)->buildTextField("sitename", $mysidia->settings->sitename)
            ->buildComment("Site Title:   ", false)->buildTextField("browsertitle", $mysidia->settings->browsertitle)
            ->buildComment("Currency Name:	 ", false)->buildTextField("cost", $mysidia->settings->cost)
            ->buildComment("Start Money:	", false)->buildTextField("startmoney", $mysidia->settings->startmoney)
            ->buildComment("Pagination Rows:	", false)->buildTextField("pagination", $mysidia->settings->pagination)
            ->buildComment("Site Slogan:	", false)->buildTextField("slogan", $mysidia->settings->slogan)
            ->buildComment("Admin Email:   ", false)->buildTextField("admincontact", $mysidia->settings->admincontact)
            ->buildComment("System User:   ", false)->buildTextField("systemuser", $mysidia->settings->systemuser)
            ->buildComment("System Email:   ", false)->buildTextField("systememail", $mysidia->settings->systememail)
            ->buildButton("Change Global Settings", "submit", "submit");
        $document->add($globalsForm);
    }

    public function system()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->system_edited_title);
            $document->addLangvar($this->lang->system_edited);
            return;
        }

        $document->setTitle($this->lang->system_title);
        $document->addLangvar($this->lang->system);
        $enabled = new LinkedHashMap();
        $enabled->put(new MysString(" Yes"), new MysString("enabled"));
        $enabled->put(new MysString(" No"), new MysString("disabled"));

        $systemForm = new FormBuilder("systemform", "system", "post");
        $systemForm->add(new Comment("Turn on Main Site:", false, "b"));
        $systemForm->buildRadioList("site", $enabled, $mysidia->systems->site);
        $systemForm->add(new Comment("Enable Adoption:", false, "b"));
        $systemForm->buildRadioList("adopts", $enabled, $mysidia->systems->adopts);
        $systemForm->add(new Comment("Enable Friend Invitation:", false, "b"));
        $systemForm->buildRadioList("friends", $enabled, $mysidia->systems->friends);
        $systemForm->add(new Comment("Enable Items/Inventory:", false, "b"));
        $systemForm->buildRadioList("items", $enabled, $mysidia->systems->items);
        $systemForm->add(new Comment("Enable Private Messages:", false, "b"));
        $systemForm->buildRadioList("messages", $enabled, $mysidia->systems->messages);
        $systemForm->add(new Comment("Enable Who's Online:", false, "b"));
        $systemForm->buildRadioList("online", $enabled, $mysidia->systems->online);
        $systemForm->add(new Comment("Enable Promocode:", false, "b"));
        $systemForm->buildRadioList("promo", $enabled, $mysidia->systems->promo);
        $systemForm->add(new Comment("Enable Registration:", false, "b"));
        $systemForm->buildRadioList("register", $enabled, $mysidia->systems->register);
        $systemForm->add(new Comment("Enable Item/Adopt Shop:", false, "b"));
        $systemForm->buildRadioList("shops", $enabled, $mysidia->systems->shops);
        $systemForm->add(new Comment("Enable Shoutbox:", false, "b"));
        $systemForm->buildRadioList("shoutbox", $enabled, $mysidia->systems->shoutbox);
        $systemForm->add(new Comment("Enable Visitor Messages:", false, "b"));
        $systemForm->buildRadioList("vmessages", $enabled, $mysidia->systems->vmessages);
        $systemForm->add(new Button("Edit System Settings", "submit", "submit"));
        $document->add($systemForm);
    }

    public function theme()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit") == "install") {
            $document->setTitle($this->lang->themes_install_title);
            $document->addLangvar($this->lang->themes_install_success);
        } elseif ($mysidia->input->post("submit") == "update" && $mysidia->input->post("theme") != "none") {
            $document->setTitle($this->lang->themes_update_title);
            $document->addLangvar($this->lang->themes_update_success);
        } else {
            $document->setTitle($this->lang->themes_title);
            $document->addLangvar($this->lang->themes);
            $themesUpdateForm = new FormBuilder("themesupdateform", "theme", "post");
            $themesUpdateForm->buildDropdownList("theme", "ThemeList", $mysidia->settings->theme)
                ->buildButton("Set Default Theme", "submit", "update");
            $document->add($themesUpdateForm);

            $document->addLangvar($this->lang->themes_install);
            $themesInstallForm = new FormBuilder("themesinstallform", "theme", "post");
            $themesInstallForm->buildComment("Theme Name: ", false)->buildTextField("themename")
                ->buildComment($this->lang->themes_update_name)
                ->buildComment("Theme Folder: ", false)->buildTextField("themefolder")
                ->buildComment($this->lang->themes_update_folder)
                ->buildButton("Install new Theme", "submit", "install");
            $document->add($themesInstallForm);
        }
    }

    public function pound()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->pound_edited_title);
            $document->addLangvar($this->lang->pound_edited);
            return;
        }

        $document->setTitle($this->lang->pound_title);
        $document->addLangvar($this->lang->pound);
        $poundsettings = $this->getField("poundsettings");
        $enabled = new LinkedHashMap();
        $enabled->put(new MysString(" Yes"), new MysString("yes"));
        $enabled->put(new MysString(" No"), new MysString("no"));
        $cost = new LinkedHashMap();
        $cost->put(new MysString(" Increment"), new MysString("increment"));
        $cost->put(new MysString(" Percent"), new MysString("percent"));
        $level = new LinkedHashMap();
        $level->put(new MysString(" Increment"), new MysString("increment"));
        $level->put(new MysString(" Multiply"), new MysString("multiply"));
        $rename = new LinkedHashMap();
        $rename->put(new MysString(" Original Owner Only"), new MysString("yes"));
        $rename->put(new MysString(" Everyone"), new MysString("no"));

        $poundForm = new FormBuilder("poundform", "pound", "post");
        $poundForm->add(new Comment("Enable Pound System:", false, "b"));
        $poundForm->buildRadioList("system", $enabled, $poundsettings->system);
        $poundForm->add(new Comment("Enable Readoption:", false, "b"));
        $poundForm->buildRadioList("adopt", $enabled, $poundsettings->adopt);
        $poundForm->add(new Comment($this->lang->pound_adopt));
        $poundForm->add(new Comment("Species immune to Pound(separated by comma):", true, "b"));
        $poundForm->add(new TextField("specieslimit", implode(",", $poundsettings->specieslimit)));
        $poundForm->add(new Comment("Cost to pound/adopt(the format is 'poundcost, adoptcost', make sure to separate them by comma!):", true, "b"));
        $poundForm->add(new TextField("cost", implode(",", $poundsettings->cost)));
        $poundForm->add(new Comment("Select the type of cost(Incremental or Percentage):", true, "b"));
        $poundForm->buildRadioList("costtype", $cost, $poundsettings->costtype);
        $poundForm->add(new Comment("Cost per additional level:", false, "b"));
        $poundForm->add(new TextField("levelbonus", $poundsettings->levelbonus));
        $poundForm->add(new Comment("Select the type of Level Bonus(Incremental or Multiple):", true, "b"));
        $poundForm->buildRadioList("leveltype", $level, $poundsettings->leveltype);
        $poundForm->add(new Comment("Number-based Restriction(How many pets a user can pound or adopt, separated by comma):", true, "b"));
        $poundForm->add(new TextField("number", implode(",", $poundsettings->number)));
        $poundForm->add(new Comment("Number-date Integration(the above limit will be number per day if turned on):", true, "b"));
        $poundForm->buildRadioList("date", $enabled, $poundsettings->date);
        $poundForm->add(new Comment("Pound/Adopt Lag(Define how many days it takes for pounded pets to be allowed for re-adoption):", true, "b"));
        $poundForm->add(new TextField("duration", $poundsettings->duration));
        $poundForm->add(new Comment("Forbid previous owners to re-adopt: ", false, "b"));
        $poundForm->buildRadioList("owner", $enabled, $poundsettings->owner);
        $poundForm->add(new Comment("Maximum times a pet can be pounded: ", false, "b"));
        $poundForm->add(new TextField("recurrence", $poundsettings->recurrence));
        $poundForm->add(new Comment("Pets Rename Constraint: ", false, "b"));
        $poundForm->buildRadioList("rename", $rename, $poundsettings->rename);
        $poundForm->add(new Button("Edit Pound Settings", "submit", "submit"));
        $document->add($poundForm);
    }

    public function plugin()
    {
        $document = $this->document;
        $document->setTitle($this->lang->plugins_title);
        $action = $this->getField("action");
        if ($action) {
            $document->addLangvar("You have successfully {$action}d this plugin.");
            return;
        }

        $document->addLangvar($this->lang->plugins);
        $helper = new PluginTableHelper();
        $pluginTable = new TableBuilder("plugins");
        $pluginTable->setAlign(new Align("center", "middle"));
        $pluginTable->buildHeaders("ID", "Name", "Link", "Status", "Action");
        $pluginTable->setHelper($helper);

        $plugins = $this->getField("plugins");
        $iterator = $plugins->iterator();
        while ($iterator->hasNext()) {
            $plugin = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($plugin->getID()));
            $cells->add(new TCell($plugin->getPluginName()));
            $cells->add(new Link($plugin->getLinkURL(), $plugin->getLinkText()));
            $cells->add(new TCell($plugin->isEnabled() ? "enabled" : "disabled"));
            $cells->add(new TCell($helper->getActionImage($plugin)));
            $pluginTable->buildRow($cells);
        }
        $document->add($pluginTable);
    }

    public function forum()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->forum_integration_title);
        if ($mysidia->input->post("submit")) {
            $document->addLangvar($this->lang->forum_integration_success);
            return;
        }
        $document->addLangvar($this->lang->forum_integration);
        $enabled = $this->getField("enabled");
        $enabledText = ($enabled->getValue() === true) ? "enabled" : "disabled";
        $document->add(new Comment("Your MyBB forum integration is currently {$enabledText}."));

        $forumForm = new FormBuilder("forumform", "forum", "post");
        $forumForm->buildCheckBox("Enable MyBB Forum Integration", "mybbenabled", "1", defined("MYBB_ENABLED") ? MYBB_ENABLED : 0);
        $forumForm->add(new Comment("MyBB Database Host:", false, "b"));
        $forumForm->add(new TextField("mybbhost", defined("MYBB_HOST") ? MYBB_HOST : "localhost"));
        $forumForm->add(new Comment("MyBB Database User:", false, "b"));
        $forumForm->add(new TextField("mybbuser", defined("MYBB_USER") ? MYBB_USER : ""));
        $forumForm->add(new Comment("MyBB Database Password:", false, "b"));
        $forumForm->add(new TextField("mybbpass", defined("MYBB_PASS") ? MYBB_PASS : ""));
        $forumForm->add(new Comment("MyBB Database Name:", false, "b"));
        $forumForm->add(new TextField("mybbname", defined("MYBB_NAME") ? MYBB_NAME : ""));
        $forumForm->add(new Comment("MyBB Forum Path:", false, "b"));
        $forumForm->add(new TextField("mybbpath", defined("MYBB_PATH") ? MYBB_PATH : ""));
        $forumForm->add(new Comment("MyBB Database Prefix:", false, "b"));
        $forumForm->add(new TextField("mybbprefix", defined("MYBB_PREFIX") ? MYBB_PREFIX : ""));
        $forumForm->add(new Button("Configure Forum Integration", "submit", "submit"));
        $document->add($forumForm);
    }
}
