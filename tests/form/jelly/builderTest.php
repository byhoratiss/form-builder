<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Jelly_BuilderTest extends Kohana_Unittest_TestCase {

	static public function setUpBeforeClass()
	{
		require_once Kohana::find_file("tests", "classes/jelly");
		require_once Kohana::find_file("tests", "classes/model/test/entry");
	}

	public function test_getters()
	{
		$jelly = new Model_Test_Entry();
		$form = Form_Builder::factory($jelly, array("name" => "new_name"));

		$this->assertEquals($jelly, $form->object(), "Should have a default object");
		$this->assertEquals(null, $form->error_file(), "Should not have an error file set by default");
	}

	public function test_setters()
	{
		$jelly = new Model_Test_Entry();
		$jelly2 = new Model_Test_Entry();
		$form = Form_Builder::factory($jelly, array("name" => "new_name"));

		$form = Form_Builder::factory($jelly);
		$form
			->error_file("error_file")
			->object($jelly2);

		$this->assertEquals("error_file", $form->error_file(), "Should have error_file set from the setter");
		$this->assertEquals($jelly2, $form->object(), "Should get the object set from the setter");
	}

	public function test_check()
	{
		$jelly = $this->getMock("Model_Test_Entry", array("check", "save"), array(), "Model_Test_Entry_Mock");
		$jelly->expects($this->once())->method("save")->will($this->returnValue(true));
		$jelly->expects($this->at(0))->method("check")->will($this->returnValue(true));
		$jelly->expects($this->at(1))->method("check")->will($this->throwException(new Jelly_Validation_Exception("model_test", Validation::factory(array())->error("name", "error"))));
		$jelly->expects($this->at(2))->method("check")->will($this->returnValue(true));

		$form = Form_Builder::factory($jelly,  array("test" => "default", "name" => 'test'));
		$this->assertArrayHasKey("id", $form->data());
		$this->assertArrayHasKey("name", $form->data());
		$this->assertArrayHasKey("email", $form->data());
		$this->assertArrayHasKey("test", $form->data());
		$this->assertArrayHasKey("test", $form->data());

		$this->assertNull($form->errors());

		$this->assertTrue($form->check());
		$this->assertNull($form->errors());

		$this->assertFalse($form->check());
		$this->assertNotNull($form->errors());

		$this->assertTrue($form->check(TRUE));
		$this->assertNull($form->errors());

	}


}