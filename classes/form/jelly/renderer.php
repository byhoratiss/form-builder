<?php
/**
* Form Form_Renderer
*/
class Form_Jelly_Renderer extends Form_Renderer
{
	protected $_template = array(
		'<div class="row :type-field :name-row :with-errors">:label:render:errors</div>',
		'checkbox' => '<div class="row :type-field :name-row :with-errors">:render:label:errors</div>',
	);	

	public function jelly_field($field_name)
	{
		return ($this->_builder->object() AND ($this->_builder->object() instanceof Jelly_Model)) ? $this->_builder->object()->meta()->field($field_name) : null;
	}

	public function html_validation($field)
	{
		$validation = array();
		foreach($field->rules as $rule)
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

	public function html_attributes($name, $attributes = null, $custom_attributes = null)
	{
		$field = $this->jelly_field($name);

		return Arr::merge(
			(array) $attributes, 
			$field ? $this->html_validation($field) : array(),
			array('name' => $this->html_name($name), 'id' => $this->html_id($name)), 
			(array) $custom_attributes
		);
	}

	public function parameters($name, $options)
	{
		$errors = $this->_builder->errors($name);

		return array_merge(
			array(
				':errors' => $this->errors($errors), 
				':with-errors' => $errors ? 'with-errors' : '',
			), 
			parent::parameters($name, $options)
		);
	}	

	/**
	 * render errors 
	 * 
	 * @return void
	 * @author 
	 **/
	public function errors($errors)
	{
		if( ! $errors )
		{
			return '';
		}

		$errors = is_array($errors) ? join(", ", Arr::flatten($errors)) : $errors;

		return '<span class="field-errors">'.$errors.'</span>';
	}


	/**
	 * WIDGETS
	 * =========================
	 */

	public function select($name, $value, $options, $attributes = null)
	{
		$options = $this->options($options, array('choices', 'include_blank'), array('choices'));

		if($options['choices'] instanceof Jelly_Builder)
		{
			$choices = array();
			foreach ($options['choices']->limit(100)->select() as $choice) {
				$choices[$choice->id()] = $choice->name();
			}
			$options['choices'] = $choices;
		}

		return parent::select($name, $value, $options, $attributes);
	}



}