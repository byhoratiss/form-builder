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

	protected $widget_helpers = array();
	protected $widgets = array();


	/**
	 * Return a ready Form_Builder object, based on the type of the argument
	 * * Array - Form_Builder
	 * * Validation - Form_Builder_Validation
	 * * Jelly_Model - Form_Jelly_Model
	 * @param mixed $object
	 * @param array $data 
	 * @return Form_Builder
	 */
	static public function factory($object, $data = null)
	{
		if( $object instanceof Jelly_Model)
		{
			return new Form_Builder_Jelly($object, $data);
		}
		elseif($object instanceof Validation)
		{
			return new Form_Builder_Validation($object);
		}
		else
		{
			return new Form_Builder($object);	
		}
	}

	function __construct($data = null)
	{
		$this->data($data);
	}

	public function widget($name)
	{
		$widget = new Form_Widget(Arr::extract($this->_data, (array) $name));

		return $widget->prefix($this->_prefix);

	}

	public function row($callback, $name, $options = null, $attributes = null )
	{
		return $this->field($callback, $name, $options, $attributes)->render();
	}

	public function field($callback, $name, $options = null, $attributes = null )
	{
		return $this
			->widget($name)
			->options((array) $options)
			->attributes((array) $attributes)
			->field_callback($callback);
	}	

	public function value($name)
	{
		return Arr::get($this->_data, $name);
	}

	public function renderer($renderer = null)
	{
		if( $renderer !== null)
		{
			$this->_renderer = $renderer;
			return $this;
		}

		if(is_string($this->_renderer))
		{
			$this->_renderer = new $this->_renderer($this);

			if( ! ($this->_renderer instanceof Form_Renderer)) 
				throw new Kohana_Exception(":renderer must be a subclass of Form_render", array(":renderer" => get_class($this->_renderer)));

			$this->_renderer->prefix($this->_prefix);
		}

		return $this->_renderer;
	}		

	public function prefix($prefix = null)
	{
		if( $prefix !== null)
		{
			$this->_prefix = (string) $prefix;
			return $this;
		}
		return $this->_prefix;
	}

	public function data($data = null)
	{
		if( $data !== null)
		{
			$this->_data = $data;
			return $this;
		}
		return $this->_data;
	}

	
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