<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_WidgetTest extends Kohana_Unittest_TestCase {

	public function provider_items()
	{
		return array(
			array('field1'),
			array('field2'),
			array(array('field1')),
			array(array('field1', 'field2')),
			array(array('field1', 'field2', 'field3')),
		);
	}

	/**
	 * @dataProvider provider_items
	 */
	public function test_items($items)
	{
		$form = Form_Builder::factory(array("field2" => "value 2", "field1" => "value 1", "field3" => "value 3"));	

		$widget = $form->widget($items);

		$items = (array) $items;
		$this->assertCount(count($items), $widget->items());

		$this->assertEquals($widget->items(reset($items))->name(),       $widget->name());
		$this->assertEquals($widget->items(reset($items))->id(),         $widget->id());
		$this->assertEquals($widget->items(reset($items))->field_name(), $widget->field_name());
		$this->assertEquals(reset($items), $widget->field_name());
		$this->assertEquals($widget->items(reset($items))->label(),      $widget->label());
		$this->assertEquals($widget->items(reset($items))->value(),      $widget->value());

		foreach($items as $item)
		{
			$this->assertEquals($widget->items($item)->field_name(), $item);
			$this->assertEquals($widget->items($item)->value(), $form->value($item));
		}
	}

	public function test_slots()
	{
		$form = Form_Builder::factory(array("field2" => "value 2", "field1" => "value 1", "field3" => "value 3"));
		$widget = $form->widget('field1')->template(":slot1, :slot2 :field");
		$widget->slots(':slot1', 'content1');
		$widget->slots(':slot2', 'content2');
		$this->assertContains('content1', $widget->render());
		$this->assertContains('content2', $widget->render());
	}

	public function test_swap()
	{
		$form = Form_Builder::factory(array("field2" => "value 2", "field1" => "value 1", "field3" => "value 3"));
		$widget = $form->widget('field1')->template(":slot1, :slot2 :field");
		$widget->swap_slots(":slot1", ":slot2");
		$this->assertEquals(":slot2, :slot1 :field", $widget->template());
	}

	public function provider_required()
	{
		return array(
			array(array('test1' => 'test1'),                     array('test1')),
			array(array('test1' => 'test1', 'test2' => 'test2'), array('test8'), 'Kohana_Exception'),
			array(array('test1' => 'test1', 'test2' => 'test2'), array('test1', 'test2')),
			array(array('test1' => 'test1', 'test2' => 'test2'), array('test3', 'test2'), 'Kohana_Exception'),
		);
	}

	/**
	 * @dataProvider provider_required
	 */
	public function test_required($options, $required, $exception = null)
	{
		$form = Form_Builder::factory(array("field2" => "value 2", "field1" => "value 1", "field3" => "value 3"));
		$widget = $form->widget('field1')->options($options);

		if($exception)
		{
			$this->setExpectedException($exception);
		}
		$widget->required($required);
	}

	public function provider_errors()
	{
		return array(
			array(array('name' => 'value'), "Error in field", "Error in field"),
			array(array('name' => 'value'), array("Error in field", "Second Error"), array("Error in field", "Second Error")),
			array(array('name' => 'value'), array('name' => "Error in field"), "Error in field"),
			array(array('name' => 'value', 'name2' => 'value2'), array('name' => "Error in field", 'name2' => 'Error 2'), "Error in field"),
		);
	}

	/**
	 * @dataProvider provider_errors
	 */
	public function test_errors($items, $errors, $expected_errors)
	{
		$widget = new Form_Widget_Object($items);
		$widget->errors($errors);
		$errors = (array) $errors;
		$this->assertEquals($widget->errors(), $widget->first_item()->errors());
		$this->assertEquals($expected_errors, $widget->errors());
	}

}