<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Tests for core Jelly methods.
 *
 * @package Form_Builder
 */
class Jelly extends Jelly_Core {

	public static function model_name($model)
	{
		$model = parent::model_name($model);
		if(substr($model, -5) == '_mock')
		{
			$model = substr($model, 0, strlen($model) - 5);
		}
		return $model;
	}
}