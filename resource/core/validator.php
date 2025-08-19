<?php

namespace Resource\Core;

use ArrayObject;
use Resource\Core\Registry;
use Resource\Native\MysObject;

abstract class Validator extends MysObject implements Validative
{

    protected $validations;
    protected $action;
    protected $value;
    protected $data;
    protected $error = "";
    protected $status;

    public function __construct(ArrayObject $validations, $action = null, $value = null, $error = null)
    {
        $this->validations = $validations;
        $this->initialize($action, $value, $error);
    }

    public function initialize($action = null, $value = null, $error = null)
    {
        if ($action !== null) $this->setAction($action);
        if ($value !== null) $this->setValue($value);
        if ($error !== null) $this->setError($error);
    }

    public function getValidations()
    {
        return $this->validations;
    }

    public function setValidations(ArrayObject $validations, $overwrite = false)
    {
        if ($overwrite) $this->validations = $validations;
        else {
            foreach ($validations as $validation) {
                $this->validations->append($validation);
            }
        }
    }

    public function setAction($action)
    {
        $this->action = $action;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function setValue($value)
    {
        $this->value = $value;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setError($error, $overwrite = false)
    {
        $br = "<br>";
        if (!is_string($error) or empty($error)) throw new Exception('The error message is invalid. It must be a non-empty string.');
        elseif ($overwrite == true) $this->error = $error;
        else $this->error .= $error . $br;
    }

    public function triggerError()
    {
        if (empty($this->error)) return false;
        else return $this->error;
    }

    public function resetError()
    {
        $this->error = "";
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus($status = "")
    {
        $this->status = $status;
    }

    public function validate()
    {
        foreach ($this->validations as $validation) {
            $method = "check" . ucfirst((string) $validation);
            $this->$method();
        }
        return true;
    }

    public function emptyValidate($field)
    {
        if (empty($field)) return false;
        else return true;
    }

    public function numericValidate($field)
    {
        if (!is_numeric($field)) return false;
        else return true;
    }

    public function dataValidate($table, $fields, $whereclause, $values = [])
    {
        $mysidia = Registry::get("mysidia");
        $data = $mysidia->db->select($table, $fields, $whereclause, $values)->fetchObject();
        if (!is_object($data)) return false;
        else {
            $this->data = $data;
            return true;
        }
    }

    public function matchValidate($var1, $var2, $approach = "")
    {
        switch ($approach) {
            case "preg_match":
                return preg_match($var1, (string) $var2);
            default:
                if ($var1 == $var2) return true;
                else return false;
        }
        // End of the switch statement
    }
}
