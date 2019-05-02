<?php

/**
 * When Completed, assimilate into V3 idea framework
 * 
 * Class to build html forms and allow for classes to overide
 * with specifcs as needed
 * 
 */


class form {
	
	var $form_stack = array();
	var $object = null;
	
	var $method_prefix = '';
	
	function __construct(&$object = null)
	{
		$this->object = $object;
		
		$columns = array();
		
		if($object != null )
		{	
			if( is_subclass_of($object,'db_mysql_table'))
			{	$this->method_prefix = "_mysql";	}
			
			foreach( $object->_form_inputs() as $key => $value )
			{
				$columns[$key] = $value;
			}
		}
		
		foreach($columns as $key => $value)
		{
			$this->_insert($key, $value);
		}
		
	}
	
	function _generalize(&$columns)
	{
		return $columns;
	}
	
	function _insert($key, $value)
	{	
		if( array_key_exists('COLUMN_NAME', $value) )
			$value['value'] = $this->object->$value['COLUMN_NAME'];
		if( !isset($value['element']) )
		{
			var_dump($value);
			return;
		}
		$this->{"_element_".$value['element']}($value);
		$this->form_stack[] = $value;
	}
	
	function show()
	{
		$uri = configurator::uri_parts();
		$html = "";
		$form_settings = $this->form_elements();
		foreach( $this->form_stack as $key => $value )
			$html .= $value['html'];
		return $this->_element_form(array('FORM_ELEMENTS'=>$html,'ACTION'=>"",'METHOD'=>'POST'));
	}
	
	function form_elements()
	{
		return array();
	}
	
	function get_template($path='',$settings)
	{
		$template = new template($path);
		$template->loadFile();
		$template->loadTerms($settings);
		return $template->replace();
	}
	
	function _element_form($settings)
	{
		$path_to_template = WORKING_CONTROLLER.get_class($this->object).'/form/form_mysql'.TMPL_END;
		return $this->get_template($path_to_template,$settings);
	}
	
	function _element_input(&$settings)
	{
		$path_to_template = WORKING_CONTROLLER.get_class($this->object).'/form/element_input'.TMPL_END;
		$settings['html'] = $this->get_template($path_to_template,$settings);
	}
	
	function _element_text(&$settings)
	{
		$path_to_template = WORKING_CONTROLLER.get_class($this->object).'/form/element_text'.TMPL_END;
		$settings['html'] = $this->get_template($path_to_template,$settings);
	}
	
}