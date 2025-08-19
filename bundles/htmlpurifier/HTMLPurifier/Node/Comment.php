<?php

/**
 * Concrete comment node class.
 */
class HTMLPurifier_Node_Comment extends HTMLPurifier_Node
{
    /**
     * @type bool
     */
    public $is_whitespace = true;

    /**
     * Transparent constructor.
     *
     * @param string $data String comment data.
     * @param int $line
     * @param int $col
     */
    public function __construct(public $data, $line = null, $col = null)
    {
        $this->line = $line;
        $this->col = $col;
    }

    public function toTokenPair()
    {
        return [new HTMLPurifier_Token_Comment($this->data, $this->line, $this->col), null];
    }
}
