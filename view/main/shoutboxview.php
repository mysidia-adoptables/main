<?php

namespace View\Main;

use Resource\Core\Mysidia;
use Resource\Core\Registry;
use Resource\Core\View;
use Resource\GUI\Component\Button;
use Resource\GUI\Container\Form;
use Resource\GUI\Document\Comment;
use Resource\GUI\Document\Division;
use Resource\GUI\Element\Color;
use Resource\GUI\Element\Dimension;
use Resource\GUI\Element\Margin;
use Resource\GUI\Element\Padding;
use Service\Builder\CKEditorBuilder;

class ShoutboxView extends View
{

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $document = $this->document;
        $document->setTitle($this->lang->title);
        $document->addLangvar($this->lang->default);
        if ($mysidia->input->post("comment")) {
            $document->addLangvar($this->lang->complete);
            $this->refresh(5);
            return;
        }

        $messages = $this->getField("messages");
        if ($messages->isEmpty()) $document->add(new Comment("Currently no one has ever here yet, wanna be the first to shout a message?"));
        else {
            $shoutboxes = new Division;
            $shoutboxes->setClass("enclosecomments");
            $iterator = $messages->iterator();
            while ($iterator->hasNext()) {
                $message = $iterator->next();
                $comment = new Division;
                $comment->setClass("comment");
                $comment->setDimension(new Dimension("94%"));
                $comment->setPadding(new Padding("", "2%"));
                $comment->setBackground(new Color("aliceblue"));
                $comment->setMargin(new Margin("bottom", "5px"));

                $userdate = new Division;
                $userdate->setClass("userdate");
                $userdate->setDimension(new Dimension("50%", "25%"));
                $userdate->setForeground(new Color("red"));
                $userdate->add(new Comment("{$message->getUsername()} - {$message->getDate('Y-m-d H:i:s')}"));
                $comment->add($userdate);
                $comment->add(new Comment($message->getComment(), false));
                $shoutboxes->add($comment);
            }
            $document->add($shoutboxes);
        }

        $editor = new CKEditorBuilder("basic");
        $shoutboxForm = new Form("shoutbox", "shoutbox", "post");
        $shoutboxForm->add(new Comment($this->lang->notice, false));
        $shoutboxForm->add($editor->buildEditor("comment", "CKEditor for Mysidia " . Mysidia::version));
        $shoutboxForm->add(new Button("Shout It!", "submit", "submit"));
        $document->add($shoutboxForm);
    }
}
