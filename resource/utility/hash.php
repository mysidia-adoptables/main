<?php

namespace Resource\Utility;

use Resource\Native\MysArray;
use Resource\Native\MysObject;

/**
 * The Hash Class, it is part of the utility package and extends from the Object Class.
 * It implements PHP basic hash functions, and adds enhanced features upon them.
 * @category Resource
 * @package Utility
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not sure, but will come in handy.
 */
final class Hash extends MysObject
{

    /**
     * The algorithm property, it defines the algorithm for hash function.
     * This value is set at Hash object instantiation, the default value is md5.
     * @access private
     * @var String
     */
    private $algorithm = "md5";

    /**
     * The context property, it stores a hash context resource.
     * @access private
     * @var Resource
     */
    private $context = null;

    /**
     * The constructor for Hash Class, it creates a Hash object and initialize the context.
     * @param String $algorithm
     * @access public
     * @return void
     */
    public function __construct($algorithm = "")
    {
        if ($algorithm) $this->algorithm = $algorithm;
        $this->init($this->algorithm);
    }

    /**
     * The init method, initialize the Curl object.
     * @param String $algorithm
     * @access private
     * @return Void
     */
    private function init($algorithm)
    {
        $this->context = hash_init($algorithm);
    }

    /**
     * The getAlgorithms method, returns a list of available Hash algorithms.
     * @access public
     * @return MysArray
     */
    public function getAlgorithms()
    {
        $algos = hash_algos();
        $array = new MysArray(count($algos));
        for ($i = 0; $i < count($algos); $i++) {
            $array[$i] = $algos[$i];
        }
        return $array;
    }

    /**
     * The getContext method, returns a reference of context resource stored in this Hash object.
     * @access public
     * @return Resource
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * The copy method, returns a copy of context resource stored in this Hash object.
     * @access public
     * @return Resource
     */
    public function copy()
    {
        return hash_copy($this->context);
    }

    /**
     * The file method, generates a hashcode for a file.
     * @param String $file
     * @param bool $rawOutput
     * @access public
     * @return String
     */
    public function file($file, $rawOutput = false)
    {
        return hash_file($this->algorithm, $file, $rawOutput);
    }

    /**
     * The hmac method, generate a keyed hash value using the HMAC method.
     * @param String $data
     * @param String $data
     * @param bool $rawOutput
     * @access public
     * @return String
     */
    public function hmac($file, $data, $rawOutput = false)
    {
        return hash_hmac($this->algorithm, (string) $file, $data, $rawOutput);
    }

    /**
     * The hmacFile method, generate a keyed hash value using the HMAC method and the contents of a given file.
     * @param String $file
     * @param String $key
     * @param bool $rawOutput
     * @access public
     * @return String
     */
    public function hmacFile($file, $key, $rawOutput = false)
    {
        return hash_hmac_file($this->algorithm, $file, $key, $rawOutput);
    }

    /**
     * The pbkdf2 method, generate a PBKDF2 key derivation of a supplied password.
     * @param String $password
     * @param String $salt
     * @param Int $iteration
     * @access public
     * @return String
     */
    public function pbkdf2($password, $salt, $iteration)
    {
        return hash_pbkdf2($this->algorithm, $password, $salt, $iteration);
    }

    /**
     * The update method, pump data into an active hashing context.
     * @param String $data
     * @access public
     * @return String
     */
    public function update($data)
    {
        $this->context = hash_update($this->context, $data);
    }

    /**
     * The updateFile method, pump data into an active hashing context from a file.
     * @param String $file
     * @access public
     * @return String
     */
    public function updateFile($file)
    {
        $this->context = hash_update_file($this->context, $file);
    }

    /**
     * The updateStream method, pump data into an active hashing context from an open stream.
     * @param Stream $stream
     * @access public
     * @return String
     */
    public function updateStream(Stream $stream)
    {
        $this->context = hash_update_stream($this->context, $stream);
    }

    /**
     * The finalize method, returns the finalized hash code.
     * @access public
     * @return int
     */
    public function finalize(): int
    {
        return hash_final($this->context);
    }
}
