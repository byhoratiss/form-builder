<?php
/**
* Form Field
*/
class Form_Jelly_Field_File extends Form_Jelly_Field
{
	
	public function render_input($attributes = null)
	{
		return Form::file($this->html_name(), $this->html_attributes($attributes));
	}
}