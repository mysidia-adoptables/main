<?php

namespace Resource\Utility;

use Resource\Collection\HashMap;
use Resource\Native\MysArray;
use Resource\Native\MysObject;
use Resource\Native\MysString;

/**
 * The OutputBuffer Class, it is part of the utility package and extends from the Object Class.
 * It implements PHP basic buffer functions for output, it can be used together with header and cookie functions.
 * a URL object is immutable, once set it cannot be altered with setter methods.
 * @category Resource
 * @package Utility
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not sure, but will come in handy.
 */

final class OutputBuffer extends MysObject
{
    /**
     * The contents property, stores the contents inside the Output buffer.
     * @access private
     * @var String
     */
    private $contents;

    /**
     * The flush property, stores the buffer flush string attribute.
     * @access private
     * @var String
     */
    private $flush;

    /**
     * The length property, defines the buffer length.
     * @access private
     * @var int
     */
    private $length = 0;

    /**
     * The level property, defines the buffer level.
     * @access private
     * @var int
     */
    private $level = 0;

    /**
     * The status property, specifies the status of the Output buffer.
     * @access private
     * @var HashMap
     */
    private $status;

    /**
     * Constructor of OutputBuffer Class, it instantiates a buffer object.
     * @param Boolean  $output
     * @access publc
     * @return void
     */
    public function __construct($output = false)
    {
        $this->output = $output;
    }

    /**
     * The clean method, discard the contents inside output buffer.
     * @access public
     * @return void
     */
    public function clean()
    {
        ob_clean();
        $this->reset();
    }

    /**
     * The endClean method, erases the output buffer and turns off output buffering.
     * @access public
     * @return void
     */
    public function endClean()
    {
        ob_end_clean();
        $this->reset();
    }

    /**
     * The endFlush method, sends/flushes the output buffer and turns off output buffering.
     * @access public
     * @return void
     */
    public function endFlush()
    {
        ob_end_flush();
        $this->reset();
    }

    /**
     * The flush method, sends/flushes the output buffer.
     * @access public
     * @return void
     */
    public function flush()
    {
        ob_flush();
        $this->reset();
    }

    /**
     * The getContents method, getter method for property $contents.
     * @access public
     * @return String
     */
    public function getContents()
    {
        $this->contents = ob_get_contents();
        return $this->contents;
    }

    /**
     * The getContents method, getter method for property $flush.
     * @access public
     * @return String
     */
    public function getFlush()
    {
        $this->flush = ob_get_flush();
        return $this->flush;
    }

    /**
     * The getLength method, getter method for property $length.
     * @access public
     * @return int
     */
    public function getLength()
    {
        $this->length = ob_get_length();
        return $this->length;
    }

    /**
     * The getLevel method, getter method for property $level.
     * @access public
     * @return int
     */
    public function getLevel()
    {
        $this->level = ob_get_level();
        return $this->level;
    }

    /**
     * The getStatus method, getter method for property $status.
     * @access public
     * @return Array
     */
    public function getStatus()
    {
        $status = ob_get_status();
        foreach ($status as $key => $value) {
            $this->status->put(new MysString($key), new MysString($value));
        }
        return $this->status;
    }

    /**
     * The gzHandler method, it's ob_start callback method to gzip output buffer.
     * @param String  $buffer
     * @param int  $mode
     * @access public
     * @return String
     */
    public function gzHandler($buffer, $mode)
    {
        return ob_gzhandler($buffer, $mode);
    }

    /**
     * The implicitFlush method, it's ob_start callback method to gzip output buffer.
     * @param Boolean  $flag
     * @access public
     * @return void
     */
    public function implicitFlush($flag = true)
    {
        ob_implicit_flush($flag);
    }

    /**
      * The listHandlers method, lists all output handlers in use.
      * @param Boolean  $flag
      * @access public
      * @return MysArray
      */
    public function listHandlers($flag = true)
    {
        $handlers = ob_list_handlers();
        $obHandlers = new MysArray(count($handlers));
        $i = 0;

        foreach ($handlers as $handler) {
            $obHandlers[$i] = $handler;
        }
        return $obHandlers;
    }

    /**
     * The reset method, resets properties for the object buffer.
     * @access public
     * @return void
     */
    private function reset()
    {
        $this->contents = null;
        $this->flush = null;
        $this->length = 0;
        $this->level = 0;
        $this->status = new HashMap();
    }

    /**
     * The start method, initializes the process of object buffering.
     * @access public
     * @return void
     */
    public function start()
    {
        ob_start();
    }
}
