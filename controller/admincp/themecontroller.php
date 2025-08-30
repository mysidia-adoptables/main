<?php

namespace Controller\AdminCP;

use DirectoryIterator;
use Exception;
use SplFileObject;
use Model\DomainModel\Theme;
use Resource\Collection\ArrayList;
use Resource\Collection\LinkedHashMap;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\BlankFieldException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Native\MysString;

class ThemeController extends AppController
{
    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage themes.");
        }
    }

    public function index()
    {
        parent::index();
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("themes")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/theme", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("themes", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $themes = new ArrayList();
        while ($dto = $stmt->fetchObject()) {
            $themes->add(new Theme($dto->id, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("themes", $themes);
    }

    public function add()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            if (!$mysidia->input->post("theme") || !$mysidia->input->post("folder")) {
                throw new BlankFieldException("global_blank");
            }
            $mysidia->db->insert("themes", ["id" => null, "themename" => $mysidia->input->post("theme"), "themefolder" => $mysidia->input->post("folder"),
                                            "usergroup" => (int)$mysidia->input->post("usergroup"), "fromdate" => $mysidia->input->post("fromdate"), "todate" => $mysidia->input->post("todate")]);
            if ($mysidia->input->post("install") != "yes") {
                $theme = new Theme($mysidia->input->post("theme"));
                $theme->updateThemeText("header.tpl", $mysidia->input->rawPost("header"));
                $theme->updateThemeText("template.tpl", $mysidia->input->rawPost("body"));
                $theme->updateThemeText("style.css", $mysidia->input->rawPost("css"));
            }
        }
    }

    public function edit($tid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$tid) {
            return $this->index();
        }
        $theme = new Theme($tid);
        if ($mysidia->input->post("submit")) {
            $mysidia->db->update("themes", ["themename" => $mysidia->input->post("theme"), "themefolder" => $mysidia->input->post("folder"), "usergroup" => (int)$mysidia->input->post("usergroup"),
                                            "fromdate" => $mysidia->input->post("fromdate"), "todate" => $mysidia->input->post("todate")], "id = '{$theme->getID()}'");
            $theme->updateThemeText("header.tpl", $mysidia->input->rawPost("header"), $mysidia->input->post("folder"));
            $theme->updateThemeText("template.tpl", $mysidia->input->rawPost("body"), $mysidia->input->post("folder"));
            $theme->updateThemeText("style.css", $mysidia->input->rawPost("css"), $mysidia->input->post("folder"));
        } else {
            $this->setField("header", new MysString($theme->getThemeText("header.tpl")));
            $this->setField("body", new MysString($theme->getThemeText("template.tpl")));
            $this->setField("css", new MysString($theme->getThemeText("style.css")));
        }
        $this->setField("theme", $tid ? $theme : null);
    }

    public function delete($tid = null)
    {
        $mysidia = Registry::get("mysidia");
        if (!$tid) {
            $this->index();
        } else {
            try {
                $theme = new Theme($tid);
                $mysidia->db->delete("themes", "id = '{$theme->getID()}'");
            } catch (Exception) {
                throw new InvalidIDException("global_id");
            }
        }
        $this->setField("theme", $tid ? $theme : null);
    }

    public function css()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $file = ($mysidia->input->post("new") == "yes") ? $mysidia->input->post("newfile") : $mysidia->input->post("file");
            $content = ($mysidia->input->post("new") == "yes") ? $mysidia->input->rawPost("newcontent") : $mysidia->input->rawPost($file);
            if (!$file) {
                throw new BlankFieldException("global_blank");
            }
            $css = new SplFileObject("{$mysidia->path->getRoot()}css/{$file}.css", "w");
            $css->fwrite($mysidia->format($content, false));
            $css->fflush();
            return;
        }

        $cssMap = new LinkedHashMap();
        $directory = new DirectoryIterator("{$mysidia->path->getRoot()}css");
        while ($directory->valid()) {
            if ($directory->getExtension() == "css") {
                $key = $directory->getPathname();
                $value = "";
                $css = new SplFileObject($key);
                while (!$css->eof()) {
                    $value .= $css->fgets();
                }
                $cssMap->put(new MysString($key), new MysString($value));
            }
            $directory->next();
        }
        $this->setField("cssMap", $cssMap);
    }
}
