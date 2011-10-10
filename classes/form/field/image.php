<?php
/**
* Form Field
*/
class Form_Field_Image extends Form_Field
{
	public $_path;

	public function init($options)
	{
		$this->options += array("path");	

		return parent::init($options);
	}

	public function render_input($attributes = null)
	{
		return strtr('<div class="image-field">:image :input</div>', array(
			":image" => $this->value() ? HTML::image($this->path().$this->value()) : '<div class="image-placeholder"></div>', 
			":input" => Form::file($this->name(), $this->html_attributes($attributes))
		));
	}

	/**
	 * Sets and gets the path string.
	 *
	 * @param   string   $path  Label of this field
	 * @return  mixed
	 */
	public function path($path = NULL)
	{
		if ($path === NULL)
		{
			// Act as a getter
			return $this->_path;
		}

		// Act as a setter
		$this->_path = (string) $path;

		return $this;
	}	


}