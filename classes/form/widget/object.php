<?php
/**
 * Form widget data
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Widget_Object extends Form_Widget
{
	protected $_template = '<div class="row :type-field :name-row :with-errors">:label:field:errors</div>';
	protected $_object;
	

	public function render()
	{
		$errors = array();
		foreach($this->_items as $item)
		{
			$errors = $item->errors();
		}

		$this->slots(":errors", "<span class=\"field-errors\">{$errors}</span>");
		$this->slots(":with-errors", $this->errors() ? 'with-errors' : '');

		return parent::render();
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

	public function object($object = null)
	{
		if( $object !== null)
		{
			$this->_object = $object;

			return $this;
		}
		return $this->_object;
	}		

	public function errors($errors = null)
	{
		if( is_array($errors))
		{
			foreach($errors as $item_name => $errors)
			{
				$this->_items[$item_name]->errors($errors);
			}
			return $this;
		}

		if( $errors !== null)
		{
			$this->_first_item()->errors($errors);
			return $this;
		}

		return $this->_first_item()->errors();
	}	
}