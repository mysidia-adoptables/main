<?php

namespace Resource\Core;
use ArrayObject;
use Resource\Core\Registry;
use Resource\Native\MysObject;

abstract class Validator extends MysObject implements Validative{

    protected $validations;
    protected $action;
    protected $value;
    protected $data;
    protected $error = "";
    protected $status;
    
    public function __construct(ArrayObject $validations, $action = NULL, $value = NULL, $error = NULL){
        $this->validations = $validations;
        $this->initialize($action, $value, $error);
    }

    public function initialize($action = NULL, $value = NULL, $error = NULL){
	    if ($action !== NULL) $this->setAction($action);
        if ($value !== NULL) $this->setValue($value);
        if ($error !== NULL) $this->setError($error);
    }

    public function getValidations(){
	    return $this->validations;
	}
	
	public function setValidations(ArrayObject $validations, $overwrite = FALSE){
	    if($overwrite) $this->validations = $validations;
		else{
		    foreach($validations as $validation){
			    $this->validations->append($validation);
			}
		}
	}
    
    public function setAction($action){
        $this->action = $action;
    }
   
    public function getAction(){
        return $this->action;
    }
  
    public function setValue($value){
        $this->value = $value;
    }
   
    public function getValue(){
        return $this->value;
    }  
     
    public function setError($error, $overwrite = FALSE){
        $br = "<br>";
        if (!is_string($error) or empty($error)) throw new Exception('The error message is invalid. It must be a non-empty string.');
        elseif($overwrite == TRUE) $this->error = $error;
	    else $this->error .= $error . $br;
    }
   
    public function triggerError(){
        if(empty($this->error)) return FALSE;
	    else return $this->error;
    }
  
    public function resetError(){
        $this->error = "";
    }
    
    public function getStatus(){
	    return $this->status;
	}
    
    public function setStatus($status = ""){ 
        $this->status = $status;
    }
    
    public function validate(){
		foreach($this->validations as $validation){
			$method = "check" . ucfirst($validation);
		    $this->$method();
		}
		return TRUE;        
    }
    
    public function emptyValidate($field){
	    if(empty($field)) return FALSE;
	    else return TRUE;
    }
  
    public function numericValidate($field){
	    if(!is_numeric($field)) return FALSE;
	    else return TRUE;
    }
  
    public function dataValidate($table, $fields, $whereclause, $values = []){
        $mysidia = Registry::get("mysidia");
	    $data = $mysidia->db->select($table, $fields, $whereclause, $values)->fetchObject();
	    if(!is_object($data)) return FALSE;
	    else{
            $this->data = $data;        
            return TRUE;
        }
    }
  
    public function matchValidate($var1, $var2, $approach = ""){
        switch($approach){
	        case "preg_match":
		    return preg_match($var1, $var2);
	    default:
	        if($var1 == $var2) return TRUE;
		    else return FALSE;
	    }
        // End of the switch statement	  
    }
} 