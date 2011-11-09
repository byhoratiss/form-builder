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
	public $attributes = null;
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
		$this->attributes = new Form_Widget_Attributes(array('id' => $this->id()));
	}

	protected function _first_name()
	{
		return is_array($this->name) ? reset($this->name) : $this->name;
	}

	public function label($label = null)
	{
		return Form::label( $this->id(), Arr::get($this->options, 'label', $label ? $label : ucfirst(Inflector::humanize($this->_first_name()))));
	}

	/**
	 * Render the widget filling the slots in the template
	 * @return string
	 */
	public function render()
	{
		$this->slot(":name", $this->_first_name(), FALSE);
		$this->slot(":label", $this->label(), FALSE);

		return strtr($this->template, $this->slots);
	}

	/**
	 * Sets the attributes of the object 
	 * @param array|string $values the name of the object or an array of name => value
	 * @param mixed $value 
	 * @return Form_Widget $this
	 */
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


	/**
	 * Set the contents of a slot for the template
	 * @param string $name The name of the slot
	 * @param string $value the content of the slot
	 * @param bool $overwrite Wheter to overwrite existing slots if they exist
	 * @return $this
	 */
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

	/**
	 * Return the real callback funciton, prepending Form_Widget if non is given
	 * @param string $callback 
	 * @return array callable array
	 */
	protected static function _real_callback($callback)
	{
		if( strpos($callback, '::') === FALSE )
		{
			$callback = array( 'Form_Widgets', $callback);
		}
		else
		{
			$callback = explode('::', $callback);

			$callback[0] = 'Form_Widgets_'.ucfirst($callback[0]);
		}
		return $callback;		
	}

	/**
	 * Execute the widgets callback and place the result in the field slot
	 * @param array $callback 
	 * @return $this
	 */
	public function field_callback($callback)
	{
		$callback = self::_real_callback($callback);

		$this->slot(':type', Arr::get($callback, 1));

		if( $field = call_user_func($callback, $this) )
		{
			$this->slot(":field", $field);
		}
		return $this;
	}

	/**
	 * Return a name with prefixed with this widget's prefix
	 * so 'email' will become parent_form[email] in child forms
	 * @param string $name 
	 * @return string
	 */
	public function prefixed_name($name)
	{
		return sprintf($this->prefix, $name);
	}

	/**
	 * Return the prefixed name of the widget, if the name is an array, return an array of prefixed names
	 * If a name is given return this prefixed name from the array of names
	 * @param string $name 
	 * @return string|array
	 */
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

	/**
	 * Return an id with prefixed with this widget's prefix
	 * so 'email' will become parent_form_email in child forms
	 * @param string $name 
	 * @return string
	 */
	public function prefixed_id($name)
	{
		return str_replace("]", "", str_replace("[", "_", $this->prefixed_name($name)));
	}

	/**
	 * Return the prefixed id of the widget, if the name is an array, return an array of prefixed ids
	 * If a name is given return this prefixed name from the array of ids
	 * @param string $name 
	 * @return string|array
	 */
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

	/**
	 * Return the value, if the name is an array, return an array of prefixed values
	 * If a name is given return this name's value
	 * @param string $name 
	 * @return string|array
	 */
	public function value($name = null)
	{
		if($name !== null)
		{
			return Arr::get((array) $this->value, $name);
		}
		return $this->value;
	}	

	/**
	 * Set required options
	 * @return $this
	 */
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