<?php
namespace ideamanagement\library;

/**
 * Contains all of the function needed for a 
 * mysql data design to auto-buid input forms
 */

class _form_mysql extends _form
{

	static function _form_inputs($arguments, $object)
	{
		$columns = $object->_getColumnsExtended();
		
		foreach($columns as $key => $column)
		{
			$args = preg_match("/(\w+)\((\w+)\)/", $column['COLUMN_TYPE'], $matches);
			if( $args == true)
			{
				$settings = $object->{$matches[1]}($matches[2]);
			} else {
				$settings = $object->{$column['COLUMN_TYPE']}();
			}
			
			if( isset($settings) && is_array($settings) )
			{
				$column = array_merge($settings,$column);
			}
			
			foreach($column as $_name => $_value)
			{
				if( !array_key_exists(self::translate($_name), $column) )
				{	$column[ self::translate($_name) ] = $_value;	}
			}
			
			if( $column['COMMENT'] == null )
				$column['COMMENT'] = $column['NAME'];
			
			$column['COMMENT'] = ucfirst($column['COMMENT'] );
			$columns[$key] = $column;
			unset($settings);
		}
		
		return $columns;
	}
	
	static function translate($word)
	{
		$lexicon = array(
			'COLUMN_NAME' => 'NAME',
			'DATA_TYPE' => 'TYPE',
			'COLUMN_TYPE' => 'TYPE',
			'COLUMN_COMMENT' => 'COMMENT',
			'CHARACTER_MAXIMUM_LENGTH' => 'MAX_LENGTH'
		);
		
		return (array_key_exists($word, $lexicon) ? $lexicon[$word] : $word);
		
	}
	/**
	 * MySql Interpretations
	 */
	static function int($length)
	{	
		return array('element'=>'input','TYPE'=>'number','MAX_LENGTH'=>(int) $length[0]);
	}
	
	static function varchar($length)
	{	return array('element'=>'input','TYPE'=>'text','length'=>(int) $length[0]);	}
	
	static function text()
	{	return array('element'=>'text','MAX_LENGTH'=>65535); /*Mysqls default value*/	}
	
	static function date()
	{
		return array('element'=>'input', 'TYPE'=>'date');
	}
	
}