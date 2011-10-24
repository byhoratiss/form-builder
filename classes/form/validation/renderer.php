<?php
/**
* Form Form_Renderer
*/
class Form_Validation_Renderer extends Form_Renderer
{
	protected $_template = array(
		'<div class="row :type-field :name-row :with-errors">:label:render:errors</div>',
		'checkbox' => '<div class="row :type-field :name-row :with-errors">:render:label:errors</div>',
	);

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
			return '';

		$errors = is_array($errors) ? join(", ", $errors) : $errors;

		return '<span class="field-errors">'.$errors.'</span>';
	}
}