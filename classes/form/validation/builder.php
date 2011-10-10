<?php
/**
* Generate form()
*/
class Form_Validation_Builder extends Form_Builder
{
	protected $_object = null;

	function __construct($object, $error_file = null)
	{
		$this->object($object);
		parent::__construct($object->as_array(), $error_file);
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
			$this->_errors = $this->object->errors($this->_error_file);
			return false;
		}
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


}