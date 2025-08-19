<?php

namespace View\AdminCP;

use Resource\Collection\ArrayList;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Component\CheckBox;
use Resource\GUI\Component\FileField;
use Resource\GUI\Component\Image;
use Resource\GUI\Component\RadioButton;
use Resource\GUI\Component\TextField;
use Resource\GUI\Container\Form;
use Resource\GUI\Container\Table;
use Resource\GUI\Container\TCell;
use Resource\GUI\Container\TRow;
use Resource\GUI\Document\Comment;
use Resource\GUI\Element\Align;

class ImageView extends View
{

    public function index()
    {
        $document = $this->document;
        $document->setTitle($this->lang->manage_title);
        $document->addLangvar($this->lang->manage);

        $align = new Align("center", "center");
        $imageForm = new Form("manageform", "delete", "post");
        $imageForm->setAlign($align);
        $files = $this->getField("files");
        $imagesTable = new Table("images", "", false);
        $total = $files->size();
        $numColumns = 3;
        $index = 0;

        for ($row = 0; $row < ceil($total / $numColumns); $row++) {
            $imageRow = new TRow("row{$row}");
            for ($column = 0; $column < $numColumns; $column++) {
                $file = $files->get($index);
                $fileImage = new Image($file->getWWWPath());
                $fileImage->setLineBreak(true);
                $action = new RadioButton("Delete this Image", "iid", $file->getID());
                $action->setLineBreak(true);
                $cell = new ArrayList;
                $cell->add($fileImage);
                $cell->add($action);
                $imageRow->add(new TCell($cell, "cell{$index}"));
                $index++;
                if ($index == $total) break;
            }
            $imagesTable->add($imageRow);
        }

        $imageForm->add($imagesTable);
        $imageForm->add(new Button("Submit", "submit", "submit"));
        $document->add($imageForm);
        $pagination = $this->getField("pagination");
        if ($pagination) $document->addLangvar($pagination->showPage());
    }

    public function upload()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;

        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->uploaded_title);
            $document->addLangvar($this->lang->uploaded);
            $document->addLangvar($this->lang->next);
            return;
        }

        $document->setTitle($this->lang->upload_title);
        $document->addLangvar($this->lang->upload);
        $imageForm = new Form("uploadform", "upload", "post");
        $imageForm->setEnctype("multipart/form-data");
        $imageForm->add(new Comment("Friendly Name: ", false));
        $imageForm->add(new TextField("ffn"));
        $imageForm->add(new Comment($this->lang->explain));
        $imageForm->add(new Comment("File to Upload: ", false));
        $imageForm->add(new FileField("uploadedfile"));
        $imageForm->add(new Comment("<br><br>"));
        $imageForm->add(new Button("Upload File", "submit", "submit"));
        $document->add($imageForm);
    }

    public function delete()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if (!$mysidia->input->post("iid")) $this->index();
        else {
            $document->setTitle($this->lang->delete_title);
            $document->addLangvar($this->lang->delete);
        }
    }

    public function settings()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        if ($mysidia->input->post("submit")) {
            $document->setTitle($this->lang->settings_updated_title);
            $document->addLangvar($this->lang->settings_updated);
            return;
        }

        $document->setTitle($this->lang->settings_title);
        $document->addLangvar($this->lang->settings);
        $settingsForm = new Form("settingsform", "settings", "post");
        $settingsForm->add(new CheckBox(" Enable GD Signature Image for GIF Files", "enablegd", "yes", $mysidia->settings->gdimages));
        $settingsForm->add(new Comment($this->lang->gd_explain));
        $settingsForm->add(new CheckBox(" Enable Alternate Friendly Signature BBCode", "altbb", "yes", $mysidia->settings->usealtbbcode));
        $settingsForm->add(new Comment($this->lang->altbb_explain));
        $settingsForm->add(new Button("Change Settings", "submit", "submit"));
        $document->add($settingsForm);
    }
}
