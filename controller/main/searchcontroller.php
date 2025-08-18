<?php

namespace Controller\Main;
use Model\DomainModel\Item;
use Model\DomainModel\Member;
use Model\DomainModel\OwnedAdoptable;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Registry;
use Resource\Exception\InvalidActionException;
use Service\ApplicationService\SearchException;

class SearchController extends AppController{

    public function __construct(){
        parent::__construct("member");
    }

    public function user(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
            $whereClause = $this->getWhereClause("user");	
			if($whereClause == "1") throw new SearchException("action");
			$stmt = $mysidia->db->select("users", [], "{$whereClause} ORDER BY uid DESC");
	        if($stmt->rowCount() == 0) throw new SearchException("result"); 
			$userList = new ArrayList;			
			while($dto = $stmt->fetchObject()){
				$userList->add(new Member($dto->uid, $dto));
			}
		    $this->setField("userList", $userList);	
			return;
		}
        $stmt = $mysidia->db->select("groups", ["groupname", "gid"], "1 ORDER BY gid ASC");
        $groupMap = $mysidia->db->fetchMap($stmt);
        $this->setField("groupMap", $groupMap);		
	}
	
	public function adopt(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $whereClause = $this->getWhereClause("adopt");			
			if($whereClause == "1") throw new SearchException("action");
			$stmt = $mysidia->db->join("adoptables", "adoptables.id = owned_adoptables.adopt")
                                ->select("owned_adoptables", [], "{$whereClause} and owner != 0 ORDER BY aid ASC");
	        if($stmt->rowCount() == 0) throw new SearchException("result"); 
	        $adoptList = new ArrayList;
			while($dto = $stmt->fetchObject()){
				$adoptList->add(new OwnedAdoptable($dto->aid, $dto));
			}
            $this->setField("adoptList", $adoptList);
			return;
		}
	}

    public function item(){
	    $mysidia = Registry::get("mysidia");		
	    if($mysidia->input->post("submit")){
		    $whereClause = $this->getWhereClause("item");			
			if($whereClause == "1") throw new SearchException("action");
			$stmt = $mysidia->db->select("items", [], "{$whereClause} ORDER BY id ASC");
		    if($stmt->rowCount() == 0) throw new SearchException("result");  
			$itemList = new ArrayList;
			while($dto = $stmt->fetchObject()){
				$itemList->add(new Item($dto->id, $dto));
			}
            $this->setField("itemList", $itemList);
			return;
		}
        $stmt = $mysidia->db->select("items_functions", ["function"], "1 ORDER BY ifid ASC");
		$funcList = $mysidia->db->fetchList($stmt);
        $this->setField("funcList", $funcList);	
	}
	
	private function getWhereClause($criteria){
        $mysidia = Registry::get("mysidia");
	    $whereClause = "1";

        switch($criteria){
            case "user":
                $whereClause .= (!$mysidia->input->post("name"))?"":" and username LIKE '%{$mysidia->input->post("name")}%'";
                $whereClause .= (!$mysidia->input->post("email"))?"":" and email LIKE '%{$mysidia->input->post("email")}%'";
                $whereClause .= ($mysidia->input->post("group") == "none")?"":" and usergroup = '{$mysidia->input->post("group")}'";		      
                $whereClause .= (!$mysidia->input->post("birthday"))?"":" and birthday LIKE '%{$mysidia->input->post("birthday")}%'";
		        $whereClause .= (!$mysidia->input->post("joindate"))?"":" and membersince LIKE '%{$mysidia->input->post("joindate")}%'";
                break;
            case "adopt":
		        $whereClause .= (!$mysidia->input->post("name"))?"":" and name LIKE '%{$mysidia->input->post("name")}%'";
                $whereClause .= (!$mysidia->input->post("type"))?"":" and type LIKE '%{$mysidia->input->post("type")}%'";
                $whereClause .= (!$mysidia->input->post("owner"))?"":" and owner LIKE '%{$mysidia->input->post("owner")}%'";
		        $whereClause .= (!$mysidia->input->post("gender"))?"":" and gender = '{$mysidia->input->post("gender")}'";
		        $whereClause .= (!$mysidia->input->post("minlevel"))?"":" and currentlevel >= '{$mysidia->input->post("minlevel")}'";
		        break;
            case "item":
                $whereClause .= (!$mysidia->input->post("name"))?"":" and itemname LIKE '%{$mysidia->input->post("name")}%'";
                $whereClause .= (!$mysidia->input->post("category"))?"":" and category LIKE '%{$mysidia->input->post("category")}%'";
                $whereClause .= ($mysidia->input->post("function") == "none")?"":" and function LIKE '%{$mysidia->input->post("function")}%'";
		        $whereClause .= (!$mysidia->input->post("shop"))?"":" and shop LIKE '%{$mysidia->input->post("shop")}%'";
		        $whereClause .= (!$mysidia->input->post("maxprice"))?"":" and price <= '{$mysidia->input->post("maxprice")}'";
		        break;   
            default:
                throw new InvalidActionException($mysidia->lang->global_action);
        }
        return $whereClause;
	}
}