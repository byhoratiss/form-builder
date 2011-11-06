<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_RendererTest extends Kohana_Unittest_TestCase {

	public function test_prefix()
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();

		$this->assertEquals("%s", $renderer->prefix(), "Should have a default prefix");
	}

	public function test_getters_with_prefix()
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();
		$renderer->prefix("parent[%s]");

		$this->assertEquals("parent[test]", $renderer->html_name("test"), "Should have a default name with prefix");

		$this->assertEquals("parent_test", $renderer->html_id("test"), "Should have a default name with prefix");

		$this->assertEquals(array("name" => "parent[test]", "id" => "parent_test", "1" => "1", "2" => "2"), $renderer->html_attributes("test", array("1" => "1"), array("2" => "2")), "Should have a default name with prefix");
	}

	public function test_parameters()
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();

		$parameters = $renderer->parameters("test", array());

		$this->assertArrayHasKey(":label", $parameters);
		$this->assertArrayHasKey(":name", $parameters);
	}

	public function test_template_switching()
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();
		$this->assertNotEmpty($renderer->template("input"));
		$this->assertNotEmpty($renderer->template("checkbox"));

		$this->assertNotEquals($renderer->template("input"), $renderer->template("checkbox"), "Should have different template for checkboxes");
	}

	public function provider_options()
	{
		return array(
			array(array("a" => 1, "b" => 2), array("a", "b", "c"), null),
			array(array("a" => 1, "b" => 2), array("a", "b"), array('a')),
			array(array("a" => 1, "b" => 2), array("a", "b", 'c', 'd'), array('a', 'b')),
			array(array("a" => 1, "b" => 2), array("a", "b", 'c', 'd'), array('z')),
		);
	}

	/**
	 * @dataProvider provider_options
	 **/
	public function test_options($options, $existing, $required)
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();

		foreach((array) $required as $r)
		{
			if( ! isset($options[$r]))
			{
				$this->setExpectedException("Kohana_Exception");		
			}
		}
		$result_options = $renderer->options($options, $existing, $required);
		
		foreach((array) $existing as $r)
		{
			$this->assertArrayHasKey($r, $result_options);
		}
	}

	public function test_select_normal()
	{
		$renderer = Form_Builder::factory(array("test" => "default"))->renderer();

		$select = $renderer->field("select", "test", null, array("choices" => array(1, 2)));

		$this->assertContains("<select", $select, "Should have a select tag");
		$this->assertContains("<option", $select, "Should have a options tags");

		$with_blank = $renderer->field("select", "test", null,  array(
			"choices" => array(1, 2), 
			'include_blank' => 'BLANK',
		));
		$this->assertContains('<option value="">BLANK</option>', $with_blank, "Should contain blank option");
	}

	public function test_label()
	{
		$renderer = Form_Builder::factory(array("big_test" => "default"))->renderer();
		$this->assertContains("Big test", $renderer->label("big_test"));
		$this->assertContains("label for big test", $renderer->label("big_test", "label for big test"));
	}

	public function provider_widgets()
	{
		return array(
			array("select",    array(), null, "Kohana_Exception"),
			array("select",    array("choices" => array(1)), "select[name=test] option"),
			array("select",    array("include_blank" => "BLANK", "choices" => array(1)), "option[value=]"),
			array("date",      array(), "input[type=date]"),
			array("input",     array(), "input[type=text]"),
			array("hidden",    array(), "input[type=hidden]"),
			array("textarea",  array(), "textarea[name=test]"),
			array("checkbox",  array(), "input[type=checkbox]"),
			array("image",     array(), null, "Kohana_Exception"),
			array("image",     array('path' => '/'), "input[type=file]"),
			array("checkboxes",array('choices' => array('a' => 'a', 'b' => 'c')), "ul li input[type=checkbox][value=b]"),
			array("checkboxes",array(), null, "Kohana_Exception"),
			array("radios",array('choices' => array('a' => 'a', 'b' => 'c')), "ul li input[type=radio][value=b]"),
			array("radios",array(), null, "Kohana_Exception"),
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