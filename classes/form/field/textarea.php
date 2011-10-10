<?php
/**
* Form Field
*/
class Form_Field_Textarea extends Form_Field
{
	
	public function render_input($attributes = null)
	{
		return Form::textarea($this->html_name(), $this->value(), $this->html_attributes($attributes));
	}
}