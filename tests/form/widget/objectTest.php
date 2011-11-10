<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Validation_ObjectTest extends Kohana_Unittest_TestCase {

	public function test_type()
	{
		$valid = Validation::factory(array("test" => "default"));
		$form = Form_Builder::factory($valid);
		$widget = $form->widget('test');
		$this->assertInstanceOf("Form_Widget_Object", $widget);
		$this->assertSame($valid, $widget->object());
	}

}