<?php
/**
* Form Field
*/
class Form_Field_Select extends Form_Field
{
	protected $required = array("choices");

	public function render_input($attributes = null)
	{
		return Form::select($this->html_name(), $this->value(), $this->options['choices'], $this->html_attributes($attributes));
	}
}