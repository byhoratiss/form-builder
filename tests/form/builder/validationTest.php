<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Builder_ValidaitonTest extends Kohana_Unittest_TestCase {

	public function test_validation()
	{
		$validation = $this->getMock("Validation", array("check"), array(array("field" => "value")));

		$validation->expects($this->at(0))->method('check')->will($this->returnValue(true));
		$validation->expects($this->at(1))->method('check')->will($this->returnValue(false));

		$form = new Form_Builder_Validation($validation);

		$this->assertTrue($form->check());
		$this->assertFalse($form->check());
	}

	public function test_errors()
	{
		$validation = Validation::factory(array("test" => "value"))->rule("test", "valid::numeric");
		$form = new Form_Builder_Validation($validation);

		$this->assertFalse($form->check());

		$this->assertArrayHasKey("test", $form->errors());
	}


}