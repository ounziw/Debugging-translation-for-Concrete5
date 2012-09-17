<?php 
defined('C5_EXECUTE') or die("Access Denied.");
// Put this file into concrete/startup/localization.php

function remove_percent_from_text($text) {
	$removed = array('$','%');
	$no_var_text = str_replace($removed,'_',$text);
	$no_var_text .= ' '; // Space for Readability
	return $no_var_text;
}

function t($text) {
	$no_var_text = remove_percent_from_text($text);
	$zt = Localization::getTranslate();
	if (func_num_args() == 1) {
		if (is_object($zt)) {
			return $no_var_text . $zt->_($text);
		} else {
			return $text;
		}
	}
	
	$arg = array();
	for($i = 1 ; $i < func_num_args(); $i++) {
		$arg[] = func_get_arg($i); 
	}
	if (is_object($zt)) {
		return vsprintf($no_var_text . $zt->_($text), $arg);
	} else {
		return vsprintf($no_var_text . $text, $arg);
	}
}

/** Translate text (plural form).
* @param string $singular The singular form.
* @param string $plural The plural form.
* @param int $number The number.
* @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf
* @return string Returns the translated text.
* @example t2('%d child', '%d children', $n) will return translated '%d child' if $n is 1, translated '%d children' otherwise.
* @example t2('%d child', '%d children', $n, $n) will return translated '1 child' if $n is 1, translated '2 children' if $n is 2.
*/
function t2($singular, $plural, $number) {
	$no_var_text_singular = remove_percent_from_text($singular);
	$no_var_text_plural = remove_percent_from_text($plural);
	$zt = Localization::getTranslate();
	if(is_object($zt)) {
		$translated = $zt->plural($no_var_text_singular . $singular, $no_var_text_plural . $plural, $number);
	} else {
		$translated = ($number == 1) ? $no_var_text_singular . $singular : $no_var_text_plural . $plural;
	}
	if(func_num_args() == 3) {
		return $translated;
	}
	$arg = array();
	for($i = 3; $i < func_num_args(); $i++) {
		$arg[] = func_get_arg($i);
	}
	return vsprintf($translated, $arg);
}
