<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Form_Widget_AttributesTest extends Kohana_Unittest_TestCase {

	public function provider_add_class()
	{
		return array(
			array(array(), 'class1', 'class1'),
			array(array('style' => 'style'), 'class1', 'class1'),
			array(array('class' => 'class0'), 'class1', 'class0 class1'),
			array(array('class' => 'class0 class1'), 'class1', 'class0 class1'),
			array(array('class' => 'class2 class1'), 'class1', 'class2 class1'),
			array(array('class' => 'class0 class1'), array('class1', 'class2'), 'class0 class1 class2'),
		);
	}

	/**
	 * @dataProvider provider_add_class
	 */
	public function test_add_class($attributes, $add_class, $result_class)
	{
		$attrs = new Form_Widget_Attributes($attributes);

		$attrs->add_class($add_class);
		$this->assertEquals($result_class, $attrs['class']);
	}

	public function provider_remove_class()
	{
		return array(
			array(array(), 'class1', ''),
			array(array('style' => 'style'), 'class1', ''),
			array(array('class' => 'class0'), 'class0', ''),
			array(array('class' => 'class0 class1'), 'class1', 'class0'),
			array(array('class' => 'class2 class1'), 'class1', 'class2'),
			array(array('class' => 'class0 class1'), array('class1', 'class2'), 'class0'),
		);
	}

	/**
	 * @dataProvider provider_remove_class
	 */
	public function test_remove_class($attributes, $remove_class, $result_class)
	{
		$attrs = new Form_Widget_Attributes($attributes);

		$attrs->remove_class($remove_class);
		$this->assertEquals($result_class, $attrs['class']);
	}	
}