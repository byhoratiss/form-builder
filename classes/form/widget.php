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

	protected function _first_item()
	{
		reset($this->_items);
		return current($this->_items);
	}

	public function field_name()
	{
		return $this->_first_item()->name();
	}

	public function name()
	{
		return $this->_first_item()->name();
	}

	public function id()
	{
		return $this->_first_item()->id();
	}

	public function value()
	{
		return $this->_first_item()->value();
	}

	public function label($label = null)
	{
		return $this->_first_item()->label($this->options('label', $label));
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

	public function child_prefix()
	{
		return call_user_func_array('Form_Builder::generate_prefix', array_merge(array($this->_prefix, $this->field_name()), func_get_args()));
	}

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

	public function template($template = null)
	{
		if( $template !== null)
		{
			$this->_template = (string) $template;

			return $this;
		}
		return $this->_template;
	}	

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

		return $this->_attributes;
	}		



}