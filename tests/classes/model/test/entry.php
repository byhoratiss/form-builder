<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Represents an author in the database.
 *
 * @package  Jelly
 */
class Model_Test_Entry extends Jelly_Model {

	public static function initialize(Jelly_Meta $meta)
	{
		// Define fields
		$meta->fields(array(
			'id'         => Jelly::field('primary'),
			'name'       => Jelly::field('string'),
			'password'   => Jelly::field('password'),
			'email'      => Jelly::field('email'),
		 ));
	}

} // End Model_Test_Author