<?php
namespace ideamanagement\library;

use Countable;

/**
 * This class is basically an empty framework for simple access
 * to mysql views to list results using standard functions / defintions
 */


class view_container extends db_mysql_row implements Countable 
{
	protected $table_name = "view_*";
	protected $primary_key = "id";
	public $id; 
	
	/**
	 * 
	 * @param string $id_organization
	 */
	function __construct($table_name='view',$primary_key='id',$id=null)
	{
		$this->table_name = $table_name;
		$this->primary_key = $primary_key;
		
		$this->dbo = DATASOURCE::DB()->controller();
		parent::__construct($this->dbo,$this->table_name);
		
		if( $id != null)
		{
			$this->id = $id;
			$this->_list();
		}
	}
	
	public function _list()
	{
		if( $this->id != null )
		{
			$result = $this->find($this->id);
			while( ($row = $result->fetch_array(MYSQL_ASSOC)) != null)
			{
				$this->array[] = $row;
			}
		}
	}
	
	public function search($where='',$orderby='',$offset='',$limit='')
	{
		$result = parent::read($where,$orderby,$offset,$limit);
		while( ($row = $result->fetch_array(MYSQL_ASSOC)) != null)
		{
			$this->array[] = $row;
		}
	}
	
	public function count()
	{
		return count($this->array);
	}
	
	/**
	 * Primary Key ID Search
	 * @param id $value
	 */
	function find($value)
	{
		$find = $this->dbo->real_escape_string($value);
		$result = $this->read("{$this->primary_key} LIKE '{$find}'");
		return $result;
	
	}
	
}