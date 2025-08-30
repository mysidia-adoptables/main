<?php

namespace Model\DomainModel;
use Resource\Core\Model;
use Resource\Core\Registry;
use Resource\Native\MysString;
use Resource\Utility\Date;

class ItemFunction extends Model{ 
    
    const IDKEY = "ifid";           
    protected $function;
    protected $intent;
    protected $description;
    protected $validFunctions = ["Valuable", "Level1", "Level2", "Level3", "Click1", "Click2", "Breed1", "Breed2", "Alts1", "Alts2", "Name1", "Name2"];
    
    public function __construct($functioninfo, $dto = NULL){
	    // Fetch the database info into object property
	    $mysidia = Registry::get("mysidia");
        if($functioninfo instanceof MysString) $functioninfo = $functioninfo->getValue();
        if(!$dto){
	        $whereclause = is_numeric($functioninfo) ? "ifid = :functioninfo" : "function = :functioninfo";
	        $dto = $mysidia->db->select("items_functions", [], $whereclause, ["functioninfo" => $functioninfo])->fetchObject();
	        if(!is_object($dto)) throw new ItemException("The item function specified is invalid...");
        }
        parent::__construct($dto);
    }    
    
    public function getFunction(){
        return $this->function;
    }
    
    public function getIntent(){
        return $this->intent;
    }
    
    public function getDescription(){
        return $this->description;
    }
    
    public function apply(OwnedItem $item, Model $target = NULL){
        if($this->function == "Valuable"){
            throw new ItemException("The item {$item->getItemname()} is a valuable item, which cannot be used on any adoptable but may sell a good deal of money.");
        }
        $method = "apply{$this->function}";
        return $this->$method($item, $target);
    }
    
    protected function applyLevel1(OwnedItem $item, OwnedAdoptable $adopt){
        $newLevel = $adopt->getCurrentLevel() + $item->getValue();
        try{
            $newLevelModel = new Level($adopt->getSpeciesID(), $newLevel);
            $adopt->setLevelAndClicks($newLevelModel->getLevel(), $newLevelModel->getRequiredClicks(), "update");
            return "Congratulations, the item {$item->getItemname()} raised your adoptable's level by {$item->getValue()}";
        }
        catch(LevelNotfoundException $lnfe){
            throw new ItemException("Unfortunately, your selected adoptable's level cannot be raised by using item {$item->getItemname()}.");
        }       
    }
    
    protected function applyLevel2(OwnedItem $item, OwnedAdoptable $adopt){
        $newLevel = $item->getValue();
        try{
            $newLevelModel = new Level($adopt->getSpeciesID(), $newLevel);
            $adopt->setLevelAndClicks($newLevelModel->getLevel(), $newLevelModel->getRequiredClicks(), "update");
            return "Congratulations, the item {$item->getItemname()} increases your adoptable's level to {$item->getValue()}";
        }
        catch(LevelNotfoundException $lnfe){
            throw new ItemException("Unfortunately, your selected adoptable's level cannot be raised by using item {$item->getItemname()}.");
        }          
    }
    
    protected function applyLevel3(OwnedItem $item, OwnedAdoptable $adopt){
        $adopt->setLevelAndClicks(0, 0, "update");
        return "Congratulations, the item {$item->getItemname()} has reset the level and clicks of your adoptable.";
    }
    
    protected function applyClick1(OwnedItem $item, OwnedAdoptable $adopt){
        $newclicks = $adopt->getTotalClicks() + $item->getValue();
        $adopt->setTotalClicks($newclicks, "update");
        $note = "By using {$item->getItemname()}, the adoptable's total number of clicks has raised by {$item->getValue()}<br>";

        //Now lets check if the adoptable has reached a new level.  
        if($adopt->hasNextLevel()){
            //new level exists, time to check if the total clicks have reached required minimum clicks for next level.
	        $nextLevel = $adopt->getNextLevel();
	        $requiredClicks = $nextLevel->getRequiredClicks();
            if($requiredClicks && $newclicks >= $requiredClicks){
	            // We need to level this adoptable up...
                $adopt->setCurrentLevel($nextLevel->getLevel(), "update");
                $note .= "And moreover, it has gained a new level!";
            }
        }
        return $note;
    }
    
    protected function applyClick2(OwnedItem $item, OwnedAdoptable $adopt){
        $newclicks = $item->getValue();
        $adopt->setTotalClicks($newclicks, "update");   
        $note = "By using {$item->getItemname()}, the adoptable's total number of clicks has changed to {$item->getValue()}<br>";

        if($adopt->hasNextLevel()){
            //new level exists, time to check if the total clicks have reached required minimum clicks for next level.
	        $nextLevel = $adopt->getNextLevel();
	        $requiredClicks = $nextLevel->getRequiredClicks();
            if($requiredClicks && $newclicks >= $requiredClicks){
	            // We need to level this adoptable up...
                $adopt->setCurrentLevel($nextLevel->getLevel(), "update");
                $note .= "And moreover, it has gained a new level!";
            }
        }
        return $note;
    }
    
    protected function applyClick3(OwnedItem $item, OwnedAdoptable $adopt){
        $mysidia = Registry::get("mysidia");
        $date = new Date; 
        $mysidia->db->delete("vote_voters", "adoptableid = '{$adopt->getAdoptID()}' and date='{$date}'");      
        return "By using item {$item->getItemname()}, you have make your adoptables eligible for clicking by everyone again!";
    }
    
    protected function applyBreed1(OwnedItem $item, OwnedAdoptable $adopt){
        $adopt->setLastBred(0, "update");
        return "The item {$item->getItemname()} has been successfully used on your adoptable, it can breed again!<br>";
    }
    
    protected function applyBreed2(OwnedItem $item, OwnedAdoptable $adopt){
        throw new ItemException("This item function is unavailable right now, wait until version 1.4.0 when items can be attached to adoptables");
    }
    
    protected function applyAlts1(OwnedItem $item, OwnedAdoptable $adopt){
        $alid = $item->getValue();
        $alternate = new AdoptAlternate($alid);
        if($adopt->getID() != $alternate->getAdopt() || $adopt->getCurrentLevel() < $alternate->getLevel()){
            throw new ItemException("The item can only be used for adoptable {$alternate->getAdopt('model')->getType()} at level {$alternate->getLevel()} or above...<br>");
        }
        $adopt->setAlternate($alternate, "update");
        return "Your adoptable {$adopt->getName()} has gotten a new alternate form.";
    }
    
    protected function applyAlts2(OwnedItem $item, OwnedAdoptable $adopt){
        if($adopt->getID() != $item->getValue()){
            throw new ItemException("The item {$item->getItemname()} cannot be used for your adoptable since it is not designed for adoptable type {$adopt->getType()}...<br>");
        }
        $adopt->updateAlternate("update");
        return "Your adoptable {$adopt->getName()} has gotten a random alternate form.";
    }
    
    protected function applyName1(OwnedItem $item, OwnedAdoptable $adopt){
        throw new ItemException("Currently adoptables can change name freely, there is no need to use an item");
    }
    
    protected function applyName2(OwnedItem $item, Member $user){
        throw new ItemException("For now the items can only be used on adoptables, so user-based item usage will be implemented later.");
    }
    
    protected function applyRecipe(OwnedItem $item, OwnedAdoptable $adopt){
        throw new ItemException("The item {$item->getItemname()} is a recipe item, which cannot be used on any adoptable and can only be useful if you are performing alchemy.");
    }
    
    protected function save($field, $value) {
		$mysidia = Registry::get("mysidia");
		$mysidia->db->update("items_functions", [$field => $value], "ifid='{$this->id}'");      
    }
}