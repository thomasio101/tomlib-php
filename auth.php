<?php
namespace tomlib\auth;

include_once dirname(__FILE__) . "/authConfig.php";
include_once dirname(__FILE__) . "/database.php";
include_once dirname(__FILE__) . "/array.php";
include_once dirname(__FILE__) . "/base64.php";

use tomlib\auth\config as AuthConfig;
use tomlib\database as Database;
use tomlib\array_util as ArrayUtil;
use tomlib\base64 as Base64;

class User {
		public $id;
		public $username;
		public $hash;

		function __construct($username = null, $password = null) {
				if(is_null($username) || is_null($password))
						return;

				$this->username = $username;
				$this->hash = password_hash($password, PASSWORD_DEFAULT);
		}

		function register() {
				register_user($this);
		}

		function check_password($password) {
				return check_password($this, $password);
		}

		function verify_token($token) {
				return verify_token($this, $token);
		}

		function get_token() {
				$token = generate_token();

				insert_token($this, $token);

				return $token;
		}
}

function get_users() {
		$table_name = AuthConfig\get_setting("userTable");

		$users = Database\table_to_array($table_name, "tomlib\auth\User");

		return $users;
}

function get_user($username) {
		$users = get_users();
		$users = ArrayUtil\index_by_property($users, "username");

		return ArrayUtil\try_get_element($users, $username);
}

function get_tokens() {
		$table_name = AuthConfig\get_setting("tokenTable");

		$tokens = Database\table_to_array($table_name);

		return $tokens;
}

function get_tokens_for_user($user) {
		$tokens = get_tokens();
		$tokens = ArrayUtil\group_by_property($tokens, "userId");

		return ArrayUtil\try_get_element($tokens, $user->id, []);
}

function register_user(&$user) {
		$table_name = AuthConfig\get_setting("userTable");

		$table_config = Database\get_table_config($table_name);

		$database_name = $table_config->database;

		$connection = Database\connect_database($database_name);

		$stmt = $connection->prepare("INSERT INTO $table_config->name SET username = ?, hash = ?;");

		$stmt->bind_param("ss", $user->username, $user->hash);

		$stmt->execute();

		$user->id = $connection->insert_id;

		$connection->close();
}

function check_password($user, $password) {
		$hash = $user->hash;

		return password_verify($password, $hash);
}

function generate_token() {
		$seed = rand(0, 16777216);

		$token = Base64\url_encode($seed);

		return $token;
}

function insert_token($user, $token) {
		$table_name = AuthConfig\get_setting("tokenTable");

		$table_config = Database\get_table_config($table_name);

		$database_name = $table_config->database;

		$connection = Database\connect_database($database_name);

		$stmt = $connection->prepare("INSERT INTO $table_config->name SET userId = ?, token = ?;");

		$token_hash = password_hash($token, PASSWORD_DEFAULT);

		$stmt->bind_param("is", $user->id, $token_hash);

		$stmt->execute();

		$connection->close();
}

function validate_token($user, $token) {
		$tokens = get_tokens_for_user($user);

		global $time_threshold;

		$time_threshold = time() - 43200;

		$key_function = function($element) {
				global $time_threshold;

				return time($element->timestamp) > $time_threshold;
		};

		$tokens = ArrayUtil\group($tokens, $key_function);

		$valid_tokens = ArrayUtil\try_get_element($tokens, 1, []);

		foreach($valid_tokens as $element) {
				if(password_verify($token, $element->token))
						return true;
		}

		return false;
}
