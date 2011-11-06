<?php
/**
* Generate form()
*/
class Form_Builder_Jelly extends Form_Builder_Validation
{
	public function widget($name)
	{
		$widget = parent::widget($name);

		if( ! is_array($name) AND $field = $this->_object->meta()->field($name))
		{
			$widget->attributes(self::html5_rules($field->rules));
		}
		return $widget;
	}	

	static public function html5_rules(array $rules)
	{
		$validation = array();
		foreach($rules as $rule)
		{
			switch($rule[0])
			{
				case 'not_empty':
					$validation['required'] = '';
					break;

				case 'range':
					$validation['min'] = $rule[1][1];
					$validation['max'] = $rule[1][2];
					break;

				case 'min_length':
					$validation['minlength'] = $rule[1][1];
					break;

				case 'max_length':
					$validation['maxlength'] = $rule[1][1];
					break;

				case 'regex':
					$validation['pattern'] = $rule[1][1];
					break;
			}
		}
		return $validation;		
	}

	function __construct(Jelly_Model $object, $data = null)
	{
		$this->object($object);
		$this->data(Arr::merge($object->as_array(), (array) $data));
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

			$this->_errors = null;

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

			$this->data(Arr::merge((array) $this->_data, $this->_object->as_array() ));

			return false;
		}
	}

}