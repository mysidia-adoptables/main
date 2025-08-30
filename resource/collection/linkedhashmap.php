<?php

namespace Resource\Collection;

use Resource\Native\MysArray;
use Resource\Native\Objective;

/**
 * The LinkedHashMap Class, extending from the HashMap Class.
 * It defines an ordered HashMap accessible from both the front and back.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */

class LinkedHashMap extends HashMap
{
    /**
     * serialID constant, it serves as identifier of the object being LinkedHashMap.
     */
    public const SERIALID =  "3801124242820219131L";

    /**
     * The header property, it stores the head of doubly linked list.
     * @access protected
     * @var MapEntry
     */
    private $header;

    /**
     * Constructor of LinkedHashMap Class, it calls parent constructor and adds its own implementation.
     * @param int|Mappable  $param
     * @param float  $loadFactor
     * @param Boolean  $order
     * @access public
     * @return void
     */
    public function __construct($param = HashMap::DEFAULTCAPACITY, $loadFactor = HashMap::DEFAULTLOAD, /**
     * The order property, it defines if an access order is specified for this LinkedHashMap.
     * @access protected
     */
        protected $order = false)
    {
        parent::__construct($param, $loadFactor);
        if (is_int($param)) {
            $this->header = new LinkedHashMapEntry(-1, null, null, null);
            $this->header->setBefore($this->header);
            $this->header->setAfter($this->header);
        }
    }

    /**
     * The addEntry method, adds an entry with a specific key, value and hash code.
     * @param int  $hash
     * @param Objective  $key
     * @param Objective  $value
     * @param int  $index
     * @access public
     * @return void
     */
    public function addEntry($hash = 0, Objective|null $key = null, Objective|null $value = null, $index = 0): void
    {
        $this->createEntry($hash, $key, $value, $index);
        $eldest = $this->header->getAfter();
        if ($this->removeEldest($eldest)) {
            $this->removeKey($eldest->getKey());
        } else {
            if ($this->size >= $this->threshold) {
                $this->resize(2 * $this->entries->length());
            }
        }
    }

    /**
     * The clear method, drops all key-value pairs currently stored in this LinkedHashMap.
     * @access public
     * @return void
     */
    public function clear(): void
    {
        parent::clear();
        $this->header->setBefore($this->header);
        $this->header->setAfter($this->header);
    }

    /**
     * The containsValue method, checks if the LinkedHashMap contains a specific value among its key-value pairs.
     * @param Objective  $value
     * @access public
     * @return Boolean
     */
    public function containsValue(Objective|null $value = null): bool
    {
        if ($value == null) {
            for ($entry = $this->header->getAfter(); $entry !== $this->header; $entry = $entry->getAfter()) {
                if ($entry->getValue() == null) {
                    return true;
                }
            }
        } else {
            for ($entry = $this->header->getAfter(); $entry !== $this->header; $entry = $entry->getAfter()) {
                if ($value->equals($entry->getValue())) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * The createEntry method, it is similar to addEntry but does not resize the entries array or remove eldest entry.
     * @param int  $hash
     * @param Objective  $key
     * @param Objective  $value
     * @param int  $index
     * @access public
     * @return void
     */
    public function createEntry($hash = 0, Objective|null $key = null, Objective|null $value = null, $index = 0): void
    {
        $oldEntry = $this->entries[$index];
        $entry = new LinkedHashMapEntry($hash, $key, $value, $oldEntry);
        $this->entries[$index] = $entry;
        $entry->addBefore($this->header);
        $this->size++;
    }

    /**
     * The entryIterator method, acquires an instance of the EntryIterator object of this LinkedHashMap.
     * @access public
     * @return EntryLinkedIterator
     */
    public function entryIterator()
    {
        return new EntryLinkedIterator($this);
    }

    /**
     * The get method, acquires the value stored in the LinkedHashMap given its key.
     * @param Objective  $key
     * @access public
     * @return Objective
     */
    public function get(Objective $key)
    {
        $entry = $this->getEntry($key);
        if ($entry == null) {
            return null;
        }
        $entry->recordAccess($this);
        return $entry->getValue();
    }

    /**
     * The getHeader method, getter method for property $header.
     * @access public
     * @return MapEntry
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * The getOrder method, getter method for property $order.
     * @access public
     * @return Boolean
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * The keyIterator method, acquires an instance of the KeyIterator object of this LinkedHashMap.
     * @access public
     * @return KeyLinkedIterator
     */
    public function keyIterator()
    {
        return new KeyLinkedIterator($this);
    }

    /**
     * The removeEldest method, allows the map to modify itself as directed by its return value.
     * @param Entry  $eldest
     * @access protected
     * @return Boolean
     */
    protected function removeEldest(Entry|null $eldest = null): bool
    {
        return false;
    }

    /**
     * The transfer method, transfers all old entries to new entries.
     * @param MysArray  $newEntries
     * @access public
     * @return Void
     */
    public function transfer(MysArray $newEntries): void
    {
        $newCapacity = $newEntries->length();
        for ($entry = $this->header->getAfter(); $entry !== $this->header; $entry = $entry->getAfter()) {
            $index = $this->indexFor($entry->getHash(), $newCapacity);
            $entry->setNext($newEntries[$index]);
            $newEntries[$index] = $entry;
        }
    }

    /**
     * The valueIterator method, acquires an instance of the ValueIterator object of this LinkedHashMap.
     * @access public
     * @return ValueLinkedIterator
     */
    public function valueIterator()
    {
        return new ValueLinkedIterator($this);
    }
}
