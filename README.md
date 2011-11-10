HTML Form Builder
=================

Generate html forms bound to Validation or Jelly objects, render errors and repopulate forms
Quick Example

In the controller
``` php
<?php
	$book = Jelly::factory('book', 20);
	$form = Form_Builder::factory($book, $_POST);

	if($this->request->method() == 'post' AND $form->check())
	{
		$form->save();
		Session::instance()->set('Saved');
	}
?>
```
In the view

``` php
	//in View
	<?php echo Form::open(); ?>
		<?php echo $form->row('input', 'title') ?>
		<?php echo $form->row('select', 'type', array('choices' => array('fantasy', 'sci-fi', 'detective', 'pulp fiction'))) ?>
		<?php echo $form->row('checkbox', 'is_on_sale') ?>
		<?php echo $form->row('project::autocomplete', 'author', array('model' => 'author')) ?>
	<?php echo Form::close(); ?>
```

It will do the validation required, display errors as needed and save the object if it passess validation.
You can easily write your own widgets, as shown with the project::autocomplete. And you can extend the form class to add specific functionality.

In Your Controller
------------------

You create forms based on Arrays, Validation objects of Jelly Models.

``` php
<?php
$array_form = Form_Builder::factory($_POST);
$validation_form = Form_Builder::factory(Validation::factory($_POST)->rules( /* ... */ ));
$jelly_form = Form_Builder::factory(Jelly::factory('book', 10));
?>
```

Validation and Jelly forms have a ``$form->check()`` method to see if it passes validation, after that you can access the errors with ``$form->errors()``. Jelly forms have ``$form->save()``. You can get access to the object of the form with ``$form->object()``;

In Your View
------------

A row is a html snipped with the input field itself along with a label, errors and some css classes.
Some examples:

``` php
$form->row("input", "username");
$form->row("input", "username", array("label" => "User Name"));
$form->row("select", "accept", array("choices" => array("yes", "no"));
```

to render only the field itslef - use the ``field`` method

``` php
$form->field("select", "accept", array("choices" => array("yes", "no"));
```

Available widgets are:

* __select__ a ``select`` tag, requires option 'choices' - associative array or a Jelly_Collection
* __date__ an ``input`` tag with type date, support by modern browsers
* __datetime__ same as date, you can use javascript to add special widgets
* __input__ an ``input`` tag
* __hidden__ an `input` tag type hidden
* __file__ an `input` tag type file
* __password__ an `input` tag type password
* __textarea__ a ``textarea`` tag
* __checkbox__ a ``input`` tag type checkbox with a hidden tag with the same name before it so you will resieve null value in the $_POST when not checked
* __radio__ a ``input`` tag type radio with a hidden tag with the same name before it so you will resieve null value in the $_POST when not checked
* __image__ a ``input`` tag file
* __checkboxes__ mimicks multiple select with html list of checkboxs, requires option 'choices' - associative array or a Jelly_Collection
* __radios__ mimicks select with html list of radio inputs, requires option 'choices' - associative array or a Jelly_Collection

Nested Forms
------------

You can achieve nested forms by defining a prefix

``` php
<?php
$form->prefix("nested_form[%s]");
// OR
$form->prefix("nested_form[child_form][%s]");
?>
```

This will move all the widget names / ids in the proper namespace

Form Widgets
------------
You can write your own widgets. Each widget is a static method of a class Form_Widgets_{your_name}. You access those widgets with ``$form->row("{your_name}::{widget_name});

A typical widget looks like this:

``` php
<?php
class Form_Widgets_Custom
{
	static public function richtextarea(Form_Widget $data)
	{
		$this->atrributes(array('data-type' => 'rich'));

		return Form::textarea($this->name(), $data->value(), $this->atrributes()->as_array());
	} 
?>
```

``` php
//In the view you'll call the widget like this
$form->row('custom::richtextarea', 'myfield');
```
$data is an instance of Form_Widget (or Form_Widget_Object if its jelly or validation) that you can manipulate. What you return will be placed inside :field slot, but you can do this directly from the widget fill different slots and whatever.

You can work with multiple fields for a widget using the ->items() array

``` php
<?php
class Form_Widgets_Custom
{
	static public function richtextarea(Form_Widget $data)
	{
		$this->atrributes(array('data-type' => 'rich'));

		return 
			"<div ".$this->attributes.">".
				Form::input($this->items('field1')->name(), $data->items('field2')->value()).
				Form::input($this->items('field2')->name(), $data->items('field2')->value()).
			"</div>";
	} 
?>
```

``` php
//In the view you'll call the widget like this
$form->row('custom::multifield', array('field1', 'field2'));
```

``$data->name()``, ``$data->value()``, ``$data->id()``, ``$data->field_value()``, ``$data->errors()`` still work, but will return the first field in ``->items()`` array


Here's an example of a very complex widget impelemnting manytomany / hasmany / belongsto jelly association with jquery autocompelete

```php
<?
	//Helper method to render an item from the outcompelte
	static public function list_item($item, $name)
	{
		return Admin::content_tag("li", array(), 
			'<span class="ui-button ui-icon ui-icon-close"></span>'.
			"<strong>".$item->name()."</strong>".
			Form::hidden($name, $item->id())
		);
	}

	static public function autocomplete(Form_Widget $data)
	{
		//If options does not have a model, throw an exception
		$data->required("model");

		//Add those to the html attributes 
		$data->attributes( array(
			'data-model' => $data->options('model'),
			'data-type' => 'autocomplete',
			'data-name' => $data->name(),
		));

		$value_html = '';

		//Different implementation based on the multiple option
		if($data->options('multiple'))
		{
			$data->attributes( array(
				'data-multiple' => 'true',
				'data-name' => $data->name().'[]',
			));

			if($value = $data->value() AND $value instanceof Jelly_Collection)
			{
				foreach($value as $item)
				{
					$value_html .= self::list_item($item, $data->attributes('data-name'));
				}
			}					
		}
		else
		{
			if($value = $data->value() AND $value instanceof Jelly_Model AND $value->loaded())
			{
				$value_html = self::list_item($value, $data->attributes('data-name'));

				//This will add a "selected" class, not disturbing other classes that you might pass from the view
				$data->attributes()->add_class('selected');
			}
		}

		return 
			//This hidden is needed to reset the association to no elements when there are no items 
			Form::hidden($data->name(), '').
			"<ul class=\"list-values\">$value_html</ul>".
			Form::input($data->name().'_input', null, $data->attributes()->as_array());
	}
?>
```





