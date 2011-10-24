<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Validation_RendererTest extends Kohana_Unittest_TestCase {

	public function test_parameters()
	{

		$valid = Validation::factory(array("test" => "default"));
		$renderer = Form_Builder::factory($valid)->renderer();

		$parameters = $renderer->parameters("test", array());

		$this->assertArrayHasKey(":errors", $parameters);
		$this->assertArrayHasKey(":with-errors", $parameters);
	}

	public function test_errors()
	{
		$valid = Validation::factory(array("test" => "default"));
		$renderer = Form_Builder::factory($valid)->renderer();

		$this->assertEquals("", $renderer->errors(array()));

		$this->assertNotEmpty($renderer->errors(array('test' => 'error')));
		
	}

}