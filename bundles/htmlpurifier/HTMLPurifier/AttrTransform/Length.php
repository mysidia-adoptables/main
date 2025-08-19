<?php

/**
 * Class for handling width/height length attribute transformations to CSS
 */
class HTMLPurifier_AttrTransform_Length extends HTMLPurifier_AttrTransform
{

    /**
     * @type string
     */
    protected $cssName;

    public function __construct(/**
     * @type string
     */
    protected $name, $css_name = null)
    {
        $this->cssName = $css_name ?: $this->name;
    }

    /**
     * @param array $attr
     * @param HTMLPurifier_Config $config
     * @param HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr[$this->name])) {
            return $attr;
        }
        $length = $this->confiscateAttr($attr, $this->name);
        if (ctype_digit((string) $length)) {
            $length .= 'px';
        }
        $this->prependCSS($attr, $this->cssName . ":$length;");
        return $attr;
    }
}

// vim: et sw=4 sts=4
