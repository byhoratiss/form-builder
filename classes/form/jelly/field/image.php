<?php
/**
* Form Field
*/
class Form_Jelly_Field_Image extends Form_Jelly_Field
{
	public $_path;
	public $_thumbnail;

	public function init($options)
	{
		$this->options = array_merge($this->options, array("path", "thumbnail"));	

		$options = Arr::merge(array('path' => ltrim($this->field()->path, DOCROOT)), $options);

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

	/**
	 * Sets and gets the thumbnail string.
	 *
	 * @param   string   $path  thumbnail of this field
	 * @return  mixed
	 */
	public function thumbnail($thumbnail = NULL)
	{
		if ($thumbnail === NULL)
		{
			// Act as a getter
			return $this->_thumbnail;
		}

		// Act as a setter
		$this->_thumbnail = (string) $thumbnail;

		return $this;
	}		


}