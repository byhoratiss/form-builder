<?php
/**
* Form Field
*/
class Form_Jelly_Field_Select extends Form_Jelly_Field
{
	public $_choices = array();
	public $_include_blank = null;

	public function init($options)
	{
		$this->options = array_merge($this->options, array("choices", "include_blank"));	

		$options = parent::init($options);

		if($this->choices() instanceof Jelly_Builder)
		{
			$choices = array();
			foreach($this->choices()->limit(100)->select() as $item)
			{
				$choices[$item->id()] = $item->name();
			}
			$this->choices($choices);
		}
		return $options;
	}	

	public function render_input($attributes = null)
	{
		$choices = array();
		if($this->include_blank())
		{
			$choices[] = $this->include_blank();
		}
		$choices = Arr::merge($choices, $this->choices());

		$value = $this->value();
		
		if($value instanceof Jelly_Model)
		{	
			$value = $value->id();
		}

		return Form::select($this->html_name(), $choices, $value, $this->html_attributes($attributes));
	}



	/**
	 * Sets and gets the thumbnail string.
	 *
	 * @param   string   $path  thumbnail of this field
	 * @return  mixed
	 */
	public function choices($choices = NULL)
	{
		if ($choices === NULL)
		{
			// Act as a getter
			return $this->_choices;
		}

		// Act as a setter
		$this->_choices = $choices;

		return $this;
	}

	/**
	 * Sets and gets the thumbnail string.
	 *
	 * @param   string   $path  thumbnail of this field
	 * @return  mixed
	 */
	public function include_blank($include_blank = NULL)
	{
		if ($include_blank === NULL)
		{
			// Act as a getter
			return $this->_include_blank;
		}

		// Act as a setter
		$this->_include_blank = $include_blank;

		return $this;
	}				
}