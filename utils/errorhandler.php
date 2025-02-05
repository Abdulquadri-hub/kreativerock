<?php

function errorhandler($error = '') {
   $debug = debug_backtrace()[1];
	$line = $debug['line'];
	$file = $debug['file'];
	error_log("[" . date("D d-m-Y h:i:s A T") . "] Error: " . $error . " at $file on line $line \n", 3, "error.log");
}
