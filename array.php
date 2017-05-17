<?php
namespace tomlib\array_util;

function try_get_element($array, $key, $default = null) {
		if(array_key_exists($key, $array))
				return $array[$key];
		else
				return $default;
}

function index($array, $key_function) {
		$result = array();

		foreach($array as $element)
				$result[$key_function($element)] = $element;

		return $result;
}

function index_by_property($array, $property) {
		$result = array();

		foreach($array as $element)
				$result[$element->$property] = $element;

		return $result;
}

function group($array, $key_function) {
		$result = array();

		foreach($array as $element)
				$result[$key_function($element)][] = $element;

		return $result;
}

function group_by_property($array, $property) {
		$result = array();

		foreach($array as $element)
				$result[$element->$property][] = $element;

		return $result;
}
