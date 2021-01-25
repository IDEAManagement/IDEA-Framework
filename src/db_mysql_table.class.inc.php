<?php
namespace ideamanagement\library;

use ideamanagement\library\authentication;
use ideamanagement\library\database_error;
use IteratorAggregate;

/**
 * DB Mysql Table Abstract
 * 
 * 
 */

Abstract class db_mysql_table implements IteratorAggregate
{
	protected $table_name = "";
	protected $primary_key = "id";
	protected $dbo;
	
	protected $_select = '';

	
	protected  $array = array();
	
	function __construct($db_resource, $db_table)
	{
		$this->dbo = $db_resource;
		$this->table_name = $db_table;
		$this->_select = " SQL_CALC_FOUND_ROWS * "; //used to know number of rows when pulling a set.
	}
	
	/**
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 * + Methods to Operate on the Table
	 * +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
	 */
	/**
	 * Create a new Row in the table
	 */
	function create($db_mysql_table_row)
	{
		return $this->update(null,$db_mysql_table_row);
	}
	
	/**
	 * 
	 */
	function read($where='',$orderby='',$offset='',$limit='',$select='',$groupby='',$having='')
	{	
		/*
		!SELECT
		**[ALL | DISTINCT | DISTINCTROW ]
		**[HIGH_PRIORITY]
		**[STRAIGHT_JOIN]
		**[SQL_SMALL_RESULT] [SQL_BIG_RESULT] [SQL_BUFFER_RESULT]
		**[SQL_CACHE | SQL_NO_CACHE] [SQL_CALC_FOUND_ROWS]
		!select_expr [, select_expr ...]
		![FROM table_references
		![WHERE where_condition]
		![GROUP BY {col_name | expr | position}
		***[ASC | DESC], ... [WITH ROLLUP]]
		*[HAVING where_condition]
		[ORDER BY {col_name | expr | position}
		[ASC | DESC], ...]
		[LIMIT {[offset,] row_count | row_count OFFSET offset}]
		[PROCEDURE procedure_name(argument_list)]
		[INTO OUTFILE 'file_name' export_options
		| INTO DUMPFILE 'file_name'
				| INTO var_name [, var_name]]
				[FOR UPDATE | LOCK IN SHARE MODE]]
		*/
		
		$query = ( preg_match("/SELECT | select/", $this->_select) ? '' : ' SELECT '). " {$this->_select} FROM `{$this->table_name}` ";
		$query .= ($this->_join!='' ? "left join ".$this->_join : '');
		$query .= ($where!=''? "WHERE ".$where.' ':'');
		$query .= ($groupby!=''?" GROUP BY ".$this->dbo->real_escape_string($groupby)." ":'');
		$query .= ($having!=''?" HAVING ".$this->dbo->real_escape_string($having)." ":'');
		$query .= ($orderby!=''?" ORDER BY ".$this->dbo->real_escape_string($orderby)." ":'');
		$query .= ($limit!=''?" LIMIT ".(int) $limit.($offset!=''?" OFFSET ".(int)$offset:'' ).' ':' ');
		
		if( DEBUG_MODE == 'db' ) echo "<pre>{$query}</pre>";
		//if( DEBUG_MODE == true ){ header("X-Query: [{$query}]", false ); }
		
		if( defined("DEBUG_MODE") && DEBUG_MODE == "backtrace") {
			$_debug = debug_backtrace(); $debug = array();
			foreach( $_debug as $entry => $array){	$debug[$entry] = $array['line']." ".$array['file'];	}
			trigger_error(__METHOD__." in ".__FILE__." <pre>".print_r($debug,true).'</pre>',E_USER_NOTICE);
		}
		
		return $this->dbo->query($query);
		
	}
	
	function found_rows()
	{
		if (strpos( $this->_select, "SELECT SQL_CALC_FOUND_ROWS" ) !== false )
		{
			$query = "SELECT FOUND_ROWS() AS `found_rows`;";
			$result = $this->dbo->query($query);
			$row = $result->fetch_array(MYSQL_ASSOC);
			return $row['found_rows'];
		}
	}
	
	/**
	 * Update a row in the table
	 * 
	 * @param $id_row unique primary key
	 * @param $data Aray keys match db columns
	 */
	function update($id,$data=array(),$force_insert=false)
	{
		$debug = "";
		 
		if( $id != null && $force_insert != true)
		{
			$this->_select = "SELECT {$this->primary_key} ";
			$result = $this->read("{$this->primary_key}='{$id}'",'','','1');
		}
		
		//Process Out $data
		$columns = $this->_getColumnsExtended();
		
		$set = '';
		var_dump($columns);
		foreach ($columns as $column => $columns_details)
		{
			if( !isset($data[$column]) || $data[$column] == '' ) continue ;
			
			if( $set != '' )
				$set .= ', ';
			/*Data Type Conversion for tinyints to be 1 or 0*/			
			if( $columns_details['DATA_TYPE'] == 'tinyint' ) 
				$data[$column] = (int) (bool) $data[$column];
			
			/*We need to do better with this - maybe prior to saving but its a fail safe*/
			if( $column == authentication::secret() )
				$data[$column] = authentication::hash($data[$column]);
			
			$set .= "`{$column}`=".
				($data[$column][0] == '~' 
						? str_replace('~','',$data[$column])
						: "'".$this->dbo->real_escape_string($data[$column])
				."'" );
		}
		
		if( $id != null && $force_insert != true  && ($result && $result->num_rows > 0) )
		{
			//Then We can update
			
			$action = "UPDATE ";
			$where = " WHERE `{$this->primary_key}` = '{$id}' "; //was $data[$this->primary_key]
			
		} elseif( $force_insert != true ) {
			//We will focus on a new row
			$action = "REPLACE INTO ";
			$where = "";
		} else {
			//Assume a forced insert
			$action = "INSERT INTO ";
			$where = ", `{$this->primary_key}` = '{$id}'";
		}
		
		$query = $action." `{$this->table_name}` SET {$set} {$where};";

		/* INSERT THIS ACTION FOR AUDIT - must be allowed to fail */
		$this->audit($action,$query);
		
		$result = $this->dbo->query($query);
		if( $result === false ){
			$error_list = $this->dbo->error_list;
			throw new database_error($error_list[0]['error'],$error_list[0]['errno']);
			//throw new app_error("Mysql {$action} Failed on {$query}");
		}
		$insert_id = $this->dbo->insert_id;
		return ( $insert_id ? $insert_id : $id );
	}
	
	/**
	 * Delete a Row in the table
	 */
	function delete($id)
	{
		
	}
	
	
	/**
	 * Primary Key ID Search
	 * @param id $value
	 */
	function find($value)
	{	
		$find = $this->dbo->real_escape_string($value);
		$result = $this->read("{$this->primary_key}='{$find}'");
		return $result;
		
	}
	
	/**
	 * We can Either manually set in the extended object
	 * OR more painfully (debugging only really) get them directly
	 * - Doing this normally is a waste of time
	 */
	function _getColumns()
	{
		if( !empty($this->table_columns) && count($this->table_columns) > 0 )
			return $this->table_columns;
		
		$this->_getColumnsExtended();
		
		$this->table_columns = array();
		
		foreach($this->columns as $column)
			$this->table_columns[] = $column[ 'COLUMN_NAME' ];
		
		return $this->table_columns;
	}
	
	function _getColumnsExtended()
	{
		if( !empty($this->columns) && count($this->columns) > 0 )
			return $this->columns;
		$index = $this->_getIndexes();
		
		$this->columns = $columns = array();
		
		$query = "SELECT `COLUMN_NAME`, `DATA_TYPE` , `CHARACTER_MAXIMUM_LENGTH`, `COLUMN_COMMENT`,
						  `EXTRA`, `COLUMN_TYPE`
		FROM `INFORMATION_SCHEMA`.`COLUMNS`
		WHERE `TABLE_SCHEMA`='{$this->dbo->getdbname()}'
		AND `TABLE_NAME`='{$this->table_name}';";
	
		$result = $this->dbo->query($query);
		while (($column = $result->fetch_array( MYSQLI_ASSOC )) != NULL)
		{	
			if( key_exists($column['COLUMN_NAME'], $index) )
			{
				$column['INDEX'] = $index[ $column['COLUMN_NAME'] ]['Key_name'];
			}
			
			$columns[ $column['COLUMN_NAME'] ] = $column;
		}
		
		$this->columns = $columns;
		return $this->columns;
	}
	
	function _getIndexes()
	{
		$index_list = array();
		
		if( !empty($this->index) && count($this->index) > 0 )
			return $this->index;
		
		$query = "SHOW INDEX FROM {$this->table_name}";
		$result = $this->dbo->query($query);
		
		while( ($row = $result->fetch_array(MYSQLI_ASSOC) ) != null )
		{
			$index_list[$row['Column_name']] = $row; 
		}
		
		return $index_list;
	}
	
	/**
	 *
	 * @param $name
	 */
	public function  __get($name)
	{
		if( method_exists($this,"get$name") )
		{
			$method = "get$name";
			return $this->$method();
		} else {
			if( array_key_exists($name, $this->array) )
				return $this->array[$name];
			elseif( $name == 'array'){
				return $this->array;
			} else 
				return null;
		}
	}
	
	
	/**
	 *
	 * @param $name
	 * @param $value
	 */
	public function  __set($name,$value)
	{
		if( method_exists($this,"set$name") )
		{
			$method = "set$name";
			$this->$method($value);
		} else {
			$this->array[$name] = $value;
		}
	}
	
	public function getIterator()
	{
		return new ArrayIterator($this->array);
	}

    private function audit($action, $query) {
    }
}
