<?php

namespace View\AdminCP;

use Resource\Collection\LinkedList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextArea;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\TCell;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;
use Service\Builder\FormBuilder;
use Service\Builder\TableBuilder;
use Service\Helper\TableHelper;

class ThemeView extends View
{
    public function index()
    {
        parent::index();
        $document = $this->document;
        $helper = new TableHelper();
        $themeTable = new TableBuilder("themes");
        $themeTable->setAlign(new Align("center", "middle"));
        $themeTable->buildHeaders("ID", "Theme", "Folder", "Edit", "Delete");
        $themeTable->setHelper($helper);

        $themes = $this->getField("themes");
        $iterator = $themes->iterator();
        while ($iterator->hasNext()) {
            $theme = $iterator->next();
            $cells = new LinkedList();
            $cells->add(new TCell($theme->getID()));
            $cells->add(new TCell($theme->getThemename()));
            $cells->add(new TCell($theme->getThemeFolder()));
            $cells->add(new TCell($helper->getEditLink($theme->getID())));
            $cells->add(new TCell($helper->getDeleteLink($theme->getID())));
            $themeTable->buildRow($cells);
        }
        $document->add($themeTable);

        $pagination = $this->getField("pagination");
        $document->addLangvar($pagination->showPage());
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->added_title);
            $document->addLangvar($this->lang->added);
            return;
        }

        $document->setTitle($this->lang->add_title);
        $document->addLangvar($this->lang->add);
        $themeForm = new FormBuilder("addform", "add", "post");
        $themeForm->buildComment("Theme Name: ", false)->buildTextField("theme")
                  ->buildComment("Theme Folder: ", false)->buildTextField("folder")
                  ->buildComment("The theme folder will appear inside the template folder.")
                  ->buildComment("Header HTML: ")->buildTextArea("header", "", 8, 85)
                  ->buildComment("Body HTML: ")->buildTextArea("body", "", 8, 85)
                  ->buildComment("The Header and Body HTML will be used in the two generated files header.tpl and template.tpl")
                  ->buildComment("Style CSS: ")->buildTextArea("css", "", 8, 85)
                  ->buildComment("Display for Usergroup(Leave blank for all usergroups):")->buildDropdownList("usergroup", "UsergroupList")
                  ->buildComment("Display from(Leave blank to disable this option):")->buildTextField("fromdate")
                  ->buildComment("Display until(Leave blank to disable this option):")->buildTextField("todate")
                  ->buildComment("The Style css will be created as style.css file inside the appropriate theme folder.")
                  ->buildCheckBox("The style has been uploaded to the site, and is pending for installation", "install", "yes")
                  ->buildButton("Create/Install Theme", "submit", "submit");
        $document->add($themeForm);
    }

    public function edit()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $theme = $this->getField("theme");
        if (!$theme) {
            $this->index();
        } elseif ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->edited_title);
            $document->addLangvar($this->lang->edited);
        } else {
            $document->setTitle($this->lang->edit_title);
            $document->addLangvar($this->lang->edit);
            $themeForm = new FormBuilder("editform", $theme->getID(), "post");
            $themeForm->buildComment("Theme Name: ", false)->buildTextField("theme", $theme->getThemename())
                      ->buildComment("Theme Folder: ", false)->buildTextField("folder", $theme->getThemeFolder())
                      ->buildComment("The theme folder will appear inside the template folder.")
                      ->buildComment("Header HTML: ")->buildTextArea("header", $this->getField("header"), 8, 85)
                      ->buildComment("Body HTML: ")->buildTextArea("body", $this->getField("body"), 8, 85)
                      ->buildComment("Style CSS: ")->buildTextArea("css", $this->getField("css"), 8, 85)
                      ->buildComment("Display for Usergroup(Leave blank for all usergroups):")->buildDropdownList("usergroup", "UsergroupList", $theme->getUsergroup())
                      ->buildComment("Display from(Leave blank to disable this option):")->buildTextField("fromdate", $theme->getDateFrom("Y-m-d"))
                      ->buildComment("Display until(Leave blank to disable this option):")->buildTextField("todate", $theme->getDateTo("Y-m-d"))
                      ->buildButton("Update Theme", "submit", "submit");
            $document->add($themeForm);
        }
    }

    public function delete()
    {
        $document = $this->document;
        $theme = $this->getField("theme");
        if (!$theme) {
            $this->index();
        } else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }

    public function css()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->additional_title);
            $document->addLangvar($this->lang->additional);
            return;
        }

        $document->setTitle($this->lang->css_title);
        $document->addLangvar($this->lang->css);
        $cssForm = new Form("cssform", "css", "post");
        $cssTable = new TableBuilder("csstable");
        $cssTable->setAlign(new Align("center", "middle"));
        $cssTable->buildHeaders("Select", "File", "CSS");

        $cssIterator = $this->getField("cssMap")->iterator();
        while ($cssIterator->hasNext()) {
            $cssEntry = $cssIterator->next();
            $css = $cssEntry->getKey()->remove("{$mysidia->path->getRoot()}css/")->remove(".css")->getValue();
            $cells = new LinkedList();
            $cssButton = new RadioButton("", "file", $css);
            $cssContent = new TextArea($css, $cssEntry->getValue(), 6, 65);

            $cells->add(new TCell($cssButton));
            $cells->add(new TCell($css));
            $cells->add(new TCell($cssContent));
            $cssTable->buildRow($cells);
        }

        $notice = new Comment($this->lang->css_notice);
        $notice->setHeading(3);
        $cssForm->add($cssTable);
        $cssForm->add($notice);

        $cssForm->add(new CheckBox("Create a new additional css", "new", "yes"));
        $cssForm->add(new Comment("CSS file name: "));
        $cssForm->add(new TextField("newfile", "blank"));
        $cssForm->add(new Comment("CSS file content: "));
        $cssForm->add(new TextArea("newcontent", "Enter CSS Here", 8, 85));
        $cssForm->add(new Button("Add/Update Additional CSS", "submit", "submit"));
        $document->add($cssForm);
    }
}
