<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Validation_BuilderTest extends Kohana_Unittest_TestCase {


	public function test_getters()
	{
		$valid = Validation::factory(array("test" => "default"));
		$form = Form_Builder::factory($valid);
		$this->assertEquals($valid, $form->object(), "Should have a default object");
		$this->assertEquals(null, $form->error_file(), "Should not have an error file set by default");
	}

	public function test_setters()
	{
		$valid = Validation::factory(array("test" => "default"));
		$valid2 = Validation::factory(array("test2" => "default2"));

		$form = Form_Builder::factory($valid);
		$form
			->error_file("error_file")
			->object($valid2);

		$this->assertEquals("error_file", $form->error_file(), "Should have error_file set from the setter");
		$this->assertEquals($valid2, $form->object(), "Should get the object set from the setter");
	}

	public function test_check()
	{
		$valid = $this->getMock("Validation", array("check", "errors"), array(array("test" => "default")));
		$valid->expects($this->at(0))->method("check")->will($this->returnValue(true));
		$valid->expects($this->at(1))->method("check")->will($this->returnValue(false));
		$valid->expects($this->any())->method("errors")->with(null)->will($this->returnValue(array("test" => "error")));

		$form = Form_Builder::factory($valid);

		$this->assertNull($form->errors());
		$this->assertTrue($form->check());
		$this->assertNull($form->errors());

		$this->assertFalse($form->check());
		$this->assertNotNull($form->errors());
	}
}