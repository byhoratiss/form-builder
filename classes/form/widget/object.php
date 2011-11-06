<?php
/**
* Form widget data
*/
class Form_Widget_Object extends Form_Widget
{
	public $template = '<div class="row :type-field :name-row :with-errors">:label:field:errors</div>';
	public $object;
	public $errors;

	public function __construct($name, $object)
	{
		$this->object = $object;
		parent::__construct($name);
	}

	public function render()
	{
		$this->slot(":errors", $this->errors());
		$this->slot(":with-errors", $this->errors() ? 'with-errors' : '');

		return parent::render();
	}

	public function errors()
	{
		$errors = array_filter((array) $this->errors);
		if( ! $errors )
		{
			return '';
		}
		
		$errors = join(", ", Arr::flatten($errors));

		return "<span class=\"field-errors\">{$errors}</span>";
	}	
}