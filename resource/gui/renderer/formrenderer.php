<?php

namespace Resource\GUI\Renderer;
use Resource\GUI\Container\Form;
use Resource\GUI\Renderer;

/**
 * The FormRenderer Class, extends from abstract GUIRenderer class.
 * It is responsible for rendering GUI Forms.
 * @category Resource
 * @package GUI
 * @subpackage Renderer
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 *
 */  

class FormRenderer extends Renderer{
	
	/**
     * Constructor of FormRenderer Class, assigns the form reference and determines its tag.
     * @param Form  $form
     * @access public
     * @return Void
     */
	public function __construct(Form $form){
        parent::__construct($form);	
		$this->tag = "form";
    }
	
	/**
     * The renderAction method, renders the action property of a Form Object.    
     * @access public
     * @return FormRenderer
     */	
	public function renderAction(){
	    $this->setRender(" action='{$this->component->getAction()}'");
        return $this;		
	}
	
	/**
     * The renderMethod method, renders the method property of a Form Object.    
     * @access public
     * @return FormRenderer
     */	
	public function renderMethod(){
	    $this->setRender(" method='{$this->component->getMethod()}'");
        return $this;		
	}

	/**
     * The renderEnctype method, renders the enctype property of a form Object.    
     * @access public
     * @return FormRenderer
     */	
	public function renderEnctype(){
        $this->setRender(" enctype='{$this->component->getEnctype()}'");
        return $this;		
	}
	
	/**
     * The renderAccept method, renders the accept property of a form Object.    
     * @access public
     * @return FormRenderer
     */	
	public function renderAccept(){
	    $this->setRender(" accept-charset='{$this->component->getAccept()}'");
        return $this;		
	}
}  