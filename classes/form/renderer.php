<?php
/**
* Form Form_Renderer used to accually generate html extend this to add your functionality
*/
class Form_Renderer
{
	protected $_prefix = '%s';
	protected $_template = 					'<div class="row :type-field :name-row">:label:render</div>';
	protected $_template_checkbox = '<div class="row :type-field :name-row">:render:label</div>';
	protected $_builder;

	public function __construct($builder)
	{
		$this->_builder = $builder;
	}

	/**
	 * getter / setter for prefix
	 *
	 * @param string $prefix
	 * @return $this|prefix
	 * @author Ivan K
	 **/
	public function prefix($prefix = null)
	{
		if( $prefix !== null)
		{
			$this->_prefix = (string) $prefix;
			return $this;
		}
		return $this->_prefix;
	}		

	/**
	 * Returns the name with the prefix applied (username becomes nested_form[username])
	 * @param string $name
	 * @return string
	 * @author Ivan K
	 **/
	protected function html_name($name)
	{
		return sprintf($this->_prefix, $name);
	}

	/**
	 * Returns the html id attribute with the prefix applied (username becomes nested_form_username)
	 * @param string $name
	 * @return string
	 * @author Ivan K
	 **/
	protected function html_id($name)
	{
		return str_replace("]", "", str_replace("[", "_", $this->html_name($name)));
	}

	/**
	 * Return html attribtues key => value array with the name and id
	 *
	 * @param string $name of the field
	 * @param array|null $attributes default
	 * @param array|null $custom_attributes overwrite
	 * @return array
	 * @author Ivan K
	 **/
	protected function html_attributes($name, $attributes = null, $custom_attributes = null)
	{
		return Arr::merge(
			(array) $attributes, 
			array('name' => $this->html_name($name), 'id' => $this->html_id($name)), 
			(array) $custom_attributes
		);
	}

	/**
	 * Returns an array with options if a required option is missing 
	 * 
	 * @param array|null $options
	 * @param array $names names of the options to be extracted, missing ones will be set to null
	 * @param array|string $required throws an exception if any of the options is missing
	 * @return array
	 * @author Ivan K
	 **/
	protected function options($options, array $names, $required = null)
	{
		$options = Arr::extract((array) $options, $names);

		if($required AND ($missing_keys = array_diff($required, array_keys($options))))
			throw new Kohana_Exception("Missing required options :missing", array(":missing" => join(", ", $missing_keys)));

		return $options;
	}

	/**
	 * Returns the parameters needed to populate the template, except :render which is populated by the render method itself. You can extend this method to return extra parameters for your templates
	 *
	 * @param string $name the name of the field
	 * @param string $options the options passed to the field / row
	 * @return array 
	 * @author Ivan K
	 **/
	protected function parameters($name, $options)
	{
		return $parameters = array(
			':label' => $this->label( $name, Arr::get($options, 'label')), 
			':name' => str_replace("_", "-", $name),
		);
	}

	/**
	 * Returns the template for the row. if options['template'] is present uses that. Custom templates for each field can be set by adding a protected $_template_{fieldname} var to the class
	 *
	 * @param string $render The method to use for rendering 
	 * @param array|null $options passed to the field / row method
	 * @return string
	 * @author Ivan K
	 **/
	protected function template($render, $options = null)
	{
		return Arr::get((array) $options, 'template', (isset($this->{"_template_$render"}) ? $this->{"_template_$render"} : $this->_template));
	}

	/**
	 * Create a html row for this name
	 * @param string $render
	 * @param string $name
	 * @param string $value
	 * @param array|null $options
	 * @param array|null $attributes
	 * @return string
	 * @author Ivan K
	 **/
	public function row($render, $name, $value, $options = null, $attributes = null)
	{
		return strtr($this->template($render, $options), Arr::merge(
			array(
				':type' => $render,
				':render' => $this->field($render, $name, $value, (array) $options, $attributes), 
			),
			$this->parameters($name, $options)
		));
	}

	/**
	 * Return an html row for this name
	 * @param string $render
	 * @param string $name
	 * @param string $value
	 * @param array|null $options
	 * @param array|null $attributes
	 * @return string
	 * @author Ivan K
	 **/
	public function field($render, $name, $value, $options = null, $attributes = null)
	{
		return $this->$render($name, $value, (array) $options, $attributes);
	}

	/**
	 * Return an html label
	 *
	 * @param string $name	 
	 * @param string $label text
	 * @return string
	 * @author Ivan K
	 **/
	public function label($name, $label = null)
	{
		return Form::label( $this->html_id($name), $label ? $label : ucfirst(Inflector::humanize($name)));
	}


	/**
	 * WIDGETS
	 * =========================
	 */

	public function select($name, $value, $options, $attributes = null)
	{
		list($choices, $include_blank) = $this->options($options, array('choices', 'include_blank'), array('choices'));

		return Form::select($this->html_name($name), $choices, $value, $field->html_attributes($name));
	}

	public function date($name, $value, $options, $attributes = null)
	{
		return Form::input($this->html_name($name), $value, $this->html_attributes($name, $attributes, array('type' => 'date')));
	}	

	public function input($name, $value, $options, $attributes = null)
	{
		return Form::input($this->html_name($name), $value, $this->html_attributes($name, $attributes));
	}

	public function hidden($name, $value, $options, $attributes = null)
	{
		return Form::input($this->html_name($name), $value, $this->html_attributes($name, $attributes, array('type' => 'date')));
	}

	public function password($name, $value, $options, $attributes = null)
	{
		return Form::input($this->html_name($name), $value, $this->html_attributes($name, $attributes, array('type' => 'password')));
	}			

	public function textarea($name, $value, $options, $attributes = null)
	{
		return Form::textarea($this->html_name($name), $value, $this->html_attributes($name, $attributes));
	}

	public function checkbox($name, $value, $options, $attributes = null)
	{
		return 
			Form::hidden($this->html_name($name), null).
			Form::checkbox($this->html_name($name), 1, $value, $this->html_attributes($name, $attributes));
	}

	public function image($name, $value, $options, $attributes = null)
	{
		list($path) = $this->options($options, array('path'), array('path'));

		return strtr('<div class="image-field">:image :input</div>', array(
			":image" => $value ? HTML::image($path.$value) : '<div class="image-placeholder"></div>', 
			":input" => Form::file($this->html_name($name), $this->html_attributes($name, $attributes))
		));
	}


}