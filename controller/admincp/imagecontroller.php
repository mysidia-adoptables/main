<?php

namespace Controller\AdminCP;

use Exception;
use Model\DomainModel\UploadedFile;
use Resource\Collection\ArrayList;
use Resource\Core\AppController;
use Resource\Core\Pagination;
use Resource\Core\Registry;
use Resource\Exception\DuplicateIDException;
use Resource\Exception\InvalidActionException;
use Resource\Exception\InvalidIDException;
use Resource\Exception\NoPermissionException;
use Resource\Exception\UnsupportedFileException;
use Resource\Native\MysString;
use Resource\Utility\Date;

class ImageController extends AppController
{

    public function __construct()
    {
        parent::__construct();
        $mysidia = Registry::get("mysidia");
        if ($mysidia->usergroup->getpermission("canmanagesettings") != "yes") {
            throw new NoPermissionException("You do not have permission to manage promocode.");
        }
    }

    public function index()
    {
        $mysidia = Registry::get("mysidia");
        $total = $mysidia->db->select("filesmap")->rowCount();
        $pagination = new Pagination($total, $mysidia->settings->pagination, "admincp/image", $mysidia->input->get("page"));
        $stmt = $mysidia->db->select("filesmap", [], "1 LIMIT {$pagination->getLimit()},{$pagination->getRowsperPage()}");
        $files = new ArrayList;
        while ($dto = $stmt->fetchObject()) {
            $files->add(new UploadedFile($dto->id, $dto));
        }
        $this->setField("pagination", $pagination);
        $this->setField("files", $files);
    }

    public function upload()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $filename = htmlentities((string) $_FILES['uploadedfile']['name']);
            $filesize = htmlentities((string) $_FILES['uploadedfile']['size']);
            $mimetype = htmlentities((string) $_FILES['uploadedfile']['type']);

            $allowedExts = ["gif", "jpg", "png"];
            $date = new Date;
            $hashstring = PREFIX . "{$filename}_{$date->format('Y-m-d')}";
            $fileinfo = pathinfo($filename);
            if (!isset($fileinfo["extension"])) throw new UnsupportedFileException("extension");
            $filetype = $fileinfo["extension"];
            if (!in_array($filetype, $allowedExts)) throw new UnsupportedFileException("extension");
            $hashedfilename = md5($hashstring) . ".{$filetype}";
            $uploaddir = "picuploads/{$filetype}";

            if (empty($hashedfilename)) throw new InvalidIDException("file_notexist");
            $existname = "{$uploaddir}/{$hashedfilename}";
            if (file_exists($existname)) throw new DuplicateIDException("file_exist");
            if ($filesize > 156000) throw new UnsupportedFileException("file_size");
            if ($mimetype != "image/gif" && $mimetype != "image/jpeg" && $mimetype != "image/png") {
                throw new UnsupportedFileException("file_type");
            }

            $imageInfo = getimagesize($_FILES["uploadedfile"]["tmp_name"]);
            if ($imageInfo["mime"] != "image/gif" && $imageInfo["mime"] != "image/jpeg" && $imageInfo["mime"] != "image/png") {
                throw new UnsupportedFileException("file_type");
            }
            if (move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $existname) && @file_exists($existname)) {
                $this->setField("upload", new MysString("success"));
            } else throw new InvalidActionException("error");

            $ffn = $mysidia->secure(preg_replace("/[^a-zA-Z0-9\\040.]/", "", (string) $mysidia->input->post("ffn")));
            if (empty($ffn)) throw new UnsupportedFileException("Unknown image");
            $serverpath = str_replace("../", "", $existname);
            $wwwpath = $mysidia->path->getAbsolute() . $serverpath;
            $mysidia->db->insert("filesmap", ["id" => null, "serverpath" => $serverpath, "wwwpath" => $wwwpath, "friendlyname" => $ffn]);
            return;
        }
    }

    public function delete()
    {
        $mysidia = Registry::get("mysidia");
        if (!$mysidia->input->post("iid")) {
            $this->index();
        } else {
            try {
                $file = new UploadedFile($mysidia->input->post("iid"));
                $serverpath = $file->getServerPath();
                if (!is_writable($file->getServerPath())) throw new NoPermissionException("notwritable");
                unlink($serverpath);
                $mysidia->db->delete("filesmap", "id = '{$file->getID()}'");
            } catch (Exception) {
                throw new InvalidIDException("noid");
            }
        }
    }

    public function settings()
    {
        $mysidia = Registry::get("mysidia");
        if ($mysidia->input->post("submit")) {
            $mysidia->db->update("settings", ["value" => $mysidia->input->post("enablegd")], "name = 'gdimages'");
            $mysidia->db->update("settings", ["value" => $mysidia->input->post("altbb")], "name = 'usealtbbcode'");
        }
    }
}
