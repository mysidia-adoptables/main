<?php

namespace Controller\AdminCP;
use Exception;
use Model\DomainModel\Adoptable;
use Model\DomainModel\Level;
use Model\Settings\DaycareSettings;
use Model\Settings\LevelSettings;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\Integer;

class LevelController extends AppController{

    private $settings;
	
	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
        $this->settings = new LevelSettings($mysidia->db);
		if($mysidia->usergroup->getpermission("canmanageadopts") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage levels.");
		}
    }
	
	public function index(){
		throw new InvalidActionException("global_action");
	}
	
	public function add($id = NULL){
	    $mysidia = Registry::get("mysidia");
	    if(!$mysidia->input->post("select")) return;
		elseif(!$mysidia->input->post("submit")){
            try{
                $id = $id ? $id : $mysidia->input->post("adopt");
                $adopt = new Adoptable($id);
                $mysidia->session->assign("acpLevel", "add", TRUE);             				
				
				$currentlevel = $mysidia->db->select("levels", [], "adopt = :adopt", ["adopt" => $id])->rowCount();
                if($currentlevel > $this->settings->maximum) throw new InvalidActionException("maximum");			
                $previouslevel = $currentlevel - 1;
				$prevlevelclicks = $mysidia->db->select("levels", ["requiredclicks"], "adopt = :adopt AND level = :level", ["adopt" => $id, "level" => $previouslevel])->fetchColumn();									

                $this->setField("adopt", $adopt);
                $this->setField("settings", $this->settings);
                $this->setField("currentlevel", new Integer($currentlevel));	
				$this->setField("prevlevelclicks", new Integer($prevlevelclicks));                           
            }
            catch(InvalidActionException $lne){ 
                throw $lne;
            }
            catch(Exception $e){
                throw new InvalidIDException("global_id");	
            }
		}
        else{
			if($mysidia->session->fetch("acpLevel") != "add"){
                $this->setFlag("global_error", "Session already expired...");
				return;
            }  
            $adopt = new Adoptable($mysidia->input->post("adopt"));
            $currentLevel = $mysidia->input->post("currentlevel"); 
            $primaryHosted = $mysidia->input->post("primaryhosted");  
            $primaryLocal = $mysidia->input->post("primarylocal");
            $reqclicks = $mysidia->input->post("reqclicks");

            for($i = $currentLevel; $i < $this->settings->maximum + 1; $i++){
                try{
                    $n = $i - $currentLevel;
                    $this->dataValidate($n);
			        $primimg = ($primaryHosted[$n] && $primaryLocal[$n] == "none") ? $primaryHosted[$n] : $primaryLocal[$n];
                    $mysidia->db->insert("levels", ["lvid" => NULL, "adopt" => $adopt->getID(), "level" => $i, "requiredclicks" => (int)$reqclicks[$n], 
			                                        "primaryimage" => $primimg, "rewarduser" => $mysidia->input->post("isreward"), "promocode" => $mysidia->input->post("rewardcode")]);		
                }
                catch(Exception $e){
                    if($i == $currentLevel) throw $e;
                    else break;
                }
            }
            $this->setField("adopt", $adopt);
        }
	}
	
	public function edit($id = NULL, $level = NULL){
	    $mysidia = Registry::get("mysidia");
        if($mysidia->input->post("submit")){ 
            try{
                $adoptLevel = new Level($id, $level);
                $primaryHosted = $mysidia->input->post("primaryhosted");  
                $primaryLocal = $mysidia->input->post("primarylocal");
                $reqclicks = (int)$mysidia->input->post("reqclicks");
			    $primimg = ($primaryHosted && $primaryLocal == "none") ? $primaryHosted : $primaryLocal;
			    if($primimg && $primimg != "none") $adoptLevel->updatePrimaryImage($primimg);
                if($reqclicks) $adoptLevel->updateRequiredClicks($reqclicks);
            }
            catch(Exception $e){
               throw new InvalidIDException("nonexist");                 
            }            
        }
        elseif($mysidia->input->post("select") || ($id && $level)){ 
            try{
                $id = $id ? $id : $mysidia->input->post("adopt");
                $adopt = new Adoptable($id);
                $this->setField("adopt", $adopt);
			    if(!$level){
					$this->setField("levels", $adopt->getLevels(FALSE));
                    $this->setField("level", NULL);
				}
                else{  
					$this->setField("level", $adopt->getLevel($level));					
                }                
            }
            catch(Exception $e){
               throw new InvalidIDException("global_id"); 
            }            
        }
	}

	public function delete($id = NULL, $level = NULL){
	    $mysidia = Registry::get("mysidia");
		if($mysidia->input->post("submit")){
            try{	
		        $adoptLevel = new Level($id, $level);
                $mysidia->db->delete("levels", "adopt = '{$adoptLevel->getAdopt()}' AND level >= '{$adoptLevel->getLevel()}'");		              
            }
            catch(Exception $e){
                throw new InvalidIDException("global_id");
            }
		}
        elseif($mysidia->input->post("select")){ 
            $this->edit($id, $level);
        }
        elseif($level) $this->setField("level", new Level($id, $level));
	}

    public function settings(){
	    $mysidia = Registry::get("mysidia");
		if($mysidia->input->post("submit")){
		    $settings = ['system', 'method', 'maximum', 'clicks', 'number', 'reward', 'owner'];
			foreach($settings as $name){			
				if($mysidia->input->post($name) != ($this->settings->{$name})) $mysidia->db->update("levels_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);	 
			}
		    return;
		}		
		$this->setField("levelSettings", $this->settings);
    }
	
	public function daycare(){
	    $mysidia = Registry::get("mysidia");
	    $daycareSettings = new DaycareSettings($mysidia->db);			
		if($mysidia->input->post("submit")){
		    $settings = ['system', 'display', 'number', 'columns', 'level', 'species', 'info', 'owned'];
			foreach($settings as $name){			
				if($mysidia->input->post($name) != ($daycareSettings->$name)) $mysidia->db->update("daycare_settings", ["value" => $mysidia->input->post($name)], "name = :name", ["name" => $name]);	 
			}
            return;		
		}
		$this->setField("daycareSettings", $daycareSettings);	
	}

	private function dataValidate($i){
	    $mysidia = Registry::get("mysidia");
        $adopt = $mysidia->input->post("adopt");
        $currentLevel = $mysidia->input->post("currentlevel"); 
        $primaryHosted = $mysidia->input->post("primaryhosted");  
        $primaryLocal = $mysidia->input->post("primarylocal");
        $reqclicks = $mysidia->input->post("reqclicks");
        $prevclicks = $mysidia->input->post("prevclicks");
   
		if(!$adopt || !$currentLevel) throw new BlankFieldException("name");
		if(!$primaryHosted[$i] && !$primaryLocal[$i]) throw new BlankFieldException("primary_image");
		if(!$primaryHosted[$i] && $primaryLocal[$i] == "none") throw new BlankFieldException("primary_image");
		if(!is_numeric($reqclicks[$i])) throw new BlankFieldException("clicks");
		if($prevclicks >= $reqclicks[$i]) throw new InvalidActionException("clicks2"); 	
		return TRUE;
	}
}