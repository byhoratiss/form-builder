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
	protected $_template = '<div class="row :type-field :name-row">:label:field</div>';
	protected $_attributes = array();
	protected $_slots = array(
		':type' => 'generic',
		':field' => '',
	);
	protected $_options = array();
	protected $_items = array();
	protected $_prefix = '%s';

	function __construct( $items )
	{
		$this->items((array) $items);
		$this->_attributes = new Form_Widget_Attributes(array('id' => $this->id()));
	}

	public function first_item()
	{
		reset($this->_items);
		return current($this->_items);
	}

	/**
	 * The field name of the first item
	 * @return string
	 */
	public function field_name()
	{
		return $this->first_item()->field_name();
	}

	/**
	 * The name of the first item
	 * @return string
	 */
	public function name()
	{
		return $this->first_item()->name();
	}

	/**
	 * The id of the first item
	 * @return string
	 */
	public function id()
	{
		return $this->first_item()->id();
	}

	/**
	 * The value of the first item
	 * @return string
	 */
	public function value()
	{
		return $this->first_item()->value();
	}

	/**
	 * A label tag for the first item
	 * @param string $label Optional label if not set will humanize the item name
	 * @return string
	 */
	public function label($label = null)
	{
		return $this->first_item()->label($this->options('label', $label));
	}

	/**
	 * Render the widget filling the slots in the template
	 * @return string
	 */
	public function render()
	{
		$this->slots(":name", $this->name(), FALSE);
		$this->slots(":label", $this->label(), FALSE);

		return strtr($this->_template, $this->_slots);
	}


	public function __toString()
	{
		return Arr::get($this->_slots, ':field');
	}


	/**
	 * Set the contents of a slot for the template
	 * @param string $name The name of the slot
	 * @param string $value the content of the slot
	 * @param bool $overwrite Wheter to overwrite existing slots if they exist
	 * @return $this
	 */
	public function slots($name, $value = null, $overwrite = TRUE)
	{
		if( $value === null)
		{
			return Arr::get($this->_slots, $name);
		}

		if( $overwrite OR ! isset($this->_slots[$name]))
		{
			$this->_slots[$name] = $value;	
		}
		
		return $this;
	}

	/**
	 * Swap two slots in the template. Useful for swapping :label and :field for example
	 * @param string $first_slot 
	 * @param string $second_slot 
	 * @return $this
	 */
	public function swap_slots($first_slot, $second_slot)
	{
		$string = str_replace($first_slot, ':$first_slot$:', $this->_template);
		$string = str_replace($second_slot, $first_slot, $string);
		$this->_template = str_replace(':$first_slot$:', $second_slot, $string);
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

		$this->slots(':type', Arr::get($callback, 1));

		if( $field = call_user_func($callback, $this) )
		{
			$this->slots(":field", $field);
		}
		return $this;
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

		if( $missing_keys = array_diff($required, array_keys($this->_options)))
		{
			throw new Kohana_Exception("Missing required options :missing for widget :name", array(":missing" => join(", ", $missing_keys), ':name' => $this->field_name()));
		}

		return $this;		
	}

	/**
	 * Return the prefix for a form that will be a "child" of this widget
	 * @return string
	 */
	public function child_prefix()
	{
		$args = func_get_args();
		return call_user_func_array('Form_Builder::generate_prefix', array_merge(array($this->_prefix, $this->field_name()), $args));
	}

	/**
	 * Get / Set prefix for this widget
	 * @param string $prefix 
	 * @return string|$this
	 */
	public function prefix($prefix = null)
	{
		if( $prefix !== null)
		{
			$this->_prefix = (string) $prefix;
			foreach($this->_items as $item)
			{
				$item->prefix($this->_prefix)	;
			}
			$this->_attributes['id'] = $this->id();

			return $this;
		}
		return $this->_prefix;
	}

	/**
	 * Get / Set template for this widget
	 * @param string $template 
	 * @return string|$this
	 */
	public function template($template = null)
	{
		if( $template !== null)
		{
			$this->_template = (string) $template;

			return $this;
		}
		return $this->_template;
	}	


	/**
	 * Get / Set options, with a second argument as the default. If you set the first argument an array will merge it with the current options
	 * 
	 * @param string|array $name the name of the option to return, if not supplied returns all the options
	 * @param mixed $default return this if the option is not present or null
	 * @return mixed
	 */
	public function options($name = null, $default = null)
	{
		if( is_array($name))
		{
			$this->_options = Arr::merge($this->_options, $name);
			return $this;
		}
		if( $name !== null)
		{
			return Arr::get($this->_options, $name, $default);
		}

		return $this->_options;
	}

	/**
	 * Get / Set attributes, with a second argument as the default. If you set the first argument an array will merge it with the current attributes
	 * 
	 * @param string|array $name the name of the option to return, if not supplied returns all the options
	 * @param mixed $default return this if the attribute is not present or null
	 * @return mixed
	 */
	public function attributes($name = null, $default = null)
	{
		if( is_array($name))
		{
			$this->_attributes->merge($name);
			return $this;
		}
		if( $name !== null)
		{
			return Arr::get($this->_attributes, $name, $default);
		}

		return $this->_attributes;
	}


	/**
	 * Get / Set items. If you set the first argument an array will merge it with the current items. The array must be 'name' => 'value'
	 * 
	 * @param string|array $name the name of the item to return, if not supplied returns all the items
	 * @return mixed
	 */
	public function items($name = null)
	{
		if( is_array($name))
		{
			foreach($name as $item_name => $item_value)
			{
				$this->_items[$item_name] = new Form_Widget_Item($item_name, $item_value, $this->_prefix);
			}
			return $this;
		}

		if( $name !== null)
		{
			return Arr::get($this->_items, $name);
		}

		return $this->_items;
	}		
}