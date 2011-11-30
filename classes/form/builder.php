<?php
/**
 * Generate form()
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Builder
{
	protected $_data = null;
	protected $_prefix = '%s';

	static protected $_builder_prefix = 'Form_Builder_';

	/**
	 * Return a ready Form_Builder object
	 * You can specify which form builder class you want. The first argument can be a name so for example Form_Builder_Jelly can be Form_Builder::factory('jelly', $model, $data);
	 * @param mixed $name
	 * @param array $data 
	 * @return Form_Builder
	 */
	static public function factory($name, $data = null)
	{
		if(is_string($name))
		{
			$args = func_get_args();
			$class = new ReflectionClass(self::$_builder_prefix.$name); 

			return $class->newInstanceArgs(array_slice($args, 1));
		}
		else
		{
			return new Form_Builder($name);	
		}		
	}

	function __construct($data = null)
	{
		$this->data($data);
	}

	/**
	 * Generate a new Widget form the name
	 * @param string|array $name the name or array of names for the widget
	 * @return Form_Widget
	 */
	public function widget($name)
	{
		$widget = new Form_Widget(Arr::extract($this->_data, (array) $name));

		return $widget->prefix($this->_prefix);

	}

	/**
	 * Generate an html row with a widget using the template
	 * 
	 * @param string $callback "input", "select", "textarea" or custom one "admin::checkboxes"
	 * @param string|array $name the name or array of names for the widget
	 * @param array $options 
	 * @param array $attributes 
	 * @return string
	 */
	public function row($callback, $name, $options = null, $attributes = null )
	{
		return $this->field($callback, $name, $options, $attributes)->render();
	}

	/**
	 * Return only the field with the widget
	 * 
	 * @param string $callback "input", "select", "textarea" or custom one "admin::checkboxes"
	 * @param string|array $name the name or array of names for the widget
	 * @param array $options 
	 * @param array $attributes 
	 * @return string
	 */
	public function field($callback, $name, $options = null, $attributes = null )
	{
		return $this
			->widget($name)
			->options((array) $options)
			->attributes((array) $attributes)
			->field_callback($callback);
	}	

	/**
	 * Get the value of a data inside the form
	 * @param string $name 
	 * @return mixed
	 */
	public function value($name)
	{
		return Arr::get($this->_data, $name);
	}

	/**
	 * Get / Set the prefix for this form
	 * @param string $prefix 
	 * @return string|$this
	 */
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
	 * Get / Set the data for this form
	 * @param string $data 
	 * @return string|$this
	 */
	public function data($data = null)
	{
		if( $data !== null)
		{
			$this->_data = $data;
			return $this;
		}
		return $this->_data;
	}

	/**
	 * Helper method to build a prefix based, arbitrary number of fields deep ( after the $name argument )
	 * @param string $prefix 
	 * @param string $name 
	 * @return string
	 */
	static public function generate_prefix($prefix, $name)
	{
		$additional = array_slice(func_get_args(), 2);
		foreach($additional as $additional_name)
		{
			if( $additional_name !== null)
				$name .= "[$additional_name]";
		}
		return preg_replace('/^([^\[]+)(.*)$/', "{$name}[\$1]\$2", $prefix);
	}	

}