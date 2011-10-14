<?php
/**
* Form Form_Renderer
*/
class Form_Validation_Renderer extends Form_Renderer
{
	protected $_template = '<div class="row :name-row :with-errors">:label:render:errors</div>';
	protected $_template_checkbox = '<div class="row :name-row :with-errors">:render:label:errors</div>';

	protected function parameters($name, $options)
	{
		$errors = $this->_builder->errors($name);

		return array_merge(
			array(
				':errors' => $this->errors($errors), 
				':with-errors' => $errors ? 'with-errors' : '',
			), 
			parent::parmaters($name)
		);
	}	

	/**
	 * render errors 
	 * 
	 * @return void
	 * @author 
	 **/
	protected function errors($errors)
	{
		if( ! $errors )
			return '';

		$errors = is_array($errors) ? join(", ", $errors) : $errors;

		return '<span class="field-errors">'.$errors.'</span>';
	}
}