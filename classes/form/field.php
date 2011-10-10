<?php
/**
* Form Field
*/
abstract class Form_Field
{
	protected $_name;
	protected $_label;
	protected $_value;
	protected $_help;

	protected $_prefix = '%s';
	protected $_errors = array();

	protected $required = array();
	protected $options = array('value', 'template', 'prefix', 'label', 'errors');
	protected $_template = ':label:field:errors';
	protected $_bound = array();


	function __construct($name, $options = null)
	{
		$this->name($name);	

		$this->init((array) $options);
	}

	abstract public function render_input($attributes = null);

	public function init($options)
	{
		foreach ($this->options as $option) {
			if(isset($options[$option]))
			{
				$this->$option($options[$option]);
				unset($options[$option]);
			}
		}
		$this->options = (array) $options;

		if( ! $this->_label )
		{
			$this->_label = ucfirst(Inflector::humanize($this->_name));
		}

		if($this->required AND ($missing_keys = array_diff($this->required, array_keys($this->options))))
		{
			throw new Kohana_Exception(":field with name :name has missing required options :missing", array(":field" => get_class($this), ":name" => $name, ":missing" => join(", ", $missing_keys)));
		}
		return $options;
	}

	public function render($attributes = null)
	{

		$this->bind(array(
			':label' => $this->render_label(), 
			':field' => $this->render_input($attributes), 
			':errors' => $this->render_errors(),
			':error_class' => $this->_errors ? 'with-errors' : '',
			':name' => str_replace("_", "-", $this->_name),
			':type' => strtolower(str_replace("_", "-", get_class($this)))
		));

		return strtr($this->_template, $this->_bound);
	}	

	public function __toString()
	{
		return $this->render();
	}

	public function render_label($attributes = null)
	{
		return Form::label($this->html_id(), $this->label().($this->_help ? "<small>{$this->_help}</small>" : ''), $attributes);
	}

	public function render_errors()
	{
		return Arr::get($this->_errors, 0);
	}

	public function html_name()
	{
		return sprintf($this->_prefix, $this->_name);
	}

	public function html_id()
	{
		return str_replace("]", "", str_replace("[", "_", $this->html_name()));
	}

	protected function html_attributes($attributes = null, $custom_attributes = null)
	{
		return Arr::merge(
			(array) $attributes, 
			array('name' => $this->html_name(), 'id' => $this->html_id()), 
			(array) $custom_attributes
		);
	}

	public function bind($name, $value = null)
	{
		if(is_array($name))
		{
			foreach ($name as $bound_name => $value) {
				$this->_bound[$bound_name] = $value;		
			}
		}
		else
		{
			$this->_bound[$name] = $value;
		}
		return $this;
	}

	public function errors($errors = null)
	{
		if( $errors !== null)
		{
			$this->_errors = $errors;
			return $this;
		}
		return $this->_errors;
	}		

	/**
	 * Sets and gets the template string.
	 *
	 * @param   string   $prefix  Prefix of this field
	 * @return  mixed
	 */
	public function template($template = NULL)
	{
		if ($template === NULL)
		{
			// Act as a getter
			return $this->_template;
		}

		// Act as a setter
		$this->_template = (string) $template;

		return $this;
	}		

	/**
	 * Sets and gets the prefix string.
	 *
	 * @param   string   $prefix  Prefix of this field
	 * @return  mixed
	 */
	public function prefix($prefix = NULL)
	{
		if ($prefix === NULL)
		{
			// Act as a getter
			return $this->_prefix;
		}

		// Act as a setter
		$this->_prefix = (string) $prefix;

		return $this;
	}	

	/**
	 * Sets and gets the help string.
	 *
	 * @param   string   $help  Help of this field
	 * @return  mixed
	 */
	public function help($help = NULL)
	{
		if ($help === NULL)
		{
			// Act as a getter
			return $this->_help;
		}

		// Act as a setter
		$this->_help = (string) $help;

		return $this;
	}	


	/**
	 * Sets and gets the value string.
	 *
	 * @param   string   $value  Value of this field
	 * @return  mixed
	 */
	public function value($value = NULL)
	{
		if ($value === NULL)
		{
			// Act as a getter
			return $this->_value;
		}

		// Act as a setter
		$this->_value = $value;

		return $this;
	}	

	/**
	 * Sets and gets the name string.
	 *
	 * @param   string   $name  Name of this field
	 * @return  mixed
	 */
	public function name($name = NULL)
	{
		if ($name === NULL)
		{
			// Act as a getter
			return $this->_name;
		}

		// Act as a setter
		$this->_name = (string) $name;

		return $this;
	}

	/**
	 * Sets and gets the label string.
	 *
	 * @param   string   $label  Label of this field
	 * @return  mixed
	 */
	public function label($label = NULL)
	{
		if ($label === NULL)
		{
			// Act as a getter
			return $this->_label;
		}

		// Act as a setter
		$this->_label = (string) $label;

		return $this;
	}

}