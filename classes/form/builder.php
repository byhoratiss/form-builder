<?php
/**
* Generate form()
*/
abstract class Form_Builder
{
	protected $_errors = null;
	protected $_data = null;
	protected $_prefix = '%s';
	protected $_error_file = array();
	protected $_row_template = '<div class="field :type :name-field :error_class">:label:field:errors</div>';

	static public function factory($object, $data = null, $error_file = null)
	{
		switch(get_class($object))
		{
			case 'Jelly_Model':
				return new Form_Jelly_Builder($object, $data, $error_file);
			case 'Validation':
				return new Form_Validation_Builder($object, $error_file);
		}
	}

	function __construct($data = null, $error_file = null)
	{
		$this->data($data);
		$this->_error_file = $error_file;
	}

	abstract public function check($extra_validation = null);

	public function errors($name = null)
	{
		return $name ? Arr::get($this->_errors, $name) : $this->_errors;
	}

	public function field($field_type, $field_name, $options = null)
	{
		$class = "Form_Field_".ucfirst($field_type);
		return new $class($field_name, Arr::merge($this->field_options($field_name), (array) $options ));
	}

	public function field_options($name)
	{
		return array( 
			'prefix' => $this->_prefix,
			'template' => $this->_row_template,
			'errors' => $this->errors($name),
			'value' => isset($this->_data[$name]) ? $this->_data[$name] : null,
		);
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
	
	public function row_template($row_template = null)
	{
		if( $row_template !== null)
		{
			$this->_row_template = (string) $row_template;
			return $this;
		}
		return $this->_row_template;
	}	

}