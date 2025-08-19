<?php

/**
 * class for undefined variable object
 * This class defines an object for undefined variable handling
 *
 * @package    Smarty
 * @subpackage Template
 */
class Smarty_Undefined_Variable implements \Stringable
{
    /**
     * Returns false for 'nocache' and null otherwise.
     *
     * @param string $name
     *
     * @return bool
     */
    public function __get($name)
    {
        if ($name == 'nocache') {
            return false;
        } else {
            return null;
        }
    }

    /**
     * Always returns an empty string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return "";
    }
}
