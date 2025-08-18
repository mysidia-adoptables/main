<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;
use Resource\Utility\Date;

class Promocode extends Model{
    
    const IDKEY = "pid";    
    protected $pid = 0;
    protected $type;
    protected $user;
    protected $code;
    protected $availability;
    protected $fromdate;
    protected $todate;
    protected $reward;
    protected $valid;
         
    public function __construct($promoinfo = "", $dto = NULL){
        $mysidia = Registry::get("mysidia");
        if($promoinfo instanceof MysString) $promoinfo = $promoinfo->getValue();
        if(!$dto){
            $whereClause = is_numeric($promoinfo) ? "pid = :promoinfo" : "code = :promoinfo";
            $dto = $mysidia->db->select("promocodes", [], $whereClause, ["promoinfo" => $promoinfo])->fetchObject();
		    if(!is_object($dto)) throw new InvalidIDException("The promocode does not exist.");
        }
        parent::__construct($dto);
    }
    
    protected function createFromDTO($dto){
        parent::createFromDTO($dto);
        $this->fromdate = $this->fromdate ? new Date($dto->fromdate) : NULL;
        $this->todate = $this->todate ? new Date($dto->todate) : NULL;
    }
    
    public function getType(){
        return $this->type;
    }
    
    public function getUser($fetchMode = ""){
	    if($fetchMode == Model::MODEL) return new Member($this->user);
	    else return $this->user;
    }
    
    public function getUsername(){
        if(!$this->user) return NULL;
        return $this->getUser(Model::MODEL)->getUsername();
    }
    
    public function getCode(){
        return $this->code;
    }
    
    public function getAvailability(){
        return $this->availability;
    }
    
    public function isAvailable(){ 
        return ($this->availability > 0);
    }
    
    public function getDateFrom($format = NULL){
        if(!$this->fromdate) return NULL;
        return $format ? $this->fromdate->format($format) : $this->fromdate;
    }
    
    public function getDateTo($format = NULL){
        if(!$this->todate) return NULL;
        return $format ? $this->todate->format($format) : $this->todate;
    }
    
    public function getReward(){
        return $this->reward;
    }

    public function execute(){
        // This method will execute the promocode and give users their desired adoptables or items, need to be used after validation is completed	  
	    switch($this->type){
	        case "Adopt":
		        // The user will receive an adoptable from the promocode now.
                $adopt = new Adoptable($this->reward);
                $adopt->makeOwnedAdopt($this->user);
			    $this->usePromo();
		        return $adopt->getType();
		    case "Item":
		        // The user will receive an item from the promocode now.            
			    $item = new OwnedItem($this->reward, $this->user);
                if($item->isOverCap(1)) throw new NoPermissionException("It appears that you cannot add one more of item {$item->getItemname()} to your inventory, its quantity has already exceeded the upper limit.");			    
			    $item->add(1, $this->user);
			    $this->usePromo();
		        return $item->getItemname();
            case "Page":
                $this->usePromo();
                break;
		    default:
		        throw new PromocodeException("type");	 
	    }
    }

    public function usePromo(){
        $this->availability--;
        $this->save("availability", $this->availability);
        return TRUE;	  
    }  
    
    protected function save($field, $value){
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("promocodes", [$field => $value], "pid = '{$this->pid}'");
    }
}