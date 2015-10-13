<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace Eden\Core;

/**
 * The base class for any class handling exceptions. Exceptions
 * allow an application to custom handle errors that would
 * normally let the system handle. This exception allows you to
 * specify error _levels and error _types. Also using this exception
 * outputs a _trace (can be turned off) that shows where the problem
 * started to where the program stopped.
 *
 * @package  Eden
 * @category Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Exception extends \Exception
{
    /**
     * @const string ARGUMENT Used when argument is invalidated
     */
    const ARGUMENT = 'ARGUMENT';

    /**
     * @const string LOGIC Used when logic is invalidated
     */
    const LOGIC = 'LOGIC';

    /**
     * @const string GENERAL Used when anything in general is invalidated
     */
    const GENERAL = 'GENERAL';

    /**
     * @const string CRITICAL Used when anything caused application to crash
     */
    const CRITICAL = 'CRITICAL';

    /**
     * @const string WARNING Used to inform developer without crashing
     */
    const WARNING = 'WARNING';

    /**
     * @const string ERROR Used when code was thrown
     */
    const ERROR = 'ERROR';

    /**
     * @const string DEBUG Used for temporary developer output
     */
    const DEBUG = 'DEBUG';

    /**
     * @const string INFORMATION Used for permanent developer notes
     */
    const INFORMATION = 'INFORMATION';
    
    /**
     * @var string|null $reporter class name that it came from
     */
    protected $reporter  = null;
    
    /**
     * @var string $type exception type
     */
    protected $type = self::LOGIC;
    
    /**
     * @var string $level level of exception
     */
    protected $level = self::ERROR;
    
    /**
     * @var int $offset used for false positives on the trace
     */
    protected $offset = 1;
    
    /**
     * @var array $variables used for sprintf messages
     */
    protected $variables = array();
    
    /**
     * @var array $trace the back trace
     */
    protected $trace = array();
    
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
     * @param string|null $message the exception message
     * @param string|null $code    exception code number
     *
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
     * @param *scalar $variable used for sprintf
     *
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
     * Returns the class or method that caught this
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
     * @return Eden\Core\Exception
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
     * @param *string $level the gravity of the exception
     *
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
     * @param *string $message the Exception message
     *
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
     * @param int $offset for correcting false positives in the trace
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
     * @param *string $type custom error type
     *
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
     */
    public function trigger()
    {
        $this->trace = debug_backtrace();

        $this->reporter = get_class($this);
        if (isset($this->trace[$this->offset]['class'])) {
            $this->reporter = $this->trace[$this->offset]['class'];
        }

        if (isset($this->trace[$this->offset]['file'])) {
            $this->file = $this->trace[$this->offset]['file'];
        }

        if (isset($this->trace[$this->offset]['line'])) {
            $this->line = $this->trace[$this->offset]['line'];
        }

        if (!empty($this->variables)) {
            $this->message = vsprintf($this->message, $this->variables);
            $this->variables = array();
        }

        throw $this;
    }
}
