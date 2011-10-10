<?php
/**
* Form Field
*/
class Form_Jelly_Field_Datetime extends Form_Jelly_Field
{
	
	public function render_input($attributes = null)
	{
		return Form::input($this->html_name(), $this->value(), $this->html_attributes($attributes, array('type' => 'date')));
	}
}