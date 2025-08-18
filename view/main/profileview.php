<?php

namespace View\Main;
use Resource\Collection\LinkedHashMap;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\Link;
use Resource\GUI\Component\PasswordField;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Container\Form;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Paragraph;
use Resource\Native\MysString;
use Service\Builder\TabBuilder;

class ProfileView extends View{
	
	public function index(){
		$pagination = $this->getField("pagination");
		$users = $this->getField("users");		
		$document = $this->document;	
		$document->setTitle($this->lang->title);		
        $document->addLangvar($this->lang->memberlist);
		
		$iterator = $users->iterator();
		while($iterator->hasNext()){
		    $user = $iterator->next();
		    if($user->getUsergroup() <= 2) $document->add(new Image("templates/icons/star.gif"));
			$document->add(new Link("profile/view/{$user->getID()}", $user->getUsername(), TRUE));
		}
		$document->addLangvar($pagination->showPage());
	}
	
	public function view(){
		$mysidia = Registry::get("mysidia");
		$user = $this->getField("user");
		$profile = $this->getField("profile");
		$document = $this->document;
		$document->setTitle($user->getUsername() . $this->lang->profile);

        $tabsMap = new LinkedHashMap;
        $tabsMap->put(new MysString("Visitor Message"), new MysString("visitormessage"));
        $tabsMap->put(new MysString("About Me"), new MysString("aboutme"));
        $tabsMap->put(new MysString("Adoptables"), new MysString("adopts"));
        $tabsMap->put(new MysString("Friends"), new MysString("friends"));
        $tabsMap->put(new MysString("Contact Info"), new MysString("contactinfo"));
        $tabs = new TabBuilder(5, $tabsMap, 2);
        $document->addLangvar($tabs->createTab());
	 
	    // Here we go with the first tab content: Visitor Message
	    $document->addLangvar($tabs->startTab(0));
		$vmTitle = new Comment($user->getUsername() . $this->lang->VM_member);
		$vmTitle->setBold();
		$vmTitle->setUnderlined();
		$document->add($vmTitle);
	    $document->add($profile->display("vmessages"));
	 
	    if(!$mysidia->user->isloggedin()) $document->addLangvar($this->lang->VM_guest);
	    elseif(!$mysidia->user->hasPermission("canvm")) $document->addLangvar($this->lang->VM_banned);
	    else{
			$document->addLangvar($this->lang->VM_post);
		    $vmForm = new Form("vmform", "{$user->getID()}", "post");
			$vmForm->add(new PasswordField("hidden", "user", $user->getID()));
			$vmForm->add(new TextArea("vmtext", "", 4, 50));
			$vmForm->add(new Button("Post Comment", "submit", "submit"));
		    if($mysidia->input->post("vmtext")){
				$reminder = new Paragraph;
				$reminder->add(new Comment("You may now view your conversation with {$user->getUsername()} from ", FALSE));
				$reminder->add(new Link("vmessage/view/{$mysidia->input->post("touser")}/{$mysidia->input->post("fromuser")}", "Here"));
				$document->addLangvar($this->lang->VM_complete);
				$document->add($reminder);
			}	
			else $document->add($vmForm);
	    }
	    $document->addLangvar($tabs->endTab(0));
	 
	    // Now the second tab: About me...
	    $document->addLangvar($tabs->startTab(1));
	    $document->add($profile->display("aboutme"));
	    $document->addLangvar($tabs->endTab(1));
	 
	    // The third tab: Adopts...	
	    $document->addLangvar($tabs->startTab(2));
		if($user->countOwnedAdopts() == 0) $document->addLangvar($user->getUsername() . $this->lang->noadopts);
		else $document->add($profile->display("adopts"));
	    $document->addLangvar($tabs->endTab(2));
	 
	    // The fourth tab: Friends...
	    $document->addLangvar($tabs->startTab(3));
        $document->add($profile->display("friends"));
        $document->addLangvar($tabs->endTab(3));

	    // The last tab: Contact Info!
        $document->addLangvar($tabs->startTab(4)); 
	    $document->add($profile->display("contactinfo", $user->getContact()));
	    $document->addLangvar($tabs->endTab(4));
        $this->addScript("profiletabs.js");
	}
}