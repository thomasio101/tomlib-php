<?php
namespace tomlib\object;

function try_get_property($object, $name, $default = null) {
		if(property_exists($object, $name))
				return $object->$name;
		else
				return $default;
}
