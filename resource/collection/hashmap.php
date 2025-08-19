<?php

namespace Resource\Collection;

use Iterator;
use Resource\Exception\IllegalArgumentException;
use Resource\Native\MysArray;
use Resource\Native\Objective;

/**
 * The HashMap Class, extending from the abstract Map Class.
 * It is a standard hash table based implementation, but does not guarantee the order of the Map.
 * @category Resource
 * @package Collection
 * @author Hall of Famer
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.4
 * @todo Not much at this point.
 *
 */
class HashMap extends Map
{
    /**
     * serialID constant, it serves as identifier of the object being HashMap.
     */
    public const SERIALID = "362498820763181265L";

    /**
     * defaultCapacity constant, it defines the initial capacity used if no such argument is specified.
     */
    public const DEFAULTCAPACITY = 16;

    /**
     * defaultLoad constant, it specifies the initial load factor used if no such argument is specified.
     */
    public const DEFAULTLOAD = 0.75;

    /**
     * The entries property, it stores an array of Entrys specified in the HashMap.
     * @access protected
     * @var MysArray
     */
    protected $entries;

    /**
     * The entrySet property, it stores entries of this HashMap in Set Format, ready to iterate.
     * @access protected
     * @var Settable
     */
    protected $entrySet;

    /**
     * The loadFactor property, it stores the floating value load factor for the HashMap
     * @access protected
     * @var float
     */
    protected $loadFactor;

    /**
     * The size property, it specifies the current size of the Entrys inside the HashMap.
     * @access protected
     * @var int
     */
    protected $size;

    /**
     * The threshold property, it defines the next size value that the internal array in HashMap needs to increase.
     * @access protected
     * @var int
     */
    protected $threshold;

    /**
     * Constructor of HashMap Class, it initializes the HashMap given its capacity or another Collection Object.
     * @param int|Mappable $param
     * @param float $loadFactor
     * @access public
     * @return void
     */
    public function __construct($param = self::DEFAULTCAPACITY, $loadFactor = self::DEFAULTLOAD)
    {
        if (is_int($param)) {
            if ($param <= 0) {
                throw new IllegalArgumentException("The initial Capacity of HashMap cannot be less than 0.");
            }
            if ($loadFactor <= 0 or !is_numeric($loadFactor)) {
                throw new IllegalArgumentException("The load factor for HashMap must be a positive number.");
            }
            $capacity = 1;
            while ($capacity < $param) {
                $capacity = $capacity << 1;
            }
            $this->initialize($capacity, $loadFactor);
        } elseif ($param instanceof Mappable) {
            $capacity = (int)($param->size() / self::DEFAULTLOAD + 1);
            $capacity = ($capacity > self::DEFAULTCAPACITY) ? $capacity : self::DEFAULTCAPACITY;
            $this->initialize($capacity, $loadFactor);
            $this->createAll($param);
        } else {
            throw new IllegalArgumentException("Invalid Argument specified.");
        }
    }

    /**
     * The initialize method, initializes basic HashMap properties.
     * @param int $capacity
     * @param float $loadFactor
     * @access private
     * @return Objective
     */
    private function initialize($capacity, $loadFactor)
    {
        $this->loadFactor = $loadFactor;
        $this->threshold = (int)($capacity * $loadFactor);
        $this->entries = new MysArray($capacity);
    }

    /**
     * The size method, returns the current size of this HashMap.
     * @access public
     * @return int
     */
    public function size(): int
    {
        return $this->size;
    }

    /**
     * The createAll method, it is used instead of put by constructors if a Mappable object is supplied.
     * @param Mappable $map
     * @access private
     * @return void
     */
    private function createAll(Mappable $map): void
    {
        $iterator = $map->iterator();
        while ($iterator->hasNext()) {
            $entry = $iterator->next();
            $this->create($entry->getKey(), $entry->getValue());
        }
    }

    /**
     * The create method, it is used instead of put by constructors if a Mappable object is supplied.
     * @param Objective $key
     * @param Objective $value
     * @access private
     * @return void
     */
    private function create(Objective $key = null, Objective $value = null): void
    {
        $hash = ($key == null) ? 0 : $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        for ($entry = $this->entries[$index]; $entry != null; $entry = $entry->getNext()) {
            $object = $entry->getKey();
            if ($entry->getHash() == $hash and ($object == $key or ($key != null and $key->equals($object)))) {
                $entry->setValue($value);
                return;
            }
        }

        $this->createEntry($hash, $key, $value, $index);
    }

    /**
     * The hash method, it applies supplemental hash function to a given HashCode.
     * @param int $hash
     * @access public
     * @return int
     */
    public function hash($hash): int
    {
        $hash = @(($hash >> 20) ^ ($hash >> 12));
        return ($hash ^ ($hash >> 7) ^ ($hash >> 4));
    }

    /**
     * The indexFor method, it returns index for HashCode $hash.
     * @param int $hash
     * @param int $length
     * @access public
     * @return int
     */
    protected function indexFor($hash, $length = 1): int
    {
        return ($hash & ($length - 1));
    }

    /**
     * The createEntry method, it is similar to addEntry but is only used in HashMap Constructor.
     * It has an advantage of not having to worry about the Entries size.
     * @param Int $hash
     * @param Objective $key
     * @param Objective $value
     * @param int $index
     * @access public
     * @return void
     */
    public function createEntry($hash = 0, Objective $key = null, Objective $value = null, $index = 0): void
    {
        $entry = $this->entries[$index];
        $this->entries[$index] = new HashMapEntry($hash, $key, $value, $entry);
        $this->size++;
    }

    /**
     * The capacity method, obtains the capacity of this HashMap.
     * @access public
     * @return int
     */
    public function capacity(): int
    {
        return $this->entries->length();
    }

    /**
     * The clear method, drops all key-value pairs currently stored in this HashMap.
     * @access public
     * @return void
     */
    public function clear(): void
    {
        $entries = $this->entries;
        for ($i = 0; $i < $entries->length(); $i++) {
            $entries[$i] = null;
        }
        $this->size = 0;
    }

    /**
     * The containsKey method, checks if the HashMap contains a specific key among its key-value pairs.
     * @param Objective $key
     * @access public
     * @return bool
     */
    public function containsKey(Objective $key = null): bool
    {
        return ($this->getEntry($key) != null);
    }

    /**
     * The getEntry method, returns the entry associated with the specified key in HashMap.
     * This is a final method, and thus can not be overridden by child class.
     * @param Objective $key
     * @access public
     * @return Entry
     * @final
     */
    final public function getEntry(Objective $key = null): ?Entry
    {
        $hash = ($key == null) ? 0 : $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        for ($entry = $this->entries[$index]; $entry != null; $entry = $entry->getNext()) {
            $object = $entry->getKey();
            if ($entry->getHash() == $hash and ($object == $key or ($object != null and $key->equals($object)))) {
                return $entry;
            }
        }

        return null;
    }

    /**
     * The containsValue method, checks if the HashMap contains a specific value among its key-value pairs.
     * @param Objective $value
     * @access public
     * @return bool
     */
    public function containsValue(Objective $value = null): bool
    {
        if ($value == null) {
            return $this->containsNull();
        }
        $entries = $this->entries;
        for ($i = 0; $i < $entries->length(); $i++) {
            for ($entry = $entries[$i]; $entry != null; $entry = $entry->getNext()) {
                if ($value->equals($entry->getValue())) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * The containsNull method, checks if the HashMap contains a Null value among its key-value pairs.
     * @access private
     * @return bool
     */
    private function containsNull(): bool
    {
        $entries = $this->entries;
        for ($i = 0; $i < $entries->length(); $i++) {
            for ($entry = $entries[$i]; $entry != null; $entry = $entry->getNext()) {
                if ($entry->getValue() == null) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * The entryIterator method, acquires an instance of the EntryIterator object of this HashMap.
     * @access public
     * @return EntryIterator
     */
    public function entryIterator(): Iterator
    {
        return new EntryIterator($this);
    }

    /**
     * The entrySet method, returns a Set of entries contained in this HashMap.
     * @access public
     * @return EntrySet
     */
    public function entrySet(): EntrySet
    {
        $entrySet = ($this->entrySet == null) ? new EntrySet($this) : $this->entrySet;
        return $entrySet;
    }

    /**
     * The get method, acquires the value stored in the HashMap given its key.
     * @param Objective $key
     * @access public
     * @return ?Objective
     */
    public function get(Objective $key): ?Objective
    {
        if ($key == null) {
            return $this->getNull();
        }
        $hash = $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        for ($entry = $this->entries[$index]; $entry != null; $entry = $entry->getNext()) {
            $object = $entry->getKey();
            if ($entry->getHash() == $hash and ($object == $key or $key->equals($object))) {
                return $entry->getValue();
            }
        }
        return null;
    }

    /**
     * The getNull method, acquires a value from the Null Key stored in HashMap.
     * @access private
     * @return Objective
     */
    private function getNull()
    {
        for ($entry = $this->entries[0]; $entry != null; $entry = $entry->getNext()) {
            if ($entry->getKey() == null) {
                return $entry->getValue();
            }
        }
        return null;
    }

    /**
     * The getEntries method, acquires the Entries Array stored inside the HashMap.
     * @access public
     * @return MysArray
     */
    public function getEntries(): MysArray
    {
        return $this->entries;
    }

    /**
     * The keyIterator method, acquires an instance of the KeyIterator object of this HashMap.
     * @access public
     * @return KeyIterator
     */
    public function keyIterator(): KeyIterator
    {
        return new KeyIterator($this);
    }

    /**
     * The keySet method, returns a Set of keys contained in this HashMap.
     * @access public
     * @return KeySet
     */
    public function keySet(): KeySet
    {
        $keySet = ($this->keySet == null) ? new KeySet($this) : $this->keySet;
        return $keySet;
    }

    /**
     * The loadFactor method, obtains the load factor of this HashMap.
     * @access public
     * @return float
     */
    public function loadFactor(): float
    {
        return $this->loadFactor;
    }

    /**
     * The putAll method, copies all of the mappings from a specific map to this HashMap.
     * @param Mappable $map
     * @access public
     * @return void
     */
    public function putAll(Mappable $map): void
    {
        $size = $map->size();
        if ($size == 0) {
            return;
        }

        if ($size > $this->threshold) {
            $targetCapacity = (int)($size / $this->loadFactor + 1);
            $newCapacity = $this->entries->length();
            while ($newCapacity < $targetCapacity) {
                $newCapacity = $newCapacity << 1;
            }
            if ($newCapacity > $this->entries->length()) {
                $this->resize($newCapacity);
            }
        }

        $iterator = $map->iterator();
        while ($iterator->hasNext()) {
            $entry = $iterator->next();
            $this->put($entry->getKey(), $entry->getValue());
        }
    }

    /**
     * The resize method, resizes the EntrySet that holds records of this HashMap.
     * @param int $newCapacity
     * @access public
     * @return void
     */
    public function resize($newCapacity): void
    {
        $newEntries = new MysArray($newCapacity);
        $this->transfer($newEntries);
        $this->entries = $newEntries;
        $this->threshold = (int)($newCapacity * $this->loadFactor);
    }

    /**
     * The transfer method, transfers all old entries to new entries.
     * @param MysArray $newEntries
     * @access public
     * @return void
     */
    public function transfer(MysArray $newEntries): void
    {
        $oldEntries = $this->entries;
        $newCapacity = $newEntries->length();
        for ($i = 0; $i < $oldEntries->length(); $i++) {
            $entry = $oldEntries[$i];
            if ($entry != null) {
                $oldEntries[$i] = null;
                do {
                    $next = $entry->getNext();
                    $index = $this->indexFor($entry->getHash(), $newCapacity);
                    $entry->setNext($newEntries[$index]);
                    $newEntries[$index] = $entry;
                    $entry = $next;
                } while ($entry != null);
            }
        }
    }

    /**
     * The put method, associates a specific value with the specific key in this HashMap.
     * @param Objective $key
     * @param Objective $value
     * @access public
     * @return ?Objective
     */
    public function put(Objective $key = null, Objective $value = null): ?Objective
    {
        if ($key == null) {
            return $this->putNull($value);
        }

        $hash = $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        for ($entry = $this->entries[$index]; $entry != null; $entry = $entry->getNext()) {
            $object = $entry->getKey();
            if ($entry->getHash() == $hash and ($object == $key or $key->equals($object))) {
                $oldValue = $entry->getValue();
                $entry->setValue($value);
                $entry->recordAccess($this);
                return $oldValue;
            }
        }

        $this->addEntry($hash, $key, $value, $index);

        return null;
    }

    /**
     * The putNull method, associates a specific value with a null key in this HashMap.
     * @param Objective $value
     * @access private
     * @return ?Objective
     */
    private function putNull(Objective $value = null): ?Objective
    {
        for ($entry = $this->entries[0]; $entry != null; $entry = $entry->getNext()) {
            if ($entry->getKey() == null) {
                $oldValue = $entry->getValue();
                $entry->setValue($value);
                $entry->recordAccess($this);
                return $oldValue;
            }
        }
        $this->addEntry(0, null, $value, 0);
        return null;
    }

    /**
     * The addEntry method, adds an entry with a specific key, value and hash code.
     * @param Int $hash
     * @param Objective $key
     * @param Objective $value
     * @param int $index
     * @access public
     * @return void
     */
    public function addEntry($hash = 0, Objective $key = null, Objective $value = null, $index = 0): void
    {
        $entry = $this->entries[$index];
        $this->entries[$index] = new HashMapEntry($hash, $key, $value, $entry);
        if ($this->size++ >= $this->threshold) {
            $this->resize(2 * $this->entries->length());
        }
    }

    /**
     * The remove method, removes a specific key-value pair from the HashMap.
     * @param Objective $key
     * @access public
     * @return bool
     */
    public function remove(Objective $key = null): bool
    {
        return $this->removeKey($key) === null;
    }

    /**
     * The removeKey method, removes and returns an entry given a specific key in the HashMap.
     * This is a final method, and thus cannot be overridden by child class.
     * @param Objective $key
     * @access public
     * @return Entry
     * @final
     */
    final public function removeKey(Objective $key = null)
    {
        $hash = ($key == null) ? 0 : $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        $prev = $this->entries[$index];
        $entry = $prev;

        while ($entry != null) {
            $next = $entry->getNext();
            $object = $entry->getKey();
            if ($entry->getHash() == $hash and ($object == $key or ($key != null and $key->equals($object)))) {
                $this->size--;
                if ($prev == $entry) {
                    $this->entry[$index] = $next;
                } else {
                    $prev->setNext($next);
                }
                $entry->recordRemoval($this);
                return $entry;
            }
            $prev = $entry;
            $entry = $next;
        }
        return $entry;
    }

    /**
     * The removeMapping method, it is a special version for removal of EntrySet.
     * This is a final method, and thus cannot be overriden by child class.
     * @param Entry $object
     * @access public
     * @return ?Entry
     * @final
     */
    final public function removeMapping(Entry $object = null): ?Entry
    {
        if ($object == null) {
            return null;
        }
        $key = $object->getKey();
        $hash = ($key == null) ? 0 : $this->hash($key->hashCode());
        $index = $this->indexFor($hash, $this->entries->length());
        $prev = $this->entries[$index];
        $entry = $prev;

        while ($entry != null) {
            $next = $entry->getNext();
            if ($entry->getHash() == $hash and $entry->equals($object)) {
                $this->size--;
                if ($prev == $entry) {
                    $this->entries[$index] = $next;
                } else {
                    $prev->setNext($next);
                }
                $entry->recordRemoval($this);
                return $entry;
            }
            $prev = $entry;
            $entry = $next;
        }
        return $entry;
    }

    /**
     * The valueIterator method, acquires an instance of the ValueIterator object of this HashMap.
     * @access public
     * @return ValueIterator
     */
    public function valueIterator(): ValueIterator
    {
        return new ValueIterator($this);
    }

    /**
     * The valueSet method, returns a Set of values contained in this HashMap.
     * @access public
     * @return ValueSet
     */
    public function valueSet(): ValueSet
    {
        $valueSet = ($this->valueSet == null) ? new ValueSet($this) : $this->valueSet;
        return $valueSet;
    }
}
