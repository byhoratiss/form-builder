<?php
/**
 * Generate form()
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Builder_Jelly extends Form_Builder_Validation
{
	protected $_original_data = array();
	protected $_html5_validation = true;

	static protected $_additional_filters = array(
		'belongsto' => array('Form_Builder_Jelly::_filter_belongsto', ':field', ':value'),
		'hasmany' => array('Form_Builder_Jelly::_filter_many', ':field', ':value'),
		'polymorphic_hasmany' => array('Form_Builder_Jelly::_filter_polymorphic_hasmany', ':field', ':value'),
		'polymorphic_belongsto' => array('Form_Builder_Jelly::_filter_polymorphic_belongsto', ':field', ':value'),
		'manytomany' => array('Form_Builder_Jelly::_filter_many',':field', ':value'),
	);

	static protected $_filteres_applied = array();

	public function widget($name)
	{
		$widget = parent::widget($name);

		if( $this->_html5_validation AND ! is_array($name) AND $field = $this->_object->meta()->field($name))
		{
			$widget->attributes(self::html5_rules($field->rules));
		}
		return $widget;
	}	

	static public function html5_rules(array $rules)
	{
		$validation = array();
		foreach($rules as $rule)
		{
			switch($rule[0])
			{
				case 'not_empty':
					$validation['required'] = '';
					break;

				case 'url':
					$validation['url'] = '';
					break;

				case 'range':
					$validation['min'] = $rule[1][1];
					$validation['max'] = $rule[1][2];
					break;

				case 'min_length':
					$validation['minlength'] = $rule[1][1];
					break;

				case 'max_length':
					$validation['maxlength'] = $rule[1][1];
					break;

				case 'regex':
					$validation['pattern'] = $rule[1][1];
					break;
			}
		}
		return $validation;		
	}

	function __construct(Jelly_Model $object, $data = null)
	{
		$this->object($object);

		foreach(Jelly::meta($object)->fields() as $field)
		{
			if ( ! isset($field->filters['jelly_form']))
			{
				foreach (self::$_additinal_filters as $field_name => $field_filter) 
				{
					if(Jelly::field_prefix().$field_name == get_class($field))
					{
						$field->filters['jelly_form'] = $field_filter;
						break;	
					}
				}
			}
		}

		$this->data((array) $data);	
	}

	public function save()
	{
		$this->_object->save();
		return $this;
	}

	public function html5_validation($validate = null)
	{
		if( $validate !== null)
		{
			$this->_html5_validation = (bool) $validate;
			return $this;
		}
		return $this->_html5_validation;		
	}

	public function check($save = FALSE, $extra_validation = null)
	{
		$this->_object->set($this->_data);

		try{
			$this->_object->check($extra_validation);

			$this->_errors = null;

			if($save)
			{
				$this->save();
			}

			$this->data(Arr::merge((array) $this->_data, $this->_object->as_array() ));

			return true;
		}
		catch(Jelly_Validation_Exception $e)
		{
			$this->_errors = $e->errors($this->_error_file);
			$this->_errors = Arr::merge($this->_errors, Arr::get($this->_errors, '_external', array()));

			$this->data(Arr::merge((array) $this->_data, $this->_object->as_array() ));

			return false;
		}
	}

	static protected function _covert_to_item($type, $item_data, $load_by_id = FALSE)
	{
		if(is_array($item_data))
		{
			$id = Arr::get($item_data, Jelly::meta($type)->primary_key(), null);

			if( ! $id )
			{
				unset($item_data[ Jelly::meta($type)->primary_key()]);
			}
			
			$item = Jelly::factory($type, $id)->set($item_data);
			if( ! $item->loaded() OR $item->changed())
			{
				$item->save();	
			}
			return $item;
		}
		elseif($load_by_id)
		{
			return Jelly::factory($type, $item_data);
		}
		return $item_data;
	}	

	static protected function _filter_many($field, $value)
	{
		if (is_array($value))
		{
			foreach( $value as $i => &$item_data)
			{
				$item_data = self::_covert_to_item($field->foreign['model'], $item_data, FALSE);
			}
		}
		return $value;
	}

	static protected function _filter_belongsto($field, $value)
	{
		if (is_array($value))
		{
			$value = self::_covert_to_item($field->foreign['model'], $value, FALSE);
		}		
		return $value;
	}

	static protected function _filter_polymorphic_belongsto($field, $value)
	{
		if (is_array($value))
		{
			$value = self::_covert_to_item(key($value), reset($value), TRUE);
		}		
		return $value;
	}

	static protected function _filter_polymorphic_hasmany($field, $value)
	{
		if (is_array($field_data))
		{
			$loaded_items = array();

			foreach( $value as $type => $items)
			{
				if(is_array($items))
				{
					foreach($items as $item_data)
					{
						$loaded_items[] = self::_covert_to_item($type, $item_data, TRUE);
					}
				}
			}

			$value = $loaded_items;
		}

		return $value;
	}	
}