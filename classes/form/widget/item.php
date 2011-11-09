<?php
/**
 * HTML Attributes
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Widget_Item
{
	protected $_name;
	protected $_value;
	protected $_errors;

	function __construct($name, $value = null, $prefix = null, $errors = null) 
	{
		$this->_name = $name;
		$this->_value = $value;
		$this->_prefix = $prefix;
		$this->_errors = $errors;
	}

	public function field_name()
	{
		return $this->_name;
	}

	public function name( )
	{
		return sprintf($this->_prefix, $this->_name);
	}

	public function id( )
	{
		return str_replace("]", "", str_replace("[", "_", $this->name()));
	}

	public function value()
	{
		return $this->_value;
	}	

	public function errors()
	{
		return $this->_errors;
	}	

	public function label($label = null)
	{
		return Form::label( $this->id(), $label ? $label : ucfirst(Inflector::humanize($this->field_name())));
	}
		
	public function prefix($prefix = null)
	{
		if( $prefix !== null)
		{
			$this->_prefix = (string) $prefix;
			return $this;
		}
		return $this->_prefix;
	}	

}