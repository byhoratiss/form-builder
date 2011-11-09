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

Form Builder
------------


