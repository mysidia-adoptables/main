<?php

namespace View\Main;

use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\Option;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\DropdownList;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\RadioList;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Paragraph;
use Service\Builder\FormBuilder;

class AccountView extends View
{
    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($mysidia->user->getUsername() . $this->lang->title);
        $document->addLangvar($this->lang->manage, true);

        $settings = new Comment("Account Settings");
        $settings->setBold();
        $settings->setUnderlined();
        $document->add(new Comment());
        $document->add($settings);

        //$document->addLangvar($this->addTemplate("accountlinks"));
        $document->add(new Link("myadopts", "Manage Adoptables", true));
        $document->add(new Link("profile/view/{$mysidia->user->getID()}", "View Profile", true));
        $document->add(new Link("account/password", "Change Password", true));
        $document->add(new Link("account/email", "Change Email Address", true));
        $document->add(new Link("account/friends", "View and Manage FriendList", true));
        $document->add(new Link("account/profile", "More Profile Settings", true));
        $document->add(new Link("account/contacts", "Change Other Settings"));
    }

    public function password()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->password_updated_title);
            $document->addLangvar($this->lang->password_updated, true);
            return;
        }

        $document->setTitle($this->lang->password_title);
        $document->addLangvar($this->lang->password);

        $formbuilder = new FormBuilder("password", "password", "post");
        $formbuilder->buildComment("Your Current Password: ", false)
            ->buildPasswordField("password", "cpass", "", true)
            ->buildComment("Your New Password: ", false)
            ->buildPasswordField("password", "np1", "", true)
            ->buildComment("Confirm Your Password: ", false)
            ->buildPasswordField("password", "np2", "", true)
            ->buildPasswordField("hidden", "action", "password")
            ->buildComment("")
            ->buildButton("Change Password", "submit", "submit");
        $document->add($formbuilder);
    }

    public function email()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->email_update_title);
            $document->addLangvar($this->lang->email_update);
            return;
        }

        $document->setTitle($this->lang->email_title);
        $document->addLangvar($this->lang->email, true);
        $formbuilder = new FormBuilder("email", "email", "post");
        $formbuilder->buildComment("New Email Address: ", false)
            ->buildPasswordField("email", "email")
            ->buildPasswordField("hidden", "action", "changeemail", true)
            ->buildButton("Update Email Address", "submit", "submit");
        $document->add($formbuilder);
    }

    public function friends()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $totalFriends = $this->getField("totalFriends");
        $profileViewModel = $this->getField("profileViewModel");
        $document->setTitle($mysidia->user->getUsername() . $this->lang->friendlist);
        $document->add(new Paragraph(new Comment("You currently have {$totalFriends->getValue()} friends.")));
        $document->add(new Link("friends/edit", "View My Friend Request", true));
        $document->add(new Link("friends/option", "Set Friend-based Options", true));
        $document->add($profileViewModel->display("friends"));
    }

    public function profile()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->profile_updated_title);
            $document->addLangvar($this->lang->profile_updated);
            return;
        }

        $profile = $this->getField("profile");
        $petMap = $this->getField("petMap");
        $document->setTitle($this->lang->profile_title);
        $document->addLangvar($this->lang->profile);
        $profileForm = new Form("profile", "profile", "post");
        $formTitle = new Comment("Profile Details: ");
        $formTitle->setBold();
        $formTitle->setUnderlined();

        $profileForm->add($formTitle);
        $profileForm->add(new Comment("Avatar: ", false));
        $profileForm->add(new TextField("avatar", $profile->getAvatar()));
        $profileForm->add(new Comment("Nickname: ", false));
        $profileForm->add(new TextField("nickname", $profile->getNickname()));
        $profileForm->add(new Comment("Gender: "));

        $genderList = new RadioList("gender");
        $genderList->add(new RadioButton("Male", "gender", "male"));
        $genderList->add(new RadioButton("Female", "gender", "female"));
        $genderList->add(new RadioButton("Unknown", "gender", "unknown"));
        $genderList->check($profile->getGender());

        $profileForm->add($genderList);
        $profileForm->add(new Comment("Favorite Color", false));
        $profileForm->add(new TextField("color", $profile->getColor()));
        $profileForm->add(new Comment("Bio: "));
        $profileForm->add(new TextArea("bio", $profile->getBio()));
        $profileForm->add(new Comment($this->lang->bio));

        $petSpotLight = new Comment("Pet Spotlight Details: ");
        $petSpotLight->setBold();
        $petSpotLight->setUnderlined();
        $profileForm->add($petSpotLight);
        $profileForm->add(new Comment("Favorite Pet ID: ", false));

        $favPet = new DropdownList("favpet");
        $favPet->add(new Option("None Selected", "none"));
        $favPet->fill($petMap, $profile->getFavpetID());
        $profileForm->add($favPet);
        $profileForm->add(new Comment("Favorite Pet Bio: "));
        $profileForm->add(new TextArea("about", $profile->getFavpetInfo()));
        $profileForm->add(new Comment($this->lang->favpet));
        $profileForm->add(new PasswordField("hidden", "action", "moreprofile"));
        $profileForm->add(new Button("Edit My Profile", "submit", "submit"));
        $document->add($profileForm);
    }

    public function contacts()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->settings_updated_title);
            $document->addLangvar($this->lang->settings_updated);
            return;
        }

        $document->setTitle($this->lang->settings_title);
        $document->addLangvar($this->lang->settings);
        $contacts = $mysidia->user->getContact();
        $options = $mysidia->user->getOption();

        $optionsForm = new Form("contacts", "contacts", "post");
        $optionsForm->add(new CheckBox(" Notify me via email when I receive a new message or reward code", "newmsgnotify", 1, $options->hasNewMessageNotify()));
        $details = new Comment("Publically Viewable Details: ");
        $details->setUnderlined();
        $optionsForm->add($details);

        $contactList = $this->getField("contactList");
        $iterator = $contactList->iterator();
        while ($iterator->hasNext()) {
            $contact = $iterator->next();
            $contactMethod = "get{$contact->capitalize()}";
            $comment = new Comment("{$contact->capitalize()} Account: ");
            $optionsForm->add($comment);
            $optionsForm->add(new TextField($contact, $contacts->$contactMethod()));
        }

        $optionsForm->add(new PasswordField("hidden", "action", "changesettings"));
        $optionsForm->add(new Button("Change Settings", "submit", "submit"));
        $document->add($optionsForm);
    }
}
