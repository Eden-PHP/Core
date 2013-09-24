<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core;

/**
 * The base class for any class handling exceptions. Exceptions
 * allow an application to custom handle errors that would
 * normally let the system handle. This exception allows you to
 * specify error levels and error types. Also using this exception
 * outputs a trace (can be turned off) that shows where the problem
 * started to where the program stopped.
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Argument
{
    const INVALID_ARGUMENT = 'Argument %d in %s() was expecting %s, however %s was given.';

    protected static $stop = false;

	/**
	 * One of the hard thing about instantiating classes is
	 * that design patterns can impose different ways of
	 * instantiating a class. The word "new" is not flexible.
	 * Authors of classes should be able to control how a class
	 * is instantiated, while leaving others using the class
	 * oblivious to it. All in all its one less thing to remember
	 * for each class call. By default we instantiate classes with
	 * this method.
	 *
	 * @param [mixed[,mixed..]]
	 * @return object
	 */
    public static function i()
    {
        $class = __CLASS__;
        return new $class($message, $code);
    }

    /**
     * Tests arguments for valid data types
     *
     * @param *int
     * @param *mixed
     * @param *string[,string..]
     * @return Eden\Core\Argument
     */
    public function test($index, $types)
    {
        //if no test
        if(self::$stop) {
            return $this;
        }

        $trace = debug_backtrace();

		//lets formulate the method
        $method = $trace[1]['function'];
        if(isset($trace[1]['class'])) {
            $method = $trace[1]['class'].'->'.$method;
        }

		$args = func_get_args();
		array_unshift($args, $method, $trace[1]['args']);

		return call_user_func_array(array($this, 'virtual'), $args);
    }

    /**
     * In a perfect production environment,
     * we can assume that arguments passed in
     * all methods are valid. To increase
     * performance call this method.
     *
     * @return Eden\Core\Argument
     */
    public function stop()
    {
        self::$stop = true;
        return $this;
    }

    /**
     * Tests virtual arguments for valid data types
     *
	 * @param *string method name
	 * @param *array arguments
     * @param *int
     * @param *string[,string..]
     * @return Eden\Core\Argument
     */
    public function virtual($method, array $args, $index, $types)
    {
        //if no test
        if(self::$stop) {
            return $this;
        }

		$offset = 1;

		//if the trace came from Argument->test()
		if(isset($trace['class'], $trace['function'])
		&& $trace['class'] == __CLASS__
		&& $trace['function'] == 'test') {
			//go back one more
        	$offset = 2;
		}

		$trace = debug_backtrace();
		$trace = $trace[$offset];

		$types   	= func_get_args();
        $method  	= array_shift($types);
        $args  		= array_shift($types);
        $index   	= array_shift($types) - 1;

        if($index < 0) {
            $index = 0;
        }

        //if it's not set then it's good because the default value
        //set in the method will be it.
        if($index >= count($args)) {
            return $this;
        }

        $argument = $args[$index];

        foreach($types as $i => $type) {
            if($this->isValid($type, $argument)) {
                return $this;
            }
        }

        if(strpos($method, '->') === false && isset($trace['class'])) {
            $method = $trace['class'].'->'.$method;
        }

        $type = $this->getDataType($argument);

        Exception::i()->setMessage(self::INVALID_ARGUMENT)
            ->addVariable($index + 1)
            ->addVariable($method)
            ->addVariable(implode(' or ', $types))
            ->addVariable($type)
            ->setTypeLogic()
            ->setTraceOffset($offset)
            ->trigger();
    }

	/**
	 * Validates a credit card argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function isCreditCard($value)
    {
        return preg_match('/^(?:4[0-9]{12}(?:[0-9]{3})?|5[1-5][0-9]'.
        '{14}|6(?:011|5[0-9][0-9])[0-9]{12}|3[47][0-9]{13}|3(?:0[0-'.
        '5]|[68][0-9])[0-9]{11}|(?:2131|1800|35\d{3})\d{11})$/', $value);
    }

	/**
	 * Validates an email argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function isEmail($value)
    {
        return preg_match('/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]'.
        '\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]'.
        '\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62'.
        '}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|'.
        '[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})'.
        '(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]'.
        '+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25'.
        '[0-5])){3}\])$/', $value);
    }

	/**
	 * Validates a hex argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function isHex($value)
    {
        return preg_match("/^[0-9a-fA-F]{6}$/", $value);
    }

	/**
	 * Validates an HTML argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function isHtml($value)
    {
        return preg_match("/<\/?\w+((\s+(\w|\w[\w-]*\w)(\s*=\s*".
        "(?:\".*?\"|'.*?'|[^'\">\s]+))?)+\s*|\s*)\/?>/i", $value);
    }

	/**
	 * Validates a URL argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function isUrl($value)
    {
        return preg_match('/^(http|https|ftp):\/\/([A-Z0-9][A-Z0'.
        '-9_-]*(?:.[A-Z0-9][A-Z0-9_-]*)+):?(d+)?\/?/i', $value);
    }

	/**
	 * Validates an alpha numeric argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function alphaNum($value)
    {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }

	/**
	 * Validates an alpha numeric underscore argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function alphaNumScore($value) {
        return preg_match('/^[a-zA-Z0-9_]+$/', $value);
    }

	/**
	 * Validates an alpha numeric hyphen argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function alphaNumHyphen($value)
    {
        return preg_match('/^[a-zA-Z0-9-]+$/', $value);
    }

	/**
	 * Validates an alpha numeric hyphen underscore argument.
	 *
	 * @param *string
	 * @return bool
	 */
    protected function alphaNumLine($value)
    {
        return preg_match('/^[a-zA-Z0-9-_]+$/', $value);
    }

	/**
	 * Validates an argument given the type.
	 *
	 * @param *string
	 * @param *mixed
	 * @return bool
	 */
    protected function isValid($type, $data)
    {
        $type = $this->getTypeName($type);

        switch($type) {
            case 'number':
                return is_numeric($data);
            case 'int':
                return is_numeric($data) && strpos((string) $data, '.') === false;
            case 'float':
                return is_numeric($data) && strpos((string) $data, '.') !== false;
            case 'file':
                return is_string($data) && file_exists($data);
            case 'folder':
                return is_string($data) && is_dir($data);
            case 'email':
                return is_string($data) && $this->isEmail($data);
            case 'url':
                return is_string($data) && $this->isUrl($data);
            case 'html':
                return is_string($data) && $this->isHtml($data);
            case 'cc':
                return (is_string($data) || is_int($data)) && $this->isCreditCard($data);
            case 'hex':
                return is_string($data) && $this->isHex($data);
            case 'alphanum':
                return is_string($data) && $this->alphaNum($data);
            case 'alphanumscore':
                return is_string($data) && $this->alphaNumScore($data);
            case 'alphanumhyphen':
                return is_string($data) && $this->alphaNumHyphen($data);
            case 'alphanumline':
                return is_string($data) && $this->alphaNumLine($data);
            default: break;
        }

        $method = 'is_'.$type;
        if(function_exists($method)) {
            return $method($data);
        }

        if(class_exists($type)) {
            return $data instanceof $type;
        }

        return true;
    }

	/**
	 * Returns the data type of the argument
	 *
	 * @param *mixed
	 * @return string
	 */
    private function getDataType($data)
    {
        if(is_string($data)) {
            return "'".$data."'";
        }

        if(is_numeric($data)) {
            return $data;
        }

        if(is_array($data)) {
            return 'Array';
        }

        if(is_bool($data)) {
            return $data ? 'true' : 'false';
        }

        if(is_object($data)) {
            return get_class($data);
        }

        if(is_null($data)) {
            return 'null';
        }

        return 'unknown';
    }


	/**
	 * Returns the type name of the argument
	 *
	 * @param *mixed
	 * @return string|void
	 */
    private function getTypeName($data)
    {
        if(is_string($data)) {
            return $data;
        }

        if(is_numeric($data)) {
            return 'numeric';
        }

        if(is_array($data)) {
            return 'array';
        }

        if(is_bool($data)) {
            return 'bool';
        }

        if(is_object($data)) {
            return get_class($data);
        }

        if(is_null($data)) {
            return 'null';
        }
    }
}