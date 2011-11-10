<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_BuilderTest extends Kohana_Unittest_TestCase {

	public function test_factory()
	{
		$this->assertInstanceOf('Form_Builder', Form_Builder::factory(array("test")));

		$validation = $this->getMockBuilder("Validation")->disableOriginalConstructor()->getMock();
		$this->assertInstanceOf('Form_Builder_Validation', Form_Builder::factory($validation));

		$jelly_model = $this->getMockBuilder("Jelly_Model")->disableOriginalConstructor()->getMock();
		$jelly_model->expects($this->any())->method('as_array')->will($this->returnValue(array()));
		$this->assertInstanceOf('Form_Builder_Jelly', Form_Builder::factory($jelly_model));		
	}

	public function test_getters()
	{
		$form = Form_Builder::factory(array("test" => "default"))->prefix('name[%s]');
		$this->assertEquals("name[%s]", $form->prefix(), "Should have a default prefix");
		$this->assertEquals(array("test" => "default"), $form->data(), "Should get a default data from the constructor");

		$this->assertEquals("default", $form->value("test"), "Should get a data set from the constructor");

		$widget = $form->widget('test');
		$this->assertInstanceOf("Form_Widget", $widget );
		$this->assertEquals($widget->prefix(), $form->prefix());
		$this->assertEquals($widget->value(), $form->value('test'));
	}

	public function test_setters()
	{
		$form = 
			Form_Builder::factory(array("test" => "default"))
				->prefix("parent[%s]")
				->data(array("test2" => "value"));

		$this->assertEquals("parent[%s]", $form->prefix(), "Should have a prefix set from the setter");
		$this->assertEquals(array("test2" => "value"), $form->data(), "Should get a data set from the setter");
		$this->assertEquals("value", $form->value("test2"), "Should get a data set from the setter");
	}

	public function provider_row_and_field()
	{
		return array(
			array('input', 'input[type=text][name=test][value=default]'),
			array('password', 'input[type=password][name=test][value=default]'),
			array('file', 'input[type=file][name=test]'),
			array('date', 'input[type=date][name=test][value=default]'),
			array('checkbox', 'input[type=checkbox][name=test][value=1]'),
			array('radio', 'input[type=radio][name=test][value=1]'),
			array('datetime', 'input[type=date][name=test][value=default]'),
			array('textarea', 'textarea[name=test]'),
			array('select', 'select[name=test] option[value=1]', array("choices" => array('1' => 'one', '2' => 'two'))),
			array('checkboxes', 'ul li input[type=checkbox][name=test[]][value=2]', array("choices" => array('1' => 'one', '2' => 'two'))),
			array('radios', 'ul li input[type=radio][name=test][value=2]', array("choices" => array('1' => 'one', '2' => 'two'))),
		);
	}

	/**
	 * @dataProvider provider_row_and_field
	 */
	public function test_row_and_field($widget_name, $expected, $options = null)
	{
		$form = Form_Builder::factory(array("test" => "default"));
		$field = $form->field($widget_name, 'test', (array) $options);
		$this->assertInstanceOf("Form_Widget", $field );
		$this->assertSelectCount($expected, 1, (string) $field, "$field should contain $expected");


		$row = $form->row($widget_name, 'test', (array) $options);
		$this->assertSelectCount($expected, 1, $row, "$row should contain $expected");

	}


}