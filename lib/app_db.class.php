<?php

/**
 * APP_DB is used to make transactions with database.
 *
 * How to make a custom SQL:
 *  1. Clear previous result $db->clearResult();
 *  2. Set custom SQL $db->sql = "SELECT * FROM ..."; OBS! make sure to escape variables from client side!!
 *  3. Execute query  $db->executeSql();
 *  4. Get result $var = $db->getResult($result_type= MYSQL_ASSOC, $class = null);
 *
 *
 * How to find:
 * 1. Number of rows returned: $this->result->num_rows;
 * 2. Number of rows affected (by update): $this->affected_rows;
 * 3. Free result resources: $this->result->free();
 * 4. Escaping characters: $this->escape_string('This is an unescape "string"');
 *
 * @author Vytautas
 */
class APP_DB extends mysqli {

    var $db_error;
    var $sql;
    var $connected;
    var $log;
    var $prefix;
    /**
     * @var mysqli_result
     */
    var $result;

    function APP_DB($host, $user, $password, $dbname, $port = 3306, $prefix = false) {
    	$this->log = Logger::getLogger("com.dalisra.DB");
        $this->prefix = $prefix;
        mysqli_report(MYSQLI_REPORT_STRICT);  // tell mysqli to throw exceptions.
        try{
            parent::mysqli($host, $user, $password, $dbname, $port);
            $this->connected = true;
        }catch (Exception $e){
            $this->connected = false;
            $this->setError("Failed to connect to database." . $e);
            $this->log->error("Failed to connect to database: " . $e);
        }

    }

    /**
     * $params['limit'] //example: "10"
     * $params['orderby'] //example: "date desc, id asc"
     * $params['where'] //example: array('id'=>'10', 'state'=>'blocked')
     * $params['select']
     * $params['from']
     * @param array $params
     * @param int $result_type //MYSQLI_ASSOC, MYSQLI_NUM, MYSQLI_BOTH (default)
     * @param string $class //name of the class to fill data inn (make sure it is included)
     * @return object objs[]
     */
    //TODO: lage egen klasse for params klassen. Query klasse.
    function getData($params, $result_type= MYSQLI_ASSOC, $class = null) {
		
        if ($this->connect_errno) {
            return false;
        }

        $this->clearResult();

        //SELECT
        if (isset($params['select'])) {
            if(is_array($params['select'])){
                $select = '';
                foreach ($params['select'] as $arg) {
                    $select .= $this->escape_string($arg) . ",";
                }
                $select = substr($select, 0, -1); //removing last comma
            }else {
                $select = $this->escape_string($params['select']);
            }
        } else {
            $select = "*";
        }
        $select = "SELECT " . $select;

        //FROM
        if (isset($params['from'])) {
            $from = '';
            if(is_array($params['from'])) {
                foreach ($params['from'] as $arg) {
                    if($this->prefix) $arg = $this->prefix . $arg;
                    $from .= $this->escape_string($arg) . ",";
                }
                $from = substr($from, 0, -1); //removing last comma
            }
            else{
                $fromTable = $params['from'];
                if($this->prefix) $fromTable = $this->prefix . $params['from'];
                $from = $this->escape_string($fromTable);
            }
        } else {
            $this->setError("You need to select at least one table");
            return false;
        }
        $from = " FROM " . $from;

        //LEFT JOIN OR JOIN
        $leftjoin = '';
        $join = '';
        if(isset($params['leftjoin'])){
            $joinExists = true;
            $joinNr = '';
            while ($joinExists){
                $joinTableName = $params["leftjoin$joinNr"];
                if($this->prefix) $joinTableName = $this->prefix . $joinTableName;
                $leftjoin .= " LEFT JOIN " . $this->escape_string($joinTableName);
                if(isset($params["on$joinNr"])){
                    if(is_array($params["on$joinNr"])){
                        foreach($params["on$joinNr"] as $key => $value){
                            $leftjoin .= " ON " . $this->escape_string($key) . "=" . $this->escape_string($value) . ",";
                        }
                        $leftjoin = substr($leftjoin, 0, -1);//removing last point
                    }else{
                        $leftjoin .= " ON " . $this->escape_string($params["on$joinNr"]);
                    }
                }else{
                    $this->setError("Found 'JOIN$joinNr' but no 'ON$joinNr' was given. Ending query.");
                    return false;
                }
                //$this->log->debug("Starting to increase joinNr from $joinNr");
                //increasing joinNext value
                if($joinNr == ''){
                    $joinNr = 1;
                }else{
                    $joinNr++;
                }
                //$this->log->debug("increased joinNr to $joinNr");
                //lets check if joinNext exists.

                if(!isset($params["join$joinNr"])){
                    $joinExists = false;
                }
                //$this->log->debug("Done checking if joinNr is set:" . isset($params["join$joinNr"]));
            }
        }elseif(isset($params['join'])){
			$joinExists = true;
			$joinNr = '';
			while ($joinExists){

                $joinTableName = $params["join$joinNr"];
                if($this->prefix) $joinTableName = $this->prefix . $joinTableName;

				$join .= " JOIN " . $this->escape_string($joinTableName);
				if(isset($params["on$joinNr"])){
					if(is_array($params["on$joinNr"])){
						foreach($params["on$joinNr"] as $key => $value){
							$join .= " ON " . $this->escape_string($key) . "=" . $this->escape_string($value) . ",";		
						}
						$join = substr($join, 0, -1);//removing last point
					}else{
						$join .= " ON " . $this->escape_string($params["on$joinNr"]);
					} 
				}else{
					$this->setError("Found 'JOIN$joinNr' but no 'ON$joinNr' was given. Ending query.");
					return false;
				}
				//$this->log->debug("Starting to increase joinNr from $joinNr");
				//increasing joinNext value
				if($joinNr == ''){
					$joinNr = 1;
				}else{
					$joinNr++;
				}
				//$this->log->debug("increased joinNr to $joinNr");
				//lets check if joinNext exists.
				
				if(!isset($params["join$joinNr"])){
					$joinExists = false;
				}
				//$this->log->debug("Done checking if joinNr is set:" . isset($params["join$joinNr"]));
			}
		}
		
        //WHERE
        if (isset($params['where'])) {
            $where = '';
            if(is_array($params['where'])){
                foreach ($params['where'] as $key => $value) {
                    if(is_array($value)){
                        $where .= $this->escape_string($key) . " IN (";
                        foreach($value as $v){
                            $where .= $this->escape_string($v) . ",";
                        }
                        $where = substr($where, 0, -1);//removing last point
                        $where .= ")";
                        $where .=  " AND ";
                    }
                    else {
                        $where .= $this->escape_string($key) . "='" . $this->escape_string($value) . "' AND ";
                    }
                }
                $where = substr($where, 0, -5); //removing " AND"
            }else {
                $where = $this->escape_string($params['where']);
            }

            $where = " WHERE $where";
        } else {
            $where = '';
        }

        //GROUP BY
        if(isset($params['groupby'])){
            $groupby = " GROUP BY " . $this->escape_string($params['groupby']);
        }else{
            $groupby = '';
        }

        //ORDER BY
        if (isset($params['orderby'])) {
            $orderby = " ORDER BY " . $this->escape_string($params['orderby']);
        } else {
            $orderby = '';
        }

        //LIMIT
        if (isset($params['limit'])) {
            $limit = " LIMIT " . $this->escape_string($params['limit']);
        } else {
            $limit = '';
        }

        //OFFSET
        if (isset($params['offset'])) {
            $offset = " OFFSET " . $this->escape_string($params['offset']);
        } else {
            $offset = '';
        }


        $this->sql = $select . $from . $leftjoin . $join . $where . $groupby . $orderby . $limit . $offset;
        //TODO: Cache?
        $this->log->debug("Got SQL: " . $this->sql);
        $this->executeSql();

        return $this->getResult($result_type, $class);
    }

    function getResult($result_type= MYSQL_ASSOC, $class = null){
        if ($this->result) {
            if ($class == null){
                $objs = array();
                while ($row = $this->result->fetch_array($result_type) ){
                    $objs[] = $row;
                }
                return $objs;
            }
            else {
                $objs = array();
                while ($row = $this->result->fetch_object($class) ){
                    $objs[] = $row;
                }
                return $objs;
            }
        } else {
            return false;
        }
    }

    function getDataById($table, $id){
        $params = array();
        $params['from'] = $table;
        $params['where'] = array("id"=>(int)$id);
        $results = $this->getData($params);
        if(isset($results[0])){
            return $results[0];
        }else return false;
    }

    function updateDataById($table, $item, $id) {
        if($this->prefix) $table = $this->prefix . $table;
        //Prepare new SQL execution
        $this->clearResult();
        if (isset($item['id'])) {
            unset($item['id']);
        }
        $this->sql = "UPDATE $table SET ";
        foreach ($item as $key => $val) {
            $key = $this->escape_string($key);
            $val = $this->escape_string($val);

            if ($val == null) {
                $this->sql .= "$key=NULL,";
            } else {
                $this->sql .= "$key='$val',";
            }
        }
        $this->sql = substr($this->sql, 0, -1); //remove last comma
        $this->sql .= " WHERE id = $id";

        $this->log->debug("Got SQL: " . $this->sql);
        return $this->executeSql();
    }

    function deleteDataById($table, $id) {
        if($this->prefix) $table = $this->prefix . $table;
        //Prepare new SQL execution
        $this->clearResult();
        $itemId = (int)$id;
        $this->sql = "DELETE FROM $table WHERE id = $itemId";

        $this->log->debug("Got SQL: " . $this->sql);
        return $this->executeSql();
    }

    /**
     * Properly clears previous <b>result</b> and <b>db_error</b> variables.
     */
    private function clearResult() {

        if (is_object($this->result)) {
            $this->result->free();
        }
        if (isset($this->db_error)) {
            unset($this->db_error);
        }
    }

    /**
     * Executes SQL that is stored in <b>sql</b> variable.<br>
     * Sets result into <b>result</b> variable.<br>
     * Logs errors into <b>cms_error</b> variable if any occur.
     */
    private function executeSql() {
        if (!$this->result = $this->query($this->sql)) {
            $this->setError("Failed to execute SQL:" . $this->sql . " error (" . $this->errno - ") " . $this->error);
            $this->log->error("Failed to execute SQL:" . $this->sql . " error (" . $this->errno - ") " . $this->error);
        }
        return $this->result;
    }

    private function setError($error) {
        if (!isset($this->db_error)) {
            $this->db_error = '';
        }
        $this->db_error .= "$error\n\n";
    }

    /**
     * Returns id of the item that was inserted or false if sql failed.
     * @param $table name of the table.
     * @param $item - array(name=>value, name1=>value1)
     * @return bool|mixed
     */
    function insertData($table, $item) {
        if($this->prefix) $table = $this->prefix . $table;
        $this->clearResult();
        $table = $this->escape_string($table);
        $this->sql = "INSERT INTO $table ";
        $keys = "(";
        $vals = "VALUES (";
        $first = true;
        foreach ($item as $key => $val) {
            $key = $this->escape_string($key);
            $val = $this->escape_string($val);
            if ($val != '' || $val != null) { // otherwise we use database standards.
                if($first){
                   $keys .= "$key";
                   $vals .= "'$val'"; 
                   $first = false;
                }else{
                    $keys .= ",$key";
                    $vals .= ",'$val'";
                }
            }
        }
        $keys .= ")";
        $vals .= ")";
        // $keys .= "insert_time)";
        //$vals .= "NOW())";
        $this->sql .= "$keys $vals";
        $this->log->debug("Got SQL: " . $this->sql);
        if($this->executeSql()){
            return $this->lastInsertId();
        }else{
            return false;
        }
    }

    function lastInsertId(){
        return $this->insert_id;
    }

    function __destruct() {
        if(mysqli_connect_errno()){
            //var_dump(mysqli_connect_errno());
        }   else{
            $this->close(); // we close connection only if one is open.
        }
    }

}
