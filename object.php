<?php
namespace tomlib\object;

function try_get_property($object, $name, $default = null) {
		if(property_exists($object, $name))
				return $object->$name;
		else
				return $default;
}

function to_indexed_array($object, $properties, $reference = false) {
		if($reference) {
				foreach($properties as $property) {
						$result[$property] = $object->$property;
				}
		} else {
				foreach($properties as $property) {
						$result[$property] = $object->$property;
				}
		}

		return $result;
}

function to_array($object, $properties, $reference = false) {
		if($reference) {
				foreach($properties as $property) {
						$result[] =& $object->$property;
				}
		} else {
				foreach($properties as $property) {
						$result[] = $object->$property;
				}
		}

		return $result;
}
