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
		$this->assertInstanceOf('Form_Validation_Builder', Form_Builder::factory($validation));

		$jelly_model = $this->getMockBuilder("Jelly_Model")->disableOriginalConstructor()->getMock();
		$jelly_model->expects($this->any())->method('as_array')->will($this->returnValue(array()));
		$this->assertInstanceOf('Form_Jelly_Builder', Form_Builder::factory($jelly_model));		
	}

	public function test_getters()
	{
		$form = Form_Builder::factory(array("test" => "default"));
		$this->assertEquals("%s", $form->prefix(), "Should have a default prefix");
		$this->assertEquals(array("test" => "default"), $form->data(), "Should get a default data from the constructor");

		$this->assertEquals("default", $form->value("test"), "Should get a data set from the constructor");

		$this->assertInstanceOf("Form_Renderer", $form->renderer() );
	}

	public function test_setters()
	{
		$form = Form_Builder::factory(array("test" => "default"));
		$form
			->prefix("parent[%s]")
			->renderer("Form_Validation_Renderer")
			->data(array("test2" => "value"));

		$this->assertEquals("parent[%s]", $form->prefix(), "Should have a prefix set from the setter");
		$this->assertEquals(array("test2" => "value"), $form->data(), "Should get a data set from the setter");

		$this->assertEquals("value", $form->value("test2"), "Should get a data set from the setter");

		$this->assertInstanceOf("Form_Validation_Renderer", $form->renderer() );
	}

	public function test_renderer()
	{
		$form = Form_Builder::factory(array("test" => "default"));
		$renderer = $this->getMock('Form_Renderer', array('label', 'row', 'field'), array($form));
		$form->renderer($renderer);
		
		$renderer->expects($this->once())->method("row")->with('input', 'test', $form->value("test"), array(), array());
		$form->row("input", "test", array(), array());

		$renderer->expects($this->once())->method("field")->with('input', 'test', $form->value("test"), array(), array());
		$form->field("input", "test", array(), array());

		$renderer->expects($this->once())->method("label")->with('input', 'test');
		$form->label("input", "test");

		$form->prefix("name1[%s]");
		$this->assertEquals("name1[%s]", $form->renderer()->prefix(), "Should be able to set prefix through the builder");

		$form->renderer("Form_Validation_Renderer");
		$this->assertEquals("name1[%s]", $form->renderer()->prefix(), "Should be able to set prefix through the builder even after changing the renderer");		
	}

}