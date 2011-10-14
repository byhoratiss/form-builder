<?php
/**
* Generate form()
*/
class Form_Jelly_Builder extends Form_Builder
{
	protected $_object = null;
	protected $_error_file = null;
	protected $_errors = null;

	public function field($render, $name, $options = null, $attributes = null )
	{
		$options = Arr::merge(
			array(
				"errors" => $this->errors($name),
				"object" => $this->_object,
			),
			(array) $options
		);
		$this->renderer()->field($render, $name, $this->value($name), (array) $options, $attributes );
	}	

	function __construct(Jelly_Model $object, $data = null)
	{
		$this->object($object);
		
		parent::__construct(Arr::merge($object->as_array(), (array) $data));
	}

	public function save()
	{
		$this->_object->save();
		return $this;
	}

	public function check($save = FALSE, $extra_validation = null)
	{
		$this->_object->set($this->_data);

		try{
			$this->_object->check($extra_validation);

			if($save)
			{
				$this->save();
			}
			
			$this->data(Arr::merge((array) $this->_data, $this->_object->as_array() ));

			return true;
		}
		catch(Jelly_Validation_Exception $e)
		{
			$this->_errors = $e->errors($this->_error_file);
			return false;
		}
	}

	public function errors($name = null)
	{
		return $name ? (is_array($name) ? Arr::extract($this->_errors, $name) : Arr::get($this->_errors, $name)) : $this->_errors;
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

	public function object($object = null)
	{
		if( $object !== null)
		{
			if( ! ($object instanceof Jelly_Model))
				throw new Kohana_Exception("Object is type :type must be an instance of Validation", array(":type" => get_class($object)));

			$this->_object = $object;
			return $this;
		}
		return $this->_object;
	}

}