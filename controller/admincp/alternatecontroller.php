<?php

namespace Controller\AdminCP;
use Exception;
use Model\DomainModel\Adoptable;
use Model\DomainModel\AdoptAlternate;
use Model\DomainModel\AlternateNotfoundException;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Native\MysArray;

class AlternateController extends AppController{

    const PARAM = "type";
    const PARAM2 = "alid";
	
	public function __construct(){
        parent::__construct();
		$mysidia = Registry::get("mysidia");
		if($mysidia->usergroup->getpermission("canmanageadopts") != "yes"){
		    throw new NoPermissionException("You do not have permission to manage adoptable alternate-forms.");
		}		
    }
	
	public function add(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $this->dataValidate();
			$imageurl = ($mysidia->input->post("existingimageurl") == "none") ? $mysidia->input->post("imageurl") : $mysidia->input->post("existingimageurl");			
		    $mysidia->db->insert("alternates", ["adopt" => $mysidia->input->post("adopt"), "image" => $imageurl, "level" => (int)$mysidia->input->post("level"), "item" => (int)$mysidia->input->post("item"), 
                                                "gender" => $mysidia->input->post("gender"), "lastalt" => (int)$mysidia->input->post("lastalt"), "chance" => (int)$mysidia->input->post("chance")]);		
		}
	}

	public function edit($alid = NULL){
	    $mysidia = Registry::get("mysidia");
        if($mysidia->input->post("submit")){ 
            try{
                $alternate = new AdoptAlternate($alid);
		        $this->dataValidate();
                $imageurl = ($mysidia->input->post("existingimageurl") == "none") ? $mysidia->input->post("imageurl") : $mysidia->input->post("existingimageurl");
		        $mysidia->db->update("alternates", ["adopt" => $mysidia->input->post("adopt"), "image" => $imageurl, "level" => (int)$mysidia->input->post("level"), "item" => (int)$mysidia->input->post("item"), 
                                                    "gender" => $mysidia->input->post("gender"), "lastalt" => (int)$mysidia->input->post("lastalt"), "chance" => (int)$mysidia->input->post("chance")], "alid='{$alternate->getID()}'");
	        }
            catch(Exception $e){
		        throw new InvalidIDException("global_id");
            }            
        }
        elseif($mysidia->input->post("select") || $alid){ 
            try{
			    if(!$alid){
                    $adopt = new Adoptable($mysidia->input->post("adopt"));
                    $levels = $adopt->getLevels(FALSE);
                    $numLevels = $levels->size();
                    $alternates = new MysArray($numLevels);
                    for($i = 1; $i <= $numLevels; $i++){
                        $alternates[$i - 1] = $adopt->getAlternatesForLevel($i);
                    }
                    $this->setField("adopt", $adopt);
                    $this->setField("levels", $levels);
                    $this->setField("alternates", $alternates);
				}
                else{
                    $alternate = new AdoptAlternate($alid);		 			
                }				
                $this->setField("alternate", $alid ? $alternate : NULL);	
			}
            catch(AlternateNotfoundException $anfe){
                throw new InvalidIDException("nonexist");
            }
            catch(Exception $e){
                throw new InvalidIDException("global_id");         
            }            
        }
	}    

	public function delete($alid = NULL){
	    $mysidia = Registry::get("mysidia");
        if($alid){
            try{	
                $alternate = new AdoptAlternate($alid);
                $mysidia->db->delete("alternates", "alid = '{$alternate->getID()}'");
                $this->setField("alternate", $alternate);
            }
            catch(Exception $e){
                throw new InvalidIDException("global_id");
            }
        }
        else $this->edit($alid);
	}
	
	private function dataValidate(){
	    $mysidia = Registry::get("mysidia");
		if(!$mysidia->input->post("adopt")) throw new BlankFieldException("adopt");	
		if(!$mysidia->input->post("imageurl") && $mysidia->input->post("existingimageurl") == "none") throw new BlankFieldException("images");
        if(!$mysidia->input->post("level")) throw new BlankFieldException("level");	
		if(!$mysidia->input->post("chance")) throw new BlankFieldException("chance");	
		return TRUE;
	}
}