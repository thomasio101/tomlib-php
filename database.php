<?php
namespace tomlib\database;

include_once dirname(__FILE__) . "/config.php";
include_once dirname(__FILE__) . "/object.php";

use tomlib\config as Config;
use tomlib\object as Object;

use mysqli;

function get_database_config($name) {
		$databases = Config\get_setting("databases");

		return Object\try_get_property($databases, $name);
}

function get_table_config($name) {
		$tables = Config\get_setting("tables");

		return Object\try_get_property($tables, $name);
}

function connect_database($name) {
		$database_config = get_database_config($name);

		$hostname = $database_config->hostname;
		$username = $database_config->username;
		$password = $database_config->password;
		$database_name = $database_config->name;

		return new mysqli($hostname, $username, $password, $database_name);
}

function table_to_array($name, $classname = "stdClass") {
		$table_config = get_table_config($name);

		$database_name = $table_config->database;

		$connection = connect_database($database_name);

		$stmt = $connection->prepare("SELECT * FROM $table_config->name;");

		$result = array();

		if($stmt->execute()) {
				$res = $stmt->get_result();

				while($row = $res->fetch_object($classname))
						$result[] = $row;

				$stmt->close();
		}

		$connection->close();

		return $result;
}

function table_to_indexed_array($name, $key_field, $classname = "stdClass") {
		$table_config = get_table_config($name);

		$database_name = $table_config->database;

		$connection = connect_database($database_name);

		$stmt = $connection->prepare("SELECT * FROM $table_config->name;");

		$result = array();

		if($stmt->execute()) {
				$res = $stmt->get_result();

				while($row = $res->fetch_object($classname))
						$result[$row->$key_field] = $row;

				$stmt->close();
		}

		$connection->close();

		return $result;
}

function get_typestring($table_name, $columns = null) {
		$table_config = get_table_config($table_name);

		$typestring = null;

		for($i = 0; $i < count($table_config->columns); $i++) {
				if($columns == null)
						$typestring .= $table_config->columns[$i]->type;
				else if(in_array($table_config->columns[$i]->type, $columns))
						$typestring .= $table_config->columns[$i]->type;
		}

		return $typestring;
}

function get_setstring($column_names) {
		$setstring = null;

		for($i = 0; $i < count($column_names); $i++) {
				$column_name = $column_names[$i];

				$setstring .= is_null($setstring) ? "$column_name = ?" : ", $column_name = ?";
		}

		return $setstring;
}
