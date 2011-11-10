<?php
/**
 * HTML Attributes
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Widget_Attributes implements ArrayAccess, Iterator, Countable 
{
	private $container = array();

	public function __construct($container) 
	{
		$this->container = (array) $container;
	}

	public function merge_as_data($options)
	{
		foreach((array) $options as $option_name => $option)
		{
			$this->container['data-'.$option_name] = $option;
		}
		return $this;
	}

	public function merge($container)
	{
		$this->container = Arr::merge($this->container, $container);
		return $this;
	}

	/**
	 * Add a css class to the attributes, can pass array for multiple classes
	 * @param array|string $class_name 
	 * @return $this
	 */
	public function add_class($class_name)
	{
		$this->container['class'] = join(' ', array_unique(array_filter(array_merge(explode(' ', Arr::get($this->container, 'class', '')), (array) $class_name))));

		return $this;
	}

	/**
	 * Remove a css class to from the attributes, can pass array for multiple classes
	 * @param array|string $class_name 
	 * @return $this
	 */
	public function remove_class($class_name)
	{
		$this->container['class'] = join(' ', array_unique(array_filter(array_diff(explode(' ', Arr::get($this->container, 'class', '')), (array) $class_name))));

		return $this;
	}

	public function __toString()
	{
		return HTML::attributes($this->container);
	}

	/**
	 * Return the raw attributes array
	 * @return array
	 */
	public function as_array()
	{
		return $this->container;
	}

	public function offsetSet($offset, $value) 
	{
		if ($offset == "") 
		{
			$this->container[] = $value;
		}
		else 
		{
			$this->container[$offset] = $value;
		}
	}

	public function offsetExists($offset) 
	{
	 return isset($this->container[$offset]);
	}

	public function offsetUnset($offset) 
	{
		unset($this->container[$offset]);
	}

	public function offsetGet($offset) 
	{
		return isset($this->container[$offset]) ? $this->container[$offset] : null;
	}

	public function rewind() 
	{
		reset($this->container);
	}

	public function current() 
	{
		return current($this->container);
	}

	public function key() 
	{
		return key($this->container);
	}

	public function next() 
	{
		return next($this->container);
	}

	public function valid() 
	{
		return $this->current() !== false;
	}    

	public function count() 
	{
	 return count($this->container);
	}

}