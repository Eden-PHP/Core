<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core\Exception;

use Eden\Core\Event;

/**
 * Error event hander
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class ErrorHandler extends Event
{
    const INSTANCE = 1;

    //error type
    const PHP = 'PHP';    //used when argument is invalidated
    const UNKNOWN = 'UNKNOWN';

    //error level
    const WARNING = 'WARNING';
    const ERROR = 'ERROR';

    /**
     * Called when a PHP error has occured. Must
     * use setErrorHandler() first.
     *
     * @param *number error number
     * @param *string message
     * @param *string file
     * @param *string line
     * @return true
     */
    public function handler($errno, $errstr, $errfile, $errline)
    {
        //depending on the error number
        //we can determine the error level
        switch ($errno) {
            case E_NOTICE:
            case E_USER_NOTICE:
            case E_WARNING:
            case E_USER_WARNING:
                $level = self::WARNING;
                break;
            case E_ERROR:
            case E_USER_ERROR:
            default:
                $level = self::ERROR;
                break;
        }

        //errors are only triggered through PHP
        $type = self::PHP;

        //get the trace
        $trace = debug_backtrace();

        //by default we do not know the class
        $class = self::UNKNOWN;

        //if there is a trace
        if(count($trace) > 1) {
            //formulate the class
            $class = $trace[1]['function'].'()';
            if(isset($trace[1]['class'])) {
                $class = $trace[1]['class'].'->'.$class;
            }
        }

        $this->trigger(
            'error',    $type,        $level,
            $class,     $errfile,     $errline,
            $errstr,    $trace,       1);

        //Don't execute PHP internal error handler
        return true;
    }

   /**
     * Returns default handler back to PHP
     *
     * @return Eden\Core\Exception\ErrorHandler
     */
    public function release()
    {
        restore_error_handler();
        return $this;
    }

    /**
     * Registers this class' error handler to PHP
     *
     * @return Eden\Core\Exception\ErrorHandler
     */
    public function register()
    {
        set_error_handler(array($this, 'handler'));
        return $this;
    }

    /**
     * Sets reporting
     *
     * @param *int
     * @return Eden\Core\Exception\ErrorHandler
     */
    public function setReporting($type)
    {
        error_reporting($type);
        return $this;
    }
}