<?php
namespace tomlib\file;

function read($filename) {
		$file = fopen($filename, "r");

		$content = fread($file, filesize($filename));

		fclose($file);

		return $content;
}

function read_json($filename) {
		$content = read($filename);

		return json_decode($content);
}
