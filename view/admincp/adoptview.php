<?php

namespace View\AdminCP;

use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\Legend;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Document\Comment;
use Service\Builder\FieldSetBuilder;
use Service\Builder\FormBuilder;

class AdoptView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $document->add(new Link("admincp/adopt/add", "Add a new adoptable", true));
        $document->add(new Link("admincp/adopt/edit", "Edit an Existing Adoptable", true));
        $document->add(new Link("admincp/adopt/delete", "Delete an Adoptable"));
    }

    public function add()
    {
        // The action of creating a new adoptable!
        $mysidia = Registry::get("mysidia");
        $document = $this->document;

        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar($this->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $adoptForm = new Form("addform", "", "post");
        $title = new Comment("Create a New Adoptable:");
        $title->setBold();
        $title->setUnderlined();
        $adoptForm->add($title);

        $basicInfo = new FieldSetBuilder("Basic Information");
        $basicInfo->add(new Comment("Adoptable Species: ", false));
        $basicInfo->add(new TextField("type"));
        $basicInfo->add(new Comment("(This may contain only letters, numbers and spaces)"));
        $basicInfo->add(new Comment("Adoptable Class: ", false));
        $basicInfo->add(new TextField("class"));
        $basicInfo->add(new Comment("(The adoptable class is a category that determines if two adoptables can interbreed or not)"));
        $basicInfo->add(new Comment("Adoptable Description:"));
        $basicInfo->add(new TextArea("description", "", 4, 45));
        $basicInfo->add(new Comment("Adoptable's Egg(initial) Image: ", false));
        $basicInfo->add(new TextField("imageurl"));
        $basicInfo->add(new Comment("(Use a full image path, beginning with http://)"));
        $basicInfo->add(new Comment("OR select an existing image: ", false));
        $basicInfo->buildDropdownList("existingimageurl", "ImageList");

        $shopSettings = new FieldSetBuilder("Shop Settings");
        $shopSettings->add(new Comment("Shop: ", false));
        $shopSettings->buildDropdownList("shop", "AdoptShopList");
        $shopSettings->add(new Comment("Price: ", false));
        $shopSettings->add(new TextField("cost", 0, 10));

        $conditions = new FieldSetBuilder("Adoptable Conditions");
        $conditions->add(new Comment("When can this adoptable be adopted?"));
        $always = new RadioButton("Always Available ", "cba", "always");
        $always->setLineBreak(true);
        $promo = new RadioButton("Only when users use promo code ", "cba", "promo");
        $promo->setLineBreak(true);
        $cond = new RadioButton("Only when the following conditions are met ", "cba", "conditions");
        $cond->setLineBreak(true);
        $conditions->add($always);
        $conditions->add($promo);
        $conditions->add($cond);
        $conditions->add(new CheckBox("The adoptable has not been adopted more than:", "freqcond", "enabled"));
        $freqField = new TextField("number", "", 6);
        $freqField->setLineBreak(false);
        $conditions->add($freqField);
        $conditions->add(new Comment(" times"));
        $conditions->add(new CheckBox("The date is: (For the date, use this format: Year-Month-Day. So, as an example: 2012-06-28)", "datecond", "enabled"));
        $conditions->add(new TextField("date"));
        $conditions->add(new CheckBox("The user does not have more than: ", "maxnumcond", "enabled"));
        $numField = new TextField("morethannum", "", 4);
        $numField->setLineBreak(false);
        $conditions->add($numField);
        $conditions->add(new Comment(" of this type of adoptable"));
        $conditions->add(new CheckBox("The user is a member of the following usergroup: ", "usergroupcond", "enabled"));
        $conditions->buildDropdownList("usergroups", "UsergroupList");

        $miscellaneous = new FieldSetBuilder("Alternative Outcomes");
        $miscellaneous->add(new Legend("Alternative Outcomes"));
        $miscellaneous->add(new Comment("This section allows you to set if you want to enable alternate outcomes. 
								         This setting allows you to specify what the chances are of a user getting an alternate or special version of this adoptable. 
								         Check the checkbox below to enable this feature and then fill out the information below.."));
        $miscellaneous->add(new CheckBox("<b>Enable Alternate Outcomes</b>", "alternates", "enabled"));
        $miscellaneous->add(new Comment("Start using the alternate outcome at level number: ", false));
        $miscellaneous->add(new TextField("altoutlevel", "", 4));
        $miscellaneous->add(new Comment("(Use Level 0 to have the alternate outcome be used from birth. This will not affect the first / egg image.)"));
        $miscellaneous->add(new Comment("<b>Note:</b> Once alternate outcomes are enabled, you may add them manually by following ", false));
        $miscellaneous->add(new Link("admincp/alternate/add", "this link.", true));

        $adoptForm->add($basicInfo);
        $adoptForm->add($shopSettings);
        $adoptForm->add($conditions);
        $adoptForm->add($miscellaneous);
        $adoptForm->add(new Button("Create this Adoptable", "submit", "submit"));
        $document->add($adoptForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;

        if (!$mysidia->input->post("choose")) {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $adoptForm = new FormBuilder("chooseform", "edit", "post");
            $adoptForm->buildDropdownList("adopt", "AdoptTypeList")
                ->buildButton("Choose", "choose", "choose");
            $document->add($adoptForm);
        } elseif ($mysidia->input->post("submit")) {
            // A form has been submitted, time to carry out out action.
            if ($mysidia->input->post("delete") == "yes") {
                $contentvar = $mysidia->input->post("deltype");
                $titlevar = "{$contentvar}_title";
                $document->setTitle($this->lang->{$titlevar});
                $document->addLangvar($this->lang->{$contentvar});
            } else {
                $document->setTitle($this->lang->edited_title);
                $document->addLangvar($this->lang->edited);
            }
        } else {
            $adopt = $this->getField("adopt");
            $conditions = $adopt->getConditions();
            $availtext = ($adopt->getWhenAvailable() != "always" && $adopt->getWhenAvailable() != "")
                ? $this->lang->restricted : $this->lang->unrestricted;
            $document->setTitle($this->lang->edit_adopt . $adopt->getType());
            $document->add($adopt->getEggImage(Model::GUI));
            $document->add(new Comment("<br>This page allows you to edit {$adopt->getType()}.  Use the form below to edit (or delete) {$adopt->getType()}."));

            $adoptForm = new FormBuilder("editform", "edit", "post");
            $adoptForm->add(new Comment("Egg Image:", true, "b"));
            $adoptForm->add(new Comment("If you wish to change the egg image for this adoptable, you may do so below. "));
            $adoptForm->add(new Comment("Select a new Egg Image: ", false));
            $adoptForm->buildDropdownList("eggimage", "ImageList");
            $adoptForm->add(new Comment("Adoptable Delete Settings:", true, "b"));
            $adoptForm->add(new CheckBox("<b>I want to delete this adoptable </b>", "delete", "yes"));
            $adoptForm->add(new Comment("What sort of deletion do you wish to perform for this adoptable?"));

            $soft = new RadioButton($this->lang->soft_comment, "deltype", "soft");
            $soft->setLineBreak(true);
            $hard = new RadioButton($this->lang->hard_comment, "deltype", "hard");
            $hard->setLineBreak(true);
            $adoptForm->add($soft);
            $adoptForm->add($hard);

            $adoptForm->add(new Comment("Adoptable Adoption Conditions: ", true, "b"));
            $adoptForm->add(new Comment($availtext));
            $adoptForm->add(new CheckBox($this->lang->condition_comment, "resetconds", "yes"));
            $adoptForm->add(new CheckBox("Reset only the date condition for this adoptable to the following value:", "resetdate", "yes"));

            $date = $conditions->hasDateCondition() ? $conditions->getDateCondition("Y-m-d") : null;
            $adoptForm->add(new TextField("date", $date));
            $adoptForm->add(new Comment("(Ex: 2012-06-28)"));
            $adoptForm->add(new Comment($this->lang->date_comment));

            $adoptForm->add(new Comment("Other Settings: ", true, "b"));
            $adoptForm->add(new Comment("Shop: ", false));
            $adoptForm->buildDropdownList("shop", "AdoptShopList", $adopt->getShop());
            $adoptForm->add(new Comment("Price: ", false));
            $adoptForm->add(new TextField("cost", $adopt->getCost(), 10));
            $adoptForm->add(new CheckBox("<b>Enable Alternate Outcomes</b>", "alternates", "enabled", $adopt->getAlternates()));
            $adoptForm->add(new Comment("Start using the alternate outcome at level number: ", false));
            $adoptForm->add(new TextField("altoutlevel", $adopt->getAltLevel(), 4));
            $adoptForm->add(new Comment("(Use Level 0 to have the alternate outcome be used from birth. This will not affect the first / egg image.)"));
            $adoptForm->add(new Comment("<b>Note:</b> Once alternate outcomes are enabled, you may add them manually by following ", false));
            $adoptForm->add(new Link("admincp/alternate/add", "this link.", true));

            $adoptForm->add(new PasswordField("hidden", "adopt", $adopt->getID()));
            $adoptForm->add(new PasswordField("hidden", "choose", "choose"));
            $adoptForm->add(new Button("Submit Changes", "submit", "submit"));
            $document->add($adoptForm);
        }
    }

    public function delete()
    {

    }
}
