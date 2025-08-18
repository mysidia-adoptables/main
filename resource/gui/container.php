<?php

namespace Resource\GUI;
use ArrayObject;
use Resource\Collection\ArrayList;
use Resource\Collection\LinkedHashMap;
use Resource\Collection\Listable;
use Resource\GUI\Element;
use Resource\Native\MysString;

/**
 * The Abstract Container Class, extends from abstract GUI Component class.
 * It is parent to all Mysidia GUI Containers types, but cannot be instantiated itself.
 * @category Resource
 * @package GUI
 * @author Hall of Famer 
 * @copyright Mysidia Inc
 * @link http://www.mysidiainc.com
 * @since 1.3.3
 * @todo Restructure the namespace
 * @abstract
 *
 */
 
abstract class Container extends Component{
	
	/**
	 * The components property, which stores references of all components inside this container.
	 * @access protected
	 * @var ArrayList
     */
	protected $components;
	
	/**
	 * The currentIndex property, which specifies the current index of components.
	 * @access protected
	 * @var int
     */
	protected $currentIndex = 1;
	
	/**
	 * The margin property, which specifies the margin settings for this container.
	 * @access protected
	 * @var Margin
     */
	protected $margin;
	
	/**
	 * The padding property, which defines the padding settings for this container.
	 * @access protected
	 * @var Padding
     */
	protected $padding;
	
	/**
	 * The borders property, which determines the border settings for this container.
	 * @access protected
	 * @var Borders
     */
	protected $borders;
	
	/**
	 * The thematicBreak property, which defines if a thematicbreak is automatically inserted between each component.
	 * @access protected
	 * @var Boolean
     */
	protected $thematicBreak = FALSE;
	
	/**
	 * The links property, stores an array of links of the components inside this container.
	 * @access protected
	 * @var ArrayList
    */
	protected $links;
	
	/**
	 * The images property, stores an array of images of the components inside this container.
	 * @access protected
	 * @var ArrayList
     */
	protected $images;
	
	/**
	 * The forms property, stores an array of forms inside this container.
	 * @access protected
	 * @var ArrayList
     */
	protected $forms;
	
	/**
	 * The tables property, stores an array of tables inside this container.
	 * @access protected
	 * @var ArrayList
     */
	protected $tables;
	
	/**
     * Constructor of GUIContainer Class, which can accept container and components argument.    
	 * @param ArrayObject|ArrayList|GUIComponent  $components
     * @access public
     * @return void
     */
	public function __construct($components = ""){
	    $this->components = new ArrayList;
        if($components instanceof ArrayObject){
            foreach($components as $component){
                $this->components->add($component);
            }
            $this->currentIndex += $components->count();
        }
        elseif($components instanceof Listable){
 		    $this->components = $components;
			$this->currentIndex += $this->components->size();
        }
        elseif($components instanceof Component){
		    $this->components->add($components);
			$components->setContainer($this);
        }	
	}

	/**
     * The Components method, getter method for property $components.
     * @access public
     * @return ArrayList
     */
	public function components(){
	    return $this->components;
	}
	
	/**
     * The getMargin method, getter method for property $margin.    
     * @access public
     * @return Margin
     */
	public function getMargin(){
	    return $this->margin;
	}
	
	/**
     * The setMargin method, setter method for property $margin.
	 * @param Margin  $margin       
     * @access public
     * @return void
     */
	public function setMargin(Element\Margin $margin){
	    $this->margin = $margin;
		$this->setCSS("Margin");
	}
	
	/**
     * The getPadding method, getter method for property $padding.    
     * @access public
     * @return Padding
     */
	public function getPadding(){
	    return $this->padding;
	}
	
	/**
     * The setPadding method, setter method for property $padding.
	 * @param Padding  $padding       
     * @access public
     * @return void
     */
	public function setPadding(Element\Padding $padding){
	    $this->padding = $padding;
		$this->setCSS("Padding");
	}
	
	/**
     * The getBorders method, getter method for property $borders.    
     * @access public
     * @return Borders
     */
	public function getBorders(){
	    return $this->borders;
	}
	
	/**
     * The setBorders method, setter method for property $borders.
	 * @param Borders  $borders      
     * @access public
     * @return void
     */
	public function setBorders(Element\Borders $borders){
	    $this->borders = $borders;
		$this->setCSS("Borders");
	}
	
	/**
     * The addLineBreak method, add a linebreak between component rendering in runtime.
     * @access public
     * @return Void
     */
	public function addLineBreak(){
		$this->renderer->renderLineBreak();
	}
	
	/**
     * The isThematicBreak method, getter method for property $thematicBreak.    
     * @access public
     * @return Boolean
     */	
	public function isThematicBreak(){
	    return $this->thematicBreak;
	}
	
	/**
     * The setThematicBreak method, setter method for property $thematicBreak.
	 * @param Boolean  $thematicBreak 
     * @access public
     * @return Void
     */
	public function setThematicBreak($thematicBreak){
		$this->thematicBreak = $thematicBreak;
	}
	
	/**
     * The getComponent method, retrieves a Component or Container at a specific index.
     * @param int  $index	 
     * @access public
     * @return Component
     */
	public function getComponent($index){
	    $component = $this->components->get($index);
	    if($component == NULL) throw new GUIException("There is no GUI Object at this specified index.");
	    return $component;
	}

	/**
     * The getComponents method, obtains an array of GUIComponents of a certain type.
	 * @param String $guiComponent
     * @access public
     * @return LinkedHashMap
     */	
	public function getComponents($guiComponent){
	    if($this->components->size() == 0) throw new GUIException("This container has no components yet...");
   	    $components = new LinkedHashMap;
		$iterator = $this->components->iterator();
		while($iterator->hasNext()){
		    $component = $iterator->next();
		    if($component instanceof $guiComponent) $components->put(new MysString($component->getID()), $component);
		}
		return $components;
	}	
	
	/**
     * The getComponentssByID method, obtains a GUI component by its ID.
	 * @param String  $id 
     * @access public
     * @return Component|Boolean 
     */		
	public function getComponentsByID($id){
	    if($this->components->size() == 0) throw new GUIException("This container has no components yet...");
	    $iterator = $this->components->iterator();
		while($iterator->hasNext()){
		    $component = $iterator->next();
		    if($component->getID() == $id) return $component;    
		}
		return FALSE;
	}
	
	/**
     * The getComponentsByName method, obtains a GUI component by its Name.
	 * @param String  $name
     * @access public
     * @return Component|Boolean 
     */	
	public function getComponentsByName($name){
	    if($this->components->size() == 0) throw new GUIException("This container has no components yet...");
	    $iterator = $this->components->iterator();
		while($iterator->hasNext()){
		    $component = $iterator->next();
		    if($component->getName() == $name) return $component;  
		}
		return FALSE;	
	}
	
		/**
     * The getLink method, obtains all link objects in this container.
     * @access public
     * @return LinkedHashMap
     */		
	public function getLinks(){
	    if(!$this->links){
		    $this->links = new LinkedHashMap;
			if($this->components->size() == 0) return $this->links;
		    $this->getComponentsByType("Link");
        }
		return $this->links;
	}
	
	/**
     * The getImages method, obtains all image objects in this container.
     * @access public
     * @return LinkedHashMap
     */	
	public function getImages(){
	    if(!$this->images){
		    $this->images = new LinkedHashMap;
			if($this->components->size() == 0) return $this->images;
		    $this->getComponentsByType("Image");
        }
		return $this->images;
	}
	
	/**
     * The getForms method, obtains an array of Form objects in this container.
     * @access public
     * @return LinkedHashMap
     */	
	public function getForms(){
	    if(!$this->forms){
		    $this->forms = new LinkedHashMap;
			if($this->components->size() == 0) return $this->forms;
		    $this->getComponentsByType("Form");
		}
		return $this->forms;
	}
	
	/**
     * The getTables method, obtains an array of Table objects in this container.
     * @access public
     * @return LinkedHashMap
     */	
	public function getTables(){
	    if(!$this->tables){
		    $this->tables = new LinkedHashMap;
			if($this->components->size() == 0) return $this->tables;
		    $this->getComponentsByType("Table");
		}
		return $this->tables;
	}

	/**
     * The getComponentsByType method, obtains an array of objects of certain type;
     * This is a helper method used for retrieving links, images, forms and tables.
     * @access protected
     * @return Void
     */
    protected function getComponentsByType($type){
        $property = strtolower("{$type}s");
        $method = "get{$type}s";
		$iterator = $this->components->iterator();
		while($iterator->hasNext()){
		    $component = $iterator->next();
			if($component instanceof $type) $this->$property->put(new MysString($component->getID()), $component);
			if($component instanceof Container){
                $this->$property->putAll($component->$method());
            }			
		}
    }

	/**
     * The contains method, checks if a GUI object is already available in the container.
	 * @param Component  $component 
     * @access public
     * @return Boolean
     */	
	public function contains(Component $component){
	    return $this->components->contains($component);
	}
	
	/**
     * The add method, sets a Component or Container to a specific index.
	 * @param Component  $component
     * @param int  $index	 
     * @access public
     * @return void
     */	
	public function add(Component $component, $index = -1){
	    if($index == -1){
		    $this->components->add($component);
			$this->currentIndex++;
		}
        else{
		    if($index > $this->components->size()) $this->components->ensureCapacity($index);
		    $this->components->insert($index, $component);
			$this->currentIndex = $index + 1;
        }
        $component->setContainer($this);		
	}
	
	/**
     * The remove method, deletes a Component or Container at current or a specific index.
     * @param int  $index	 
     * @access public
     * @return void
     */	
	public function remove($index = -1){	    
	    if($index == -1){
		    $this->currentIndex--;
		    $this->components->delete($this->currentIndex);
		}
		else{
		    $this->components->delete($index);
		}
	}
	
	/**
     * The clear method, completely erases all components and containers.	 
     * @access public
     * @return void
     */	
	public function clear(){
	    $this->components->clear();
	}
		
	/**
     * The method render for Container class, it validates components and sorts components by indexes.
     * @access public
     * @return String
     */
	public function render(){
	    if($this->renderer->getStatus() == "ready"){
		    $this->renderer->start();
			parent::render()->pause();
            $this->renderer->renderComponents();
            $this->renderer->end(); 			
		}	
		return $this->renderer->getRender();
	}
	
	/**
     * Magic method __toString for Container class, it reveals that the class is a container type Container.
     * @access public
     * @return String
     */
    public function __toString(){
	    return "This is The GUIContainer class.";
	}
}