<?php
/**
* Generate form()
*/
class Form_Validation_Builder extends Form_Builder
{
	protected $_object = null;
	protected $_error_file = null;
	protected $_errors = null;

	function __construct(Validation $object)
	{
		$this->object($object);

		parent::__construct($object->as_array());
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

	public function field($render, $name, $options = null, $attributes = null)
	{
		$options = Arr::merge(
			array(
				"errors" => $this->errors($name),
				"object" => $this->_object,
			),
			(array) $options
		);
		$this->renderer()->field($render, $name, $this->value($name), $options);
	}

	public function errors($name = null)
	{
		return $name ? (is_array($name) ? Arr::extract($this->_errors, $name) : Arr::get($this->_errors, $name)) : $this->_errors;
	}	
	
	public function object($object = null)
	{
		if( $object !== null)
		{
			if( ! ($object instanceof Validation))
				throw new Kohana_Exception("Object is type :type must be an instance of Validation", array(":type" => get_class($object)));

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