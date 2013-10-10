<?php

	

require_once('../underscore_kit/underscore_kit.php');

__kit::initialize();

$test = array(
	'key' => null,
);

echo __::has($test, 'key');