<?php

namespace Resource\Core;

use Resource\Native\MysObject;
use Resource\Native\MysString;

class Pagination extends MysObject
{

    private $website;
    private $page;
    private $symbol;

    public function __construct(private $totalrows, private $rowsperpage, $website, $page = 1)
    {
        $path = Registry::get("path");
        $frame = Registry::get("frame");
        if ($page instanceof MysString) $page = (string)$page;
        $frame->getHeader()->addStyle("{$path->getTempRoot()}css/pagination.css");
        $this->website = $path->getAbsolute() . $website;
        $this->setPage($page);
    }

    public function setPage($page)
    {
        $this->page = $page ? (int)$page : 1;
    }

    public function getLimit()
    {
        return ($this->page - 1) * $this->rowsperpage;
    }

    public function getRowsperPage()
    {
        return $this->rowsperpage;
    }

    public function getTotalRows()
    {
        return $this->totalrows;
    }

    public function getLastPage()
    {
        return ceil($this->totalrows / $this->rowsperpage);
    }

    public function showPage($margin = null, $padding = null)
    {
        $this->getTotalRows();
        $pagination = "";
        $lpm1 = $this->getLastPage() - 1;
        $page = $this->page;
        $prev = $this->page - 1;
        $next = $this->page + 1;
        $this->symbol = "/";

        $pagination .= "<br><br><div class='pagination'";
        if ($margin || $padding) {
            $pagination .= " style='";
            if ($margin) $pagination .= "margin: $margin;";
            if ($padding) $pagination .= "padding: $padding;";
            $pagination .= "'";
        }
        $pagination .= ">";

        if ($this->getLastPage() > 1) {
            if ($page > 1) $pagination .= "<a href='{$this->website}{$this->symbol}page-{$prev}'><< prev</a>";
            else $pagination .= "<span class='disabled'><< prev</span>";


            if ($this->getLastPage() < 9) {
                for ($counter = 1; $counter <= $this->getLastPage(); $counter++) {
                    if ($counter == $page) $pagination .= "<span class='current'>{$counter}</span>";
                    else $pagination .= "<a href='{$this->website}{$this->symbol}page-{$counter}'>{$counter}</a>";
                }
            } elseif ($this->getLastPage() >= 9) {
                if ($page < 4) {
                    for ($counter = 1; $counter < 6; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>{$counter}</span>";
                        else
                            $pagination .= "<a href='{$this->website}{$this->symbol}page-{$counter}'>{$counter}</a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href=$this->website{$this->symbol}page-$lpm1>{$lpm1}</a>";
                    $pagination .= "<a href=$this->website{$this->symbol}page-{$this->getLastPage()}>{$this->getLastPage()}</a>";
                } elseif ($this->getLastPage() - 3 > $page && $page > 1) {
                    $pagination .= "<a href=$this->website{$this->symbol}page-1>1</a>";
                    $pagination .= "<a href=$this->website{$this->symbol}page-2>2</a>";
                    $pagination .= "...";
                    for ($counter = $page - 1; $counter <= $page + 1; $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>{$counter}</span>";
                        else
                            $pagination .= "<a href='{$this->website}{$this->symbol}page-{$counter}'>{$counter}</a>";
                    }
                    $pagination .= "...";
                    $pagination .= "<a href='{$this->website}{$this->symbol}page-{$lpm1}'>$lpm1</a>";
                    $pagination .= "<a href='{$this->website}{$this->symbol}page-{$this->getLastPage()}'>{$this->getLastPage()}</a>";
                } else {
                    $pagination .= "<a href='{$this->website}{$this->symbol}page-1'>1</a>";
                    $pagination .= "<a href='{$this->website}{$this->symbol}page-2'>2</a>";
                    $pagination .= "...";
                    for ($counter = $this->getLastPage() - 4; $counter <= $this->getLastPage(); $counter++) {
                        if ($counter == $page)
                            $pagination .= "<span class='current'>{$counter}</span>";
                        else
                            $pagination .= "<a href='{$this->website}{$this->symbol}page-{$counter}'>{$counter}</a>";
                    }
                }
            }

            if ($page < $counter - 1)
                $pagination .= "<a href='{$this->website}{$this->symbol}page-{$next}'>next >></a>";
            else
                $pagination .= "<span class='disabled'>next >></span>";
            $pagination .= "</div>\n";
        }

        return $pagination;
    }
}
