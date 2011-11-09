<?php
/**
 * Generate form()
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerinerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Builder_Validation extends Form_Builder
{
	protected $_object = null;
	protected $_error_file = null;
	protected $_errors = null;

	public function widget($name)
	{
		$widget = new Form_Widget_Object($name, $this->_object);

		return $widget->set(array(
			'prefix' => $this->_prefix,
			'errors' => $this->errors($name),
			'value' => $this->value($name),
		));
	}	

	function __construct($object)
	{
		$this->object($object);
		$this->data($object->as_array());
	}

	public function check($extra_validation = null)
	{
		if($this->_object->check())
		{
			$this->data(Arr::merge((array) $this->_data, $this->_object->as_array() ));
			return true;
		}
		else
		{
			$this->_errors = $this->_object->errors($this->_error_file);
			return false;
		}
	}	

	public function errors($name = null)
	{
		return $name ? (is_array($name) ? Arr::extract($this->_errors, $name) : Arr::get($this->_errors, $name)) : $this->_errors;
	}	
	
	public function object($object = null)
	{
		if( $object !== null)
		{
			$this->_object = $object;
			return $this;
		}
		return $this->_object;
	}

	public function error_file($error_file = null)
	{
		if( $error_file !== null)
		{
			$this->_error_file = (string) $error_file;
			return $this;
		}
		return $this->_error_file;
	}	


}