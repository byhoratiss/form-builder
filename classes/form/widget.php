<?php
/**
 * Form widget data
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Widget
{
	public $template = '<div class="row :type-field :name-row">:label:field</div>';
	public $attributes = array();
	public $slots = array(
		':type' => 'generic',
		':field' => '',
	);
	public $options = array();
	public $name;
	public $value;
	public $prefix = '%s';

	function __construct( $name )
	{
		$this->name = $name;
		$this->attributes(array('id' => $this->id()));
	}

	protected function _first_name()
	{
		return is_array($this->name) ? reset($this->name) : $this->name;
	}

	public function label($label = null)
	{
		return Form::label( $this->id(), Arr::get($this->options, 'label', $label ? $label : ucfirst(Inflector::humanize($this->_first_name()))));
	}

	public function render()
	{
		$this->slot(":name", $this->_first_name(), FALSE);
		$this->slot(":label", $this->label(), FALSE);

		return strtr($this->template, $this->slots);
	}

	public function set($values, $value = null)
	{
		if ( ! is_array($values))
		{
			$values = array($values => $value);
		}

		foreach( $values as $name => $value )
		{
			$this->$name = $value;
		}

		return $this;
	}

	public function __toString()
	{
		return Arr::get($this->slots, ':field');
	}

	public function slot($name, $value = null, $overwrite = TRUE)
	{
		if( $value === null)
		{
			return Arr::get($this->slots, $name);
		}

		if( $overwrite OR ! isset($this->slots[$name]))
		{
			$this->slots[$name] = $value;	
		}
		
		return $this;
	}

	public function field_callback($callback)
	{
		$this->slot(':type', Arr::get($callback, 1));

		$field = call_user_func($callback, $this);
		if( $field )
		{
			$this->slot(":field", $field);
		}
		return $this;
	}

	public function prefixed_name($name)
	{
		return sprintf($this->prefix, $name);
	}

	public function name( $name = null )
	{
		if( $name !== null AND is_array($this->name))
		{
			return $this->prefixed_name( Arr::get($this->name, $name));
		}
		elseif(is_array($this->name))
		{
			return isset($this->name[$name]) ? array_map(array($this, 'prefixed_name'), $this->name) : null;
		}
		else
		{
			return $this->prefixed_name($this->name);
		}
	}

	public function prefixed_id($name)
	{
		return str_replace("]", "", str_replace("[", "_", $this->prefixed_name($name)));
	}

	public function id( $name = null )
	{
		if( $name !== null AND is_array($this->name) )
		{
			return isset($this->name[$name]) ? $this->prefixed_id( Arr::get($this->name, $name)) : null;
		}
		elseif(is_array($this->name))
		{
			return array_map(array($this, 'prefixed_id'), $this->name);
		}
		else
		{
			return $this->prefixed_id($this->name);
		}
	}

	public function value($name = null)
	{
		if($name !== null)
		{
			return Arr::get((array) $this->value, $name);
		}
		return $this->value;
	}	

	public function options($name = null, $default = null )
	{
		if( $name === null)
		{
			return $this->options;
		}

		if( is_array( $name ) )
		{
			$this->options = Arr::merge($this->options, $name);
			return $this;
		}

		return Arr::get($this->options, $name, $default);
	}

	public function attributes($name = null, $default = null )
	{
		if( $name === null)
		{
			return $this->attributes;
		}
		
		if( is_array( $name ) )
		{
			$this->attributes = Arr::merge($this->attributes, $name);
			return $this;
		}

		return Arr::get($this->attributes, $name, $default);
	}

	public function required()
	{
		if(func_num_args() == 0)
		{
			throw new Kohana_Exception("Please set some fields as required");
		}

		$required = Arr::flatten(func_get_args());

		if( $missing_keys = array_diff($required, array_keys($this->options)))
		{
			throw new Kohana_Exception("Missing required options :missing for widget :name", array(":missing" => join(", ", $missing_keys), ':name' => $this->name));
		}

		return $this;		
	}

}