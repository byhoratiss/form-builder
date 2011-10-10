<?php
/**
* Form Field
*/
abstract class Form_Jelly_Field extends Form_Field
{
	protected $_field;

	function __construct($field, $options = null)
	{
		if ( ! ($field instanceof Jelly_Field))
			throw new Kohana_Exception("The first parameter must be a Jelly_Field");

		$this->name($field->name);
		$this->field($field);	
		$this->init((array) $options);
	}

	protected function html_attributes($attributes = null, $custom_attributes = null)
	{
		return Arr::merge($this->html_validation(), parent::html_attributes($attributes, $custom_attributes));
	}	

	public function html_validation()
	{
		$validation = array();
		foreach($this->_field->rules as $rule)
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

	/**
	 * Sets and gets the field string.
	 *
	 * @param   string   $field  Jelly_Field
	 * @return  mixed
	 */
	public function field($field = NULL)
	{
		if ($field === NULL)
		{
			// Act as a getter
			return $this->_field;
		}

		// Act as a setter
		$this->_field = $field;

		return $this;
	}

}