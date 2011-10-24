<?php
/**
* Generate form()
*/
class Form_Builder
{
	protected $_data = null;
	protected $_prefix = '%s';
	protected $_renderer = 'Form_Renderer';

	static public function factory($object, $data = null)
	{
		if( $object instanceof Jelly_Model)
		{
			return new Form_Jelly_Builder($object, $data);
		}
		elseif($object instanceof Validation)
		{
			return new Form_Validation_Builder($object);
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

	public function row($render, $name, $options = null, $attributes = null )
	{
		return $this->renderer()->row($render, $name, $this->value($name), (array) $options, $attributes);
	}

	public function field($render, $name, $options = null, $attributes = null )
	{
		return $this->renderer()->field($render, $name, $this->value($name), (array) $options, $attributes);
	}	

	public function label($name, $label = null)
	{
		return $this->renderer()->label($name, $label);
	}


	public function value($name)
	{
		return is_array($name) ? Arr::extract($this->_data, $name) : Arr::get($this->_data, $name);
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

			if(is_object($this->_renderer))
			{
				$this->_renderer->prefix($prefix);
			}			
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

}