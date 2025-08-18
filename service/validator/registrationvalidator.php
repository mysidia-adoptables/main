<?php

namespace Service\Validator;
use ArrayObject, Exception;
use Resource\Core\Registry;
use Resource\Core\Validator;
use Resource\Utility\Date;
use Service\ApplicationService\RegistrationException;

class RegistrationValidator extends Validator{ 
    
    private $form;
    
    public function __construct(ArrayObject $form, ArrayObject $validations){
        parent::__construct($validations);
        $this->form = $form;
    }
    
    protected function checkUsername(){
        $username = $this->form["username"];
        if(!$this->emptyValidate($username)) throw new RegistrationException("username_empty");
        if(in_array(strtolower($username), ["admin", "system"])){ 
            throw new RegistrationException("username_filter");
        }
        if(is_numeric($username[0]) || ($username != ltrim($username))) throw new RegistrationException("username_invalid");
        $userExists = $this->datavalidate("users", ["username"], "username = :username", ["username" => $username]);
        if($userExists) throw new RegistrationException("username_exists");
    } 
    
    protected function checkPassword(){ 
        $password = $this->form["password"];
        if(!$this->emptyValidate($password)) throw new RegistrationException("password_empty");
        if(strlen($password) < 6) throw new RegistrationException("password_length");
    }
    
    protected function checkPassword2(){ 
        $password = $this->form["password"];
        $password2 = $this->form["password2"];
        if(!$this->emptyValidate($password2)) throw new RegistrationException("password_confirm");
        if(!$this->matchValidate($password, $password2)){ 
            throw new RegistrationException("password_match");
        }
    }
    
    protected function checkEmail(){ 
        $email = $this->form["email"];
        if(!$this->emptyValidate($email)) throw new RegistrationException("email_empty");
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RegistrationException("email_invalid");
    }
    
    protected function checkBirthday(){ 
        $birthday = $this->form["birthday"];
        if(!$this->emptyValidate($birthday)) throw new RegistrationException("birthday_empty");
        try{ 
            $date = new Date($birthday);
            $date->format("Y-m-d");
        }
        catch(Exception $e){ 
            throw new RegistrationException("birthday_invalid");
        }
    }
    
    protected function checkIp(){ 
        if(!$this->matchValidate($this->form["ip"], $_SERVER['REMOTE_ADDR'])){ 
            throw new RegistrationException("ip");
        }
    }
    
    protected function checkAnswer(){
        $mysidia = Registry::get("mysidia");
        if(!$this->matchvalidate($this->form["answer"], $mysidia->settings->securityanswer)){ 
            throw new RegistrationException("answer");            
        }       
    }
    
    protected function checkTos(){ 
        if(!$this->matchvalidate($this->form["tos"], "yes")){
            throw new RegistrationException("tos"); 
        }
    }
    
    protected function checkAvatar(){
        if(!$this->emptyValidate($this->form["avatar"])) throw new RegistrationException("avatar");  
    }
    
    protected function checkBio(){
        $bio = $this->form["bio"];
        if($bio && strlen($bio) > 500) throw new RegistrationException("bio");  
    }
    
    protected function checkColor(){ 
        $color = $this->form["color"];
        if($color && strlen($color) > 20) throw new RegistrationException("color");  
    }
    
    protected function checkGender(){ 
        $gender = $this->form["gender"];
        if($gender && !in_array($gender, ["male", "female", "unknown"])){
            throw new RegistrationException("gender");
        }
    }
    
    protected function checkNickname(){ 
        $nickname = $this->form["nickname"];
        if($nickname && strlen($nickname) > 40) throw new RegistrationException("nickname");  
    }
}