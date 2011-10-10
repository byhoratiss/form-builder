<?php
/**
* Generate form()
*/
class Form_Jelly_Builder extends Form_Builder
{
	protected $_object = null;

	function __construct($object, $data = null, $error_file = null)
	{
		$this->object($object);
		
		parent::__construct(Arr::merge($object->as_array(), (array) $data), $error_file);
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

	public function field($field_type, $field, $options = null)
	{
		if($field instanceof Jelly_Field)
		{
			$class = "Form_Jelly_Field_".ucfirst($field_type);
			return new $class($field, Arr::merge($this->field_options($field->name), (array) $options ));
		}
		else
		{
			return parent::field($field_type, $field, $options);
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