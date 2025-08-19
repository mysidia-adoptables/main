<?php

namespace Model\ViewModel;

use Throwable;
use Model\DomainModel\Module;
use Model\DomainModel\Widget;
use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Core\ViewModel;
use Resource\GUI\Component;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Document\Paragraph;
use Resource\GUI\Renderable;

class WidgetViewModel extends ViewModel implements Renderable
{

    /**
     * The division property, holds an division object of the entired rendered menu.
     * @access protected
     * @var Division
     */
    protected $division;

    /**
     * The modules property, it stores an array of modules used in the placeholder.
     * @access protected
     * @var ArrayList
     */
    protected $modules;


    public function __construct(Widget $widget)
    {
        parent::__construct($widget);
        $mysidia = Registry::get("mysidia");
        $userLevel = $mysidia->user->isLoggedIn() ? "member" : "visitor";
        $stmt = $mysidia->db->select("modules", [], "widget = '{$this->getID()}' AND (userlevel = '{$userLevel}' OR userlevel = 'user') AND status = 'enabled' ORDER BY `order`");
        while ($data = $stmt->fetchObject()) {
            $module = new Module($data->moid, $data);
            $method = "set{$module->getName()}";
            if ($this->hasMethod($method)) $this->$method();
            else $this->loadModule($module);
        }
    }

    /**
     * The getName method, acquires the name of the widget model.
     * @access public
     * @return String
     */
    public function getName()
    {
        return $this->model->getName();
    }

    /**
     * The getModules method, getter method for property $modules.
     * @access public
     * @return ArrayList
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * The loadModules method, autoload modules from database to the Widget ViewModel.
     * @param Module $module
     * @access public
     * @return void
     */
    public function loadModule(Module $module)
    {
        $moduleContainer = new Paragraph;
        if ($module->getSubtitle()) $moduleContainer->add(new Comment($module->getSubtitle(), true, "b"));
        if ($module->getPHP()) {
            if (PHP_MAJOR_VERSION >= 7) {
                try {
                    eval($module->getPHP());
                } catch (Throwable $te) {
                    echo "There appears to be an error in the PHP code of your module: {$this->getName()}. \n The error message is: {$te->getMessage()}.";
                }
            }
        }
        if ($module->getHTML()) $moduleContainer->add(new Comment($module->getHTML()));
        $this->setDivision($moduleContainer);
    }

    /**
     * The getDivision method, getter method for property $division.
     * @access public
     * @return Division
     */
    public function getDivision()
    {
        return $this->division;
    }

    /**
     * The setDivision method, setter method for property $division.
     * It is set internally upon object instantiation, cannot be accessed in client code.
     * @param Component $module
     * @access protected
     * @return void
     */
    protected function setDivision(Component $module)
    {
        if (!$this->division) {
            $this->division = new Division;
            $this->division->setClass($this->getName());
        }
        $this->division->add($module);
    }

    /**
     * The clear method, erase all modules inside this widget.
     * @access public
     * @return void
     */
    public function clear()
    {
        $this->modules = new ArrayList;
    }

    /**
     * The render method for Widget class, it loops through its modules and render them all.
     * @access public
     * @return Renderer
     */
    public function render()
    {
        if (!$this->division) return;
        return $this->division->render();
    }
}
