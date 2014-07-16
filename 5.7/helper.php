<?
defined('C5_EXECUTE') or die('Access Denied.');

use Concrete\Core\Foundation\Environment as Environment;
use Concrete\Core\Foundation\Object as Object;

// Put this file into concrete/bootstrap/helper.php

/**
 * Translate text (simple form).
 * @param string $text The text to be translated.
 * @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf.
 * @return string Returns the translated text.
 * @example t('Hello %s') will return translation for 'Hello %s' (example for Italian 'Ciao %s').
 * @example t('Hello %s', 'John') will return translation for 'Hello %s' (example: 'Ciao %s'), using 'John' for printf (so the final result will be 'Ciao John' for Italian).
 */
function t($text) {
    $zt = Localization::getTranslate();
    if (func_num_args() == 1) {
        if (is_object($zt)) {
            return $text . $zt->_($text);
        } else {
            return $text;
        }
    }
    $arg = array();
    for($i = 1 ; $i < func_num_args(); $i++) {
        $arg[] = func_get_arg($i);
    }
    if (is_object($zt)) {
        return $text . vsprintf($zt->_($text), $arg);
    } else {
        return $text . vsprintf($text, $arg);
    }
}

/**
 * Translate text (plural form).
 * @param string $singular The singular form.
 * @param string $plural The plural form.
 * @param int $number The number.
 * @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf
 * @return string Returns the translated text.
 * @example t2('%d child', '%d children', $n) will return translated '%d child' if $n is 1, translated '%d children' otherwise.
 * @example t2('%d child', '%d children', $n, $n) will return translated '1 child' if $n is 1, translated '2 children' if $n is 2.
 */
function t2($singular, $plural, $number) {
    $zt = Localization::getTranslate();
    if(is_object($zt)) {
        $translated = $zt->plural($singular, $plural, $number);
    } else {
        $translated = $number == 1 ? $singular : $plural;
    }
    $arg = array_slice(func_get_args(), 3);
    if ($arg) {
        return $singular . $plural . vsprintf($translated, $arg);
    }
    return $singular . $plural . vsprintf($translated, $number);
}

/**
 * Translate text (simple form) with a context.
 * @param string $context A context, useful for translators to better understand the meaning of the text to be translated.
 * @param string $text The text to be translated.
 * @param mixed ... Unlimited optional number of arguments: if specified they'll be used for printf.
 * @return string Returns the translated text.
 * @example tc('Recipient', 'To %s') will return translation for 'To %s' (example for Italian 'A %s').
 * @example tc('End date', 'To %s') will return translation for 'To %s' (example for Italian 'Fino al %s').
 * @example tc('Recipient', 'To %s', 'John') will return translation for 'To %s' (example: 'A %s'), using 'John' for printf (so the final result will be 'A John' for Italian).
 * @example tc('End date', 'To %s', '01/01/2000') will return translation for 'To %s' (example: 'Fino al %s'), using '01/01/2000' for printf (so the final result will be 'Fino al 01/01/2000' for Italian).
 */
function tc($context, $text) {
    $zt = Localization::getTranslate();
    if (is_object($zt)) {
        $msgid = $context . "\x04" . $text;
        $msgtxt = $zt->_($msgid);
        if($msgtxt != $msgid) {
            $text = $msgtxt;
        }
    }
    if (func_num_args() == 2) {
        return $text;
    }
    $arg = array();
    for($i = 2 ; $i < func_num_args(); $i++) {
        $arg[] = func_get_arg($i);
    }
    return $text . sprintf($text, $arg);
}

/**
 * Security helper
 */
function h($input) {
    return Loader::helper('text')->specialchars($input);
}


/**
 *  Returns a concrete5 namespaced class
 */
function core_class($class, $prefix = false) {
    $class = trim($class, '\\');
    if ($prefix) {
        if ($prefix === true) {
            $prefix = NAMESPACE_SEGMENT_APPLICATION;
        } else {
            $prefix = 'Concrete\\Package\\' . camelcase($prefix);
        }
    }

    if (!$prefix) {
        $prefix = 'Concrete';
    }

    $class = '\\' . $prefix . '\\' . $class;
    return $class;
}

/**
 * Returns $string in CamelCase
 * @param  [string] $string
 * @return [string]
 */
function camelcase($string, $leaveSlashes = false) {
    $return = '';
    $string = trim($string, '_-/\\');
    if (strpos($string, '/')) {
        $segments = explode('/', $string);
        foreach($segments as $segment) {
            $subsegments = preg_split("/[_-]/", $segment);
            foreach($subsegments as $subsegment) {
                $return .= ucfirst($subsegment);
            }
            if ($leaveSlashes) {
                $return .= '/';
            }
        }
        $return = trim($return, '/');
    } else {
        $segments = preg_split("/[_-]/", $string);
        foreach($segments as $segment) {
            $return .= ucfirst($segment);
        }
    }

    return $return;
}


/**
 * Returns CamelCase string as camel_case
 * @param  [string] $string
 * @return [string]
 */
function uncamelcase($string) {
    $v = preg_split('/([A-Z])/', $string, false, PREG_SPLIT_DELIM_CAPTURE);
    $a = array();
    array_shift($v);
    for($i = 0; $i < count($v); $i++) {
        if ($i % 2) {
            if (function_exists('mb_strtolower')) {
                $a[] = mb_strtolower($v[$i - 1] . $v[$i], APP_CHARSET);
            } else {
                $a[] = strtolower($v[$i - 1] . $v[$i]);
            }
        }
    }
    return str_replace('__', '_', implode('_', $a));
}

/**
 * Fills an object properties from an array
 */
function array_to_object($o, $array) {
    foreach ($array as $property => $value) {
        $o->$property = $value;
    }
    return $o;
}