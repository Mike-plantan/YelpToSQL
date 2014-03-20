<?php

// OTHPDO 
// VERSION 1.0 

class db
{



	/**
	* PDO connection that will be used in the execute method to connect to MySQL database.
	* @access private
	* @var $connection
	*/
	private  $connection;
	
	/**
	* Database name will be fetched from config.php
	* @access private
	* @var $db_name
	*/
	private $db_name;
	
	
	/**
	* Database Host. will be fetched from config.php
	* @access private
	* @var $db_host
	*/
	private $db_host;
	   
	/**
	* Database username. will be fetched from config.php
	* @access private
	* @var $db_username
	*/
	private $db_username;
	   
	/**
	* Database password. will be fetched from config.php
	* @access private
	* @var $db_password
	*/
	private $db_password;
	   
	/**
	* This variable will be holding a boolean value depending on the last query.
	* @access public
	* @var $null
	*/ 
	public $null;
	   
	/**
	* Number of rows affected by the last query.
	* @access public
	* @var $num_of_rows
	*/
	public $num_of_rows;
	   
	/**
	* If the last query was insert, then we are going to hold the last inserted id in the variable.
	* @access public
	* @var $last_insert_id
	*/
	public $last_insert_id;
	   
	/**
	* This is going to be used for printing the data for debuging purposes.
	* @access private
	* @var $db_name
	*/
	private $data;
	
	
	/**
	* __construct
	*
	* this will initaite the object. 
	*
	* @return none.
	*/
	public function __construct(){
	   
	   // we need to set the username, password, host and database name for this object
	   // we are calling this method to fetch values from config.php
	   $this->set_data();
	   
	   // initate the database connection, and save the connection for future queries.
	   try
	   {
	   		$this->connection = new PDO("mysql:host=".$this->db_host.";dbname=".$this->db_name."",
	   									 $this->db_username, 
	   									 $this->db_password);
	   }
	   
	   
	   // catch any PDO exception and kill the script.
		catch(PDOException $e)
		{
			die("<b>Couldn't Establish Database Connection.</b> <br/><br/> $e");
		}
   
    }
    
    
    
	/**
	* set_data
	*
	* this function will include config.php and get all the variables needed for PDO database connection. 
	*
	* @return none.
	*/
    private function set_data(){
    	
    	// including config.php
   	   	include('config.php');
   		
   		// save the database name in the object.
   		$this->db_name = $db_name;
   		
   		// save the database host in the object.
   		$this->db_host = $db_host;
   		
   		// save the database username in the object.
   		$this->db_username = $db_username;
   		
   		// save the database password in the object.
   		$this->db_password = $db_password;
   }
   
	/**
	* execute
	*
	* this function will execute any SQL query and return the fetched array. 
	*
	* @return array mixed.
	*/
   	public function execute(){
   		
   		// check if the connection is null
   		if($this->connection == null){return null;}
   		
   		
   		/**
	   	* Get arguments.
	 	*/
   		$args = func_get_args();
   		
   		/**
	   	* Get the first SQL statement to a variable and then remove it from the array.
	 	*/
   		$query = array_shift($args);
   		
   		
   		/**
	    * prepare and execute the statement, and return an array with data. 
	    */
	    $stmt = $this->connection->prepare($query);
	    
	    /** Arabic chars modifation */
	    $arabic1 = $this->connection->prepare("set character_set_server='utf8'");
	    $arabic1->execute(array());
	    $arabic2 = $this->connection->prepare("set names 'utf8'");
	    $arabic2->execute(array());
	    
	    // we are checking the variables passed by an array or variables.
	    if(is_array($args[0])){
		    $stmt->execute($args[0]);
	    }else{
		     $stmt->execute($args);
	    }
	    
   		// fetching
   		$data = $stmt->fetchALL(PDO::FETCH_ASSOC);
   		
   		
   		// Get last insert_id and save it.
   		$this->last_insert_id = $this->connection->lastInsertId();
   		
   		// Get num of rows and save it.
   		$this->num_of_rows = $stmt->rowCount();
   		
   		// check if this result equal to null.
  		if($this->num_of_rows == 0){
	   		$this->null = true;
   		}else{
	   		$this->null = false;
   		}
   		
   		// save the data for debuging 
   		$this->data = $data;
   		
   		// return the data.
   		return $data;
   		
   	}
   	
   	public function getALL(){
   	
   		// GET the arguments of the function.
	   	$args = func_get_args();
	   	
	   	// check args
		if(count($args) == 0){
			return array("ERROR: You have to declare the table name");
		}
			   	
	   	// GET the table name
	   	$table_name = array_shift($args);
	   
	   	// If the user insert the order_by 
	   	if(count($args) > 0){
		   	$order_by = array_shift($args);
		   	
		   	// if desc 
		   	if(count($args) > 0){
		   		$desc = array_shift($args);
		   	}
		   	
		   	if($desc){
		   		$data = $this->execute("SELECT * FROM $table_name ORDER BY $order_by DESC");
		   	}else{
		   		$data = $this->execute("SELECT * FROM $table_name ORDER BY $order_by ASC");
		}
	   	
	   	
	   	}else{
		   	$data = $this->execute("SELECT * FROM $table_name");
	   	}
	   	
	   	if($this->null){
		   	return array("ERROR: Empty Query.");
	   	}
	   	return $data;
	   	
   	}
   	
   	public function getCol(){
   		
   		
   		// Get the args
   		$args = func_get_args();
   		$count = count($args);
   		$col = array_shift($args);
	   	$table_name = array_shift($args);
	   	$where_col = array_shift($args);
	   	$where_val = array_shift($args);
	   	$order_by = array_shift($args);
	   	$desc = array_shift($args);
	   	
	   	
	   	if($count == 0){
		   	return array("ERROR: You have to declare the column name.");
	   	}
	   	
	   	if($count == 1){
		   	return array("ERROR: You have to declare the table name.");
	   	}
	   	
	   	if($count == 2){
		   	return $data = $this->execute("SELECT $col FROM $table_name");
	   	}
	   	
	   	if($count == 3){
		   	return array("ERROR: You have to declare the WHERE value.");
	   	}
	   	
	   	if($count == 4){
		   	return $this->execute("SELECT $col FROM $table_name WHERE $where_col = ?", $where_val);
	   	}
	   	
	   	if($count == 5){
		   	return $data = $this->execute("SELECT $col FROM $table_name WHERE $where_col = ? ORDER BY $order_by", $where_val);
	   	}
	   	
	   	if($count == 6){
	   		
	   		if($where_col == null && $where_val == null){
	   		
	   			if($desc == true){
		   			$query = "SELECT $col FROM $table_name ORDER BY $order_by DESC";
	   			}else{
		   			$query = "SELECT $col FROM $table_name ORDER BY $order_by ASC";
	   			}
	   			
	   			return $this->execute($query);
		   	}else{
			   	if($desc == true){
				   	return $data = $this->execute("SELECT $col FROM $table_name WHERE $where_col = ? ORDER BY $order_by DESC", $where_val);
			   	}else{
				   	return $data = $this->execute("SELECT $col FROM $table_name WHERE $where_col = ? ORDER BY $order_by ASC", $where_val);
			   	}
		   	}
	   	}	
   	}

   	public function getWithCondition($colmun_name, $table_name, $colsAndValues, $condition, $boolean){
   		
   		$query = "SELECT $colmun_name FROM $table_name WHERE ";
   		
   		$values = array();
   		
   		$countOfValues = count($colsAndValues);
   		$counter = 0;
   		
   		
	   	foreach($colsAndValues as $col => $val){
	   		if($countOfValues == 1){
		   		$query .= $col." = ? ";
	   		}else{
		   		$query .= $col." = ? $condition ";
	   		}
	   		$values[$counter] = $val;
		   	$countOfValues--;
		   	$counter++;
		   	
		  
	   	}
	   	
	   	if(!$boolean){
		   return $this->execute($query,$values);	
	   	}else{
		   	if($this->null){
			   	return false;
		   	}else{
			   	return true;
		   	}
	   	}
	   		
	   	
   	}
   	
   	public function printData(){
   	
   	
	   	if($this->data != null){
		   	foreach($this->data as $item){
		   	foreach($item as $f => $v){
		   		if(!is_numeric($f)){
			   		echo $f. ' : '. $v;
			   		echo '<br/>';
		   		}

		   	}
		   	echo '<hr>';
	   	}
	   	}
   	}
   	
   	public function updateField($field, $table, $to, $where, $whereValue){
	   	$SQL = "UPDATE $table SET $field = ? WHERE $where = ?";
	   	$this->execute($SQL,$to,$whereValue);
   	}
   	
   	public function updateMulFields($array, $table, $where, $whereValue){
	   	$query = "UPDATE $table SET ";
	   	
	   	
	   	$countOfValues = count($array);
   		$counter = 0;
   		
   		
	   	foreach($array as $col => $val){
	   		if($countOfValues == 1){
		   		$query .= $col." = ? ";
	   		}else{
		   		$query .= $col." = ?, ";
	   		}
	   		$values[$counter] = $val;
		   	$countOfValues--;
		   	$counter++;
		   	
		  
	   	}
	   	
	   	array_push($values,$whereValue);
	   	
	   	$query .= "WHERE $where = ?";
	   	
	   	$this->execute($query,$values);
	   	
	   	
   	}
   	
   	public function insert($table_name,$array){
	   	
	   	$type = array_keys($array) !== range(0, count($array) - 1);
	   	
	   	if($type == false){
		   	
		   	$query = "INSERT INTO $table_name VALUES( ";
		   	$countOfValues = count($array);
   	
		   	foreach($array as $col){
		   	
		   		if($countOfValues == 1){
			   		$query .= '?';
		   		}else{
			   		$query .= '?'.", ";
		   		}
				$countOfValues--;
			}
			
			$query .= ')';
		   	
		   	$this->execute($query,$array);
		   	
		   	
	   	}else{
		   	$query = "INSERT INTO $table_name (";
		   	$countOfValues = count($array);
		   	foreach($array as $col => $val){
		   	
		   		if($countOfValues == 1){
			   		$query .= $col;
		   		}else{
			   		$query .= $col.", ";
		   		}
				$countOfValues--;
				$vala[] = $val;
			}
		   	
		   	$query .= ") VALUES (";
		   	$countOfValues = count($array);
		   	foreach($array as $col){
		   	
		   		if($countOfValues == 1){
			   		$query .= '?';
		   		}else{
			   		$query .= '?'.", ";
		   		}
				$countOfValues--;
			}
			
			$query .= ')';
		   	$this->execute($query,$vala);
	   	}
	   	
   	} 
   	
   	public function delete($table_name, $primary,$value){
	   	$SQL = "DELETE FROM $table_name WHERE $primary = ?";
	   	$this->execute($SQL,$value);
   	}
   	
   	public function getCount($table_name,$field,$DISTINCT){
	   	if($DISTINCT == true){
		   	$SQL = "SELECT COUNT(DISTINCT $field) FROM $table_name";
	   	}else{
		   	$SQL = "SELECT COUNT($field) FROM $table_name";
	   	}
	   	
	   	$result = $this->execute($SQL);
	   	return $result[0][0];
   	}
   	
   	public function random($table_name){
	   	$SQL = "SELECT * FROM $table_name ORDER BY RAND() LIMIT 1";
	   	return $this->execute($SQL);
   	}
   	
   	public function max($table_name,$col){
	   	$result = $this->execute("SELECT MAX($col) FROM $table_name");
	   	return $result[0][0];
   	}
   	
   	public function min($table_name,$col){
	   	$result = $this->execute("SELECT MIN($col) FROM $table_name");
	   	return $result[0][0];
   	}
   	
   	public function in($table_name,$col,$array, $not = false, $order_by = null){
   	
   		if($not){
   		
   			if($order_by != null)
	   		$SQL = "SELECT * FROM $table_name WHERE $col NOT IN (";
   		}else{
	   		$SQL = "SELECT * FROM $table_name WHERE $col IN (";
   		}
	   
	   	
	   	$countOfValues = count($array);
	   	foreach($array as $val){
	   		if($countOfValues == 1){
		   		$SQL .= "?) ";
	   		}else{
		   		$SQL .= "?, ";
	   		}
		   	$countOfValues--;
	   	}
	   	
	   	if($order_by != null){
		   	$SQL .= " ORDER BY $order_by";
	   	}
	   	
	   	return $this->execute($SQL,$array);
	   	
   	}
   	
   	public function avg($table_name,$col){
	   	$result = $this->execute("SELECT AVG($col) FROM $table_name");
	   	return $result[0][0];
   	}
   	
   	public function sum($table_name,$col){
	   	$result = $this->execute("SELECT SUM($col) FROM $table_name");
	   	return $result[0][0];
   	}
   	
   	public function inc($table_name,$col,$where, $where_val){
	   	$SQL = "UPDATE $table_name SET $col = $col + 1 WHERE $where = ?";
	   	$this->execute($SQL,$where_val);
   	}
   	
   	public function dec($table_name,$col,$where, $where_val){
	   	$SQL = "UPDATE $table_name SET $col = $col - 1 WHERE $where = ?";
	   	$this->execute($SQL,$where_val);
   	}
   	
   	public function search(){
	   	// Get the args
   		$args = func_get_args();
   		$count = count($args);
   		$table_name = array_shift($args);
	   	$field = array_shift($args);
	   	$keyword = array_shift($args);
	   	$type = array_shift($args);
	   	$not = array_shift($args);
	   	
	   	if($count < 3){
		   	return null;
		   	
	   	}elseif($count == 3){
	   		$keyword = "%".$keyword."%";
		   	$SQL = "SELECT * FROM $table_name WHERE $field LIKE ?";
		   	return $this->execute($SQL,$keyword);
	   	}elseif($count == 4){
		   	
		   	if($type == 0){
			   	$keyword = "%".$keyword."%";
			   	$SQL = "SELECT * FROM $table_name WHERE $field LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}elseif($type == 2){
			   	$keyword = "%".$keyword;
			   	$SQL = "SELECT * FROM $table_name WHERE $field LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}elseif($type == 1){
			   	$keyword = $keyword."%";
			   	$SQL = "SELECT * FROM $table_name WHERE $field LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}
		   	
		   	
		   	
	   	}elseif($count == 5){
		   	
		   	if($not == true){
			   	if($type == 0){
			   	$keyword = "%".$keyword."%";
			   	$SQL = "SELECT * FROM $table_name WHERE $field NOT LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}elseif($type == 2){
			   	$keyword = "%".$keyword;
			   	$SQL = "SELECT * FROM $table_name WHERE $field NOT LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}elseif($type == 1){
			   	$keyword = $keyword."%";
			   	$SQL = "SELECT * FROM $table_name WHERE $field NOT LIKE ?";
			   	return $this->execute($SQL,$keyword);
		   	}
		   	}
		   	
	   	}else{
		   	return null;
	   	}
	   	
	   	
	   	
	   	
	   	
   	}
   	
}
?>