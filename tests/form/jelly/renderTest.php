<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Jelly_RenderTest extends Kohana_Unittest_TestCase {

	static public function setUpBeforeClass()
	{
		require_once Kohana::find_file("tests", "classes/jelly");
		require_once Kohana::find_file("tests", "classes/model/test/entry");
	}


	public function test_parameters()
	{
		$jelly = new Model_Test_Entry();
		$renderer = Form_Builder::factory($jelly, array("name" => "new_name"))->renderer();

		$parameters = $renderer->parameters("test", array());

		$this->assertArrayHasKey(":errors", $parameters);
		$this->assertArrayHasKey(":with-errors", $parameters);
	}

	public function test_errors()
	{
		$jelly = new Model_Test_Entry();
		$renderer = Form_Builder::factory($jelly, array("name" => "new_name"))->renderer();

		$this->assertEquals("", $renderer->errors(array()));
		$this->assertNotEmpty($renderer->errors(array('test' => 'error')));
	}


	public function provider_widgets()
	{
		return array(
			array("select",   array(), null, "Kohana_Exception"),
			array("select",   array("choices" => array(1)), "select[name=test] option"),
			array("select",   array("include_blank" => "BLANK", "choices" => array(1)), "option[value=]"),
			array("date",     array(), "input[type=date]"),
			array("input",    array(), "input[type=text]"),
			array("hidden",   array(), "input[type=hidden]"),
			array("textarea", array(), "textarea[name=test]"),
			array("checkbox", array(), "input[type=checkbox]"),
			array("image",    array(), null, "Kohana_Exception"),
			array("image",    array('path' => '/'), "input[type=file]"),
		);
	}

	/**
	 * @dataProvider provider_widgets
	 */
	public function test_widgets($widget, $options, $selector, $exception = null)
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();

		if($exception)
		{
			$this->setExpectedException($exception);
		}

		$field = $renderer->field($widget, "test", null, $options);
		$row = $renderer->row($widget, "test", null, $options);

		$this->assertNotEmpty($field);
		$this->assertNotEmpty($row);
		$this->assertContains($field, $row);

		if( $selector )
		{
			$this->assertSelectCount($selector, true, $field, "Should have the tag $selector in the returned html $field");
		}
	}			
}