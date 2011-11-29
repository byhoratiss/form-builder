<?php

/**
 * 
 * @package    OpenBuildings/form-builder
 * @author     Ivan Kerin
 * @copyright  (c) 2011 OpenBuildings Inc.
 * @license    http://creativecommons.org/licenses/by-sa/3.0/legalcode
 */
class Form_Widgets 
{
	static public function _list_choices($choices)
	{
		if($choices instanceof Jelly_Builder)
		{
			$choices = $choices->select_all();
		}

		if($choices instanceof Jelly_Collection)
		{
			$choices = $choices->as_array($choices->meta()->primary_key(), $choices->meta()->name_key());
		}		


		return $choices;
	}

	static public function _list_values($values)
	{
		if ($values instanceof Jelly_Collection) 
		{
			return $values->as_array(null, $values->meta()->primary_key());
		}
		elseif ($values instanceof Jelly_Model)
		{
			return $values->{$values->meta()->primary_key()};
		}
		else
		{
			return $values;
		}
	}

	static public function select(Form_Widget $data)
	{
		$choices = self::_list_choices($data->required('choices')->options('choices'));
		
		if($blank = $data->options('include_blank'))
		{
			Arr::unshift($choices, '', ($blank === TRUE) ? " -- Select -- " : $blank);
		}

		return Form::select($data->name(), $choices, self::_list_values($data->value()), $data->attributes()->as_array());
	}

	static public function date(Form_Widget $data)
	{
		$data->attributes(array('type' => 'date', 'data-type' => 'date'));

		return Form::input($data->name(), $data->value(), $data->attributes()->as_array());
	}	

	static public function datetime(Form_Widget $data)
	{
		$data->attributes()->merge(array('type' => 'text', 'data-type' => 'datetime'));

		return Form::input($data->name(), $data->value(), $data->attributes()->as_array());
	}		

	static public function input(Form_Widget $data)
	{
		return Form::input($data->name(), $data->value(), $data->attributes()->as_array());
	}

	static public function file(Form_Widget $data)
	{
		return Form::file($data->name(), $data->attributes()->as_array());
	}	

	static public function hidden(Form_Widget $data)
	{
		return Form::hidden($data->name(), $data->value(), $data->attributes()->as_array());
	}

	static public function password(Form_Widget $data)
	{
		$value = null;
		if( $data->options('value') )
		{
			$value = is_string($data->options('value')) ? $data->options('value') : $data->value();
		}
		return Form::password($data->name(), $value, $data->attributes()->as_array());
	}			

	static public function textarea(Form_Widget $data)
	{
		return Form::textarea($data->name(), $data->value(), $data->attributes()->as_array());
	}

	static public function checkbox(Form_Widget $data)
	{
		return 
			Form::hidden($data->name(), null).
			Form::checkbox($data->name(), 1, (bool) $data->value(), $data->attributes()->as_array());
	}

	static public function radio(Form_Widget $data)
	{
		return 
			Form::hidden($data->name(), null).
			Form::radio($data->name(), 1, (bool) $data->value(), $data->attributes()->as_array());
	}	

	static public function image(Form_Widget $data)
	{
		$data->required('path');

		return strtr('<div class="image-field">:image :input</div>', array(
			":image" => $data->value() ? HTML::image($data->options('path').$data->value()) : '<div class="image-placeholder"></div>', 
			":input" => Form::file($data->name(), $data->value(), $data->attributes()->as_array())
		));
	}

	static public function checkboxes(Form_Widget $data)
	{
		$data->required('choices');
		$data->attributes()->add_class('inputs-list');
		$html = '';
				 
		$values =  self::_list_values($data->value());

		foreach(self::_list_choices($data->options('choices')) as $key => $title)
		{
			$html .= '<li>'.
				Form::label($data->id().'_'.$key, Form::checkbox($data->name()."[]", $key, in_array($key, $values), array("id" => $data->id().'_'.$key))."<span>$title</span>").
			'</li>';
		}
		return "<ul ".HTML::attributes($data->attributes()->as_array()).">$html</ul>";
	}


	static public function radios(Form_Widget $data)
	{
		$choices = self::_list_choices($data->required('choices')->options('choices'));
		$data->attributes()->add_class('inputs-list');

		$html = '';

		if($blank = $data->options('include_blank'))
		{
			Arr::unshift($choices, '', ($blank === TRUE) ? " -- Select -- " : $blank);
		}

		foreach($choices as $key => $title)
		{
			$html .= '<li>'.
				Form::label($data->id().'_'.$key, Form::radio($data->name(), $key, $key == $data->value(), array("id" => $data->id().'_'.$key))."<span>$title</span>").
			'</li>';
		}
		return "<ul ".HTML::attributes($data->attributes()->as_array()).">$html</ul>";		
	}
	
	static public function upload_image(Form_Widget $data) {
		return Form::file($data->name(), $data->attributes()->as_array());
	}
	
}