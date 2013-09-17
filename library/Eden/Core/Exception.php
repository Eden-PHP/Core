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
class Exception extends \Exception
{
    //error type
    const ARGUMENT = 'ARGUMENT'; //used when argument is invalidated
    const LOGIC    = 'LOGIC';    //used when logic is invalidated
    const GENERAL  = 'GENERAL';  //used when anything in general is invalidated
    const CRITICAL = 'CRITICAL'; //used when anything caused application to crash

    //error level
    const WARNING     = 'WARNING';
    const ERROR       = 'ERROR';
    const DEBUG       = 'DEBUG';       //used for temporary developer output
    const INFORMATION = 'INFORMATION'; //used for permanent developer notes

    protected $reporter  = null;
    protected $type      = self::LOGIC;
    protected $level     = self::ERROR;
    protected $offset    = 1;
    protected $variables = array();
    protected $trace     = array();

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
    public static function i($message = null, $code = 0)
    {
        $class = get_called_class();
        return new $class($message, $code);
    }

    /**
     * Adds parameters used in the message
     *
	 * @param *scalar
     * @return Eden\Core\Exception
     */
    public function addVariable($variable)
    {
        $this->variables[] = $variable;
        return $this;
    }

    /**
     * Returns the exception level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Returns raw trace
     *
     * @return array
     */
    public function getRawTrace()
    {
        return $this->trace;
    }

    /**
     * REturns the class or method that caught this
     *
     * @return string
     */
    public function getReporter()
    {
        return $this->reporter;
    }

    /**
     * Returns the trace offset; where we should start the trace
     *
     * @return this
     */
    public function getTraceOffset()
    {
        return $this->offset;
    }

    /**
     * Returns the exception type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Returns variables
     *
     * @return array
     */
    public function getVariables()
    {
        return $this->variables;
    }

    /**
     * Sets exception level
     *
     * @param *string
     * @return Eden\Core\Exception
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Sets exception level to DEBUG
     *
     * @return Eden\Core\Exception
     */
    public function setLevelDebug()
    {
        return $this->setLevel(self::DEBUG);
    }

    /**
     * Sets exception level to ERROR
     *
     * @return Eden\Core\Exception
     */
    public function setLevelError()
    {
        return $this->setLevel(self::WARNING);
    }

    /**
     * Sets exception level to INFORMATION
     *
     * @return Eden\Core\Exception
     */
    public function setLevelInformation()
    {
        return $this->setLevel(self::INFORMATION);
    }

    /**
     * Sets exception level to WARNING
     *
     * @return Eden\Core\Exception
     */
    public function setLevelWarning()
    {
        return $this->setLevel(self::WARNING);
    }

    /**
     * Sets message
     *
     * @param *string
     * @return Eden\Core\Exception
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Sets what index the trace should start at
     *
     * @return Eden\Core\Exception
     */
    public function setTraceOffset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * Sets exception type
     *
     * @param *string
     * @return Eden\Core\Exception
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets exception type to ARGUMENT
     *
     * @return Eden\Core\Exception
     */
    public function setTypeArgument()
    {
        return $this->setType(self::ARGUMENT);
    }

    /**
     * Sets exception type to CRITICAL
     *
     * @return Eden\Core\Exception
     */
    public function setTypeCritical()
    {
        return $this->setType(self::CRITICAL);
    }

    /**
     * Sets exception type to GENERAL
     *
     * @return Eden\Core\Exception
     */
    public function setTypeGeneral()
    {
        return $this->setType(self::GENERAL);
    }

    /**
     * Sets exception type to LOGIC
     *
     * @return Eden\Core\Exception
     */
    public function setTypeLogic()
    {
        return $this->setType(self::CRITICAL);
    }

    /**
     * Combines parameters with message and throws it
     *
     * @return void
     */
    public function trigger()
    {
        $this->trace = debug_backtrace();

        $this->reporter = get_class($this);
        if(isset($this->trace[$this->offset]['class'])) {
            $this->reporter = $this->trace[$this->offset]['class'];
        }

        if(isset($this->trace[$this->offset]['file'])) {
            $this->file = $this->trace[$this->offset]['file'];
        }

        if(isset($this->trace[$this->offset]['line'])) {
            $this->line = $this->trace[$this->offset]['line'];
        }

        if(!empty($this->variables)) {
            $this->message = vsprintf($this->message, $this->variables);
            $this->variables = array();
        }

        throw $this;
    }
}