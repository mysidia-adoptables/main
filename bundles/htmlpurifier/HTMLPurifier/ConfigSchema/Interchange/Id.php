<?php

/**
 * Represents a directive ID in the interchange format.
 */
class HTMLPurifier_ConfigSchema_Interchange_Id
{

    /**
     * @param string $key
     */
    public function __construct(public $key)
    {
    }

    /**
     * @return string
     * @warning This is NOT magic, to ensure that people don't abuse SPL and
     *          cause problems for PHP 5.0 support.
     */
    public function toString()
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getRootNamespace()
    {
        return substr((string) $this->key, 0, strpos((string) $this->key, "."));
    }

    /**
     * @return string
     */
    public function getDirective()
    {
        return substr((string) $this->key, strpos((string) $this->key, ".") + 1);
    }

    /**
     * @param string $id
     * @return HTMLPurifier_ConfigSchema_Interchange_Id
     */
    public static function make($id)
    {
        return new HTMLPurifier_ConfigSchema_Interchange_Id($id);
    }
}

// vim: et sw=4 sts=4
