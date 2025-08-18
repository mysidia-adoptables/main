<?php

namespace Resource\Core;
use Exception, PDO, PDOStatement;
use Resource\Collection\LinkedHashMap;
use Resource\Collection\LinkedList;
use Resource\Native\MysString;
use Resource\Native\Objective;

/**
 * The Database Class, extending from the PDO class and implementing Objective interface
 * It adds new features beyond PDO's capability, and implements the object's interface to be used in Collections.
 * @category Resource
 * @package Core
 * @author Fadillzzz
 * @copyright Mysidia Adoptables Script
 * @link http://www.mysidiaadoptables.com
 * @since 1.3.2
 * @todo Not much at this point.
 *
 */

class Database extends PDO implements Objective{

    /**
     * Database's name
     *
     * @access private
     * @var string
     */
    private $dbname;
    
    /**
     * Tables' prefix
     *
     * @access private
     * @var string
     */
    private $prefix;

    /**
     * Keep track of total rows from each query
     *
     * @access private
     * @var array
     */
    private $totalRows = [];

    /**
     * Stores join table
     *
     * @access private
     * @var array
     */
    private $joins = [];

    /**
     * If you don't know what this is, you shouldn't be here
     *
     * @param string $dbname
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $prefix
     * @access public
     */
    public function __construct($dbname, $host, $user, $password, $prefix = "adopts_"){
        parent::__construct("mysql:host={$host};dbname={$dbname}", $user, $password);
        $this->dbname = $dbname;
        $this->prefix = $prefix;
    }
	
    /**
     * The equals method, checks whether target object is equivalent to this one.
     * @param Objective  $object	 
     * @access public
     * @return Boolean
     */
    public function equals(Objective $object){
        return ($this == $object);
    } 	

    /**
     * The getClassName method, returns class name of an instance. 
     * @access public
     * @return String
     */
    public function getClassName(){
        return get_class($this);
    }

	/**
     * The hashCode method, returns the hash code for the very Database.
     * @access public
     * @return Int
     */			
    public function hashCode(){
	    return hexdec(spl_object_hash($this));
    }

	/**
     * The serialize method, serializes this Database Object into string format.
     * @access public
     * @return String
     */
    public function serialize(){
        return serialize($this);
    }
   
    /**
     * The unserialize method, decode a string to its object representation.
	 * @param String  $string
     * @access public
     * @return String
     */
    public function unserialize($string){
        return unserialize($string);
    }	
	
    /**
     * Basic INSERT operation
     *
     * @param string $tableName
     * @param array  $data         A key-value pair with keys that correspond to the fields of the table
     * @access public
     * @return object 
     */
    public function insert($tableName, array $data){
        return $this->createQuery($tableName, $data, 'insert');
    }	
	
    /**
     * Basic UPDATE operation
     *
     * @param string $tableName
     * @param array  $data        A key-value pair with keys that correspond to the fields of the table
     * @param string $clause      Clauses for creating advance queries with WHERE conditions, and whatnot
     * @param array  $values      A key-value pair with values that will bind to the where clause params.
     * @access public
     * @return object 
     */
    public function update($tableName, array $data, $clause = NULL, $values = []){
        return $this->createQuery($tableName, $data, 'update', $clause, $values);
    }

    /**
     * Basic SELECT operation
     *
     * @param string $tableName
     * @param array  $data        A key-value pair with values that correspond to the fields of the table
     * @param string $clause      Clauses for creating advance queries with JOINs, WHERE conditions, and whatnot
     * @param array  $values      A key-value pair with values that will bind to the where clause params.
     * @access public
     * @return object
     */
    public function select($tableName, array $data = [], $clause = NULL, $values = []){
        return $this->createQuery($tableName, $data, 'select', $clause, $values);
    }

    /**
     * Basic DELETE operation
     *
     * @param string $tableName
     * @param string $clause      Clauses for creating advance queries with JOINs, WHERE conditions, and whatnot
     * @param array  $values      A key-value pair with values that will bind to the where clause params.
     * @access public
     * @return object
     */
    public function delete($tableName, $clause = NULL, $values = []){
        return $this->createQuery($tableName, [], 'delete', $clause, $values);
    }

    /**
     * Adds JOIN to the next SELECT operation
     *
     * @param string $tableName
     * @param string $cond
     * @access public
     * @return object
     */
    public function join($tableName, $cond){
        $this->joins[] = [$tableName, $cond];
        return $this;
    }

    /**
     * Get total rows affected by previous queries
     *
     * @param int    $index
     * @return int
     */
    public function getTotalRows($index){
        if($index < 0){
            return $this->totalRows[count($this->totalRows) + $index];
        }
        return $this->totalRows[$index];
    }

    /**
     * Handles queries
     *
     * @param string $tableName
     * @param array  $data         A key-value pair with keys that correspond to the fields of the table
     * @param string $operation    Defines what kind of operation we'll carry on with the database
     * @access private
     * @return object
     */
    private function createQuery($tableName, array $data, $operation, $clause = NULL, array $values = []){
        if(!is_string($tableName)){
            throw new Exception('Argument 1 to ' . __CLASS__ . '::' . __METHOD__ . ' must be a string');
        }

        if(!in_array($operation, ['insert', 'update', 'select', 'delete'])){
            throw new Exception('Unknown database operation.');
        }

        $method = "{$operation}Query";
        $query = $this->$method($tableName, $data);

        if(!empty($clause)){
            $query .= ' WHERE ' . $clause;
        }
        //The comments can be removed for debugging purposes.
        //if($values) echo $query;
        $stmt = $this->prepare($query);
        if($operation != "select") $this->bindData($stmt, $data);
        if(!empty($values)) $this->bindData($stmt, $values);

        if (!$stmt->execute()){
            $error = $stmt->errorInfo();
            throw new Exception('Database error ' . $error[1] . ' - ' . $error[2]);
        }

        $this->totalRows[] = $stmt->rowCount();
        return $stmt;
    }

    /**
     * Generates prepared INSERT query string
     *
     * @param string $tableName
     * @param array  $data         A key-value pair with keys that correspond to the fields of the table
     * @access protected
     * @return string
     */
    protected function insertQuery($tableName, &$data){
        $tableFields = array_keys($data);
        return 'INSERT INTO ' . $this->prefix . $tableName . ' 
                  (`' . implode('`, `', $tableFields) . '`) 
                  VALUES (:' . implode(', :', $tableFields) . ')';
    }

    /**
     * Generates prepared UPDATE query string
     *
     * @param string $tableName
     * @param array  $data         A key-value pair with keys that correspond to the fields of the table
     * @access protected
     * @return string
     */
    protected function updateQuery($tableName, &$data){
        $setQuery = [];
        foreach ($data as $field => &$value){
            $setQuery[] = '`' . $field . '` = :' . $field;
        }
        return 'UPDATE ' . $this->prefix . $tableName . '
                  SET ' . implode(', ', $setQuery);
    }

    /**
     * Generates prepared SELECT query string
     *
     * @param string $tableName
     * @param array  $data         A key-value pair with values that correspond to the fields of the table
     * @access protected
     * @return string
     */
    protected function selectQuery($tableName, &$data){
        $joins = '';
        if(!empty($this->joins)){
            foreach ($this->joins as $k => &$join){
                $exploded = explode('=', $join[1]);
                $join_cond = '`' . $this->prefix . implode('`.`', explode('.', trim($exploded[0]))) . '` = `' . $this->prefix . implode('`.`', explode('.', trim($exploded[1]))) . '`';    
                $joins .= ' INNER JOIN `' . $this->prefix . $join[0] . '` ON ' . $join_cond;
            }
            $this->joins = NULL;
            $this->joins = [];
        }
        $fields = empty($data) ? '*' : '`' . implode('`, `', array_values($data)) . '`';
        return 'SELECT ' . $fields . '
                  FROM `' . $this->prefix . $tableName . '`' . $joins;
    }

    /**
     * Generates prepared DELETE query string
     *
     * @param string $tableName
     * @access protected
     * @return string
     */
    protected function deleteQuery($tableName, &$data = []){
        return 'DELETE FROM `' . $this->prefix . $tableName . '`';
    }

    /**
     * Binds data to the prepared statement
     *
     * @param object $stmt A PDOStatement object
     * @param array  $data A key-value pair to be bound with the statement
     * @access private
     * @return object
     */
    private function bindData(&$stmt, &$data){
        if(!empty($data)){
            foreach ($data as $field => &$value){
                $stmt->bindParam(':' . $field, $value);
            }    
        }
        return $this;
    }

	/**
     * The fetchList method, fetches a LinkedList of column data.
     * @param PDOStatement  $stmt
     * @access public
     * @return LinkedList
     */
    public function fetchList(PDOStatement $stmt){
        $list = new LinkedList;
        while($field = $stmt->fetchColumn()){
            $list->add(new MysString($field));
        }
        return $list;
    }

	/**
     * The fetchMap method, fetches a LinkedHashMap of column data.
     * @param PDOStatement  $stmt
     * @access public
     * @return LinkedHashMap
     */
    public function fetchMap(PDOStatement $stmt){
        $map = new LinkedHashMap;
        while($fields = $stmt->fetch(PDO::FETCH_NUM)){
            if(count($fields) == 1) $fields[1] = $fields[0];
            $map->put(new MysString($fields[0]), new MysString($fields[1]));
        }
        return $map;
    }

	/**
     * The nextAutoID method, fetches the next auto increment ID value for a given table.
     * @param String  $table
     * @access public
     * @return int
     */    
    public function nextAutoID($table){
        $query = "SELECT `AUTO_INCREMENT` FROM INFORMATION_SCHEMA.TABLES
                  WHERE TABLE_SCHEMA = '{$this->dbname}'
                  AND TABLE_NAME = '{$this->prefix}{$table}'";
        return $this->query($query)->fetchColumn();        
    }
	
    /**
     * Magic method __toString() for Database class, returns database information.
     * @access public
     * @return String
     */
    public function __toString(){
        return "This is a Database Object.";
    }    	
} 