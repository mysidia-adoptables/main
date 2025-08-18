<?php

namespace Controller\Main;
use Model\DomainModel\Theme;
use Resource\Collection\LinkedList;
use Resource\Core\AppController;
use Resource\Core\Model;
use Resource\Core\Registry;

class ChangeStyleController extends AppController{

    public function __construct(){
        parent::__construct("member");
    }
	
	public function index($theme = NULL){
	    $mysidia = Registry::get("mysidia");		
	    if($theme){
		    $themeExists = $mysidia->db->select("themes", ["id"], "themefolder = :theme", ["theme" => $theme])->fetchColumn();	
            if($themeExists) $mysidia->user->getOption()->setTheme($theme, Model::UPDATE);
	        else throw new InvalidIDException("fail");
			return;
		}
		
		$themes = new LinkedList;
		$stmt = $mysidia->db->select("themes", []);	
	    while($dto = $stmt->fetchObject()){
            $themeModel = new Theme($dto->id, $dto);
            if($themeModel->isDisplayble()) $themes->add($themeModel);
	    }
        $this->setField("themes", $themes);
	}
}