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
use Eden\Core\Exception;

/**
 * Exception event handler
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class ExceptionHandler extends Event
{
    const INSTANCE = 1;

   /**
     * Called when a PHP exception has occured. Must
     * use setExceptionHandler() first.
     *
     * @param *Exception
     * @return void
     */
    public function handler(\Exception $e)
    {
        //by default set LOGIC ERROR
        $type = Exception::LOGIC;
        $level = Exception::ERROR;
        $offset = 1;
        $reporter = get_class($e);

        $trace = $e->getTrace();
        $message = $e->getMessage();

        //if the exception is an eden exception
        if($e instanceof EdenError) {
            //set type and level from that
            $trace = $e->getRawTrace();

            $type = $e->getType();
            $level = $e->getLevel();
            $offset = $e->getTraceOffset();
            $reporter = $e->getReporter();
        }

        $this->trigger(
            'exception', $type,             $level,
            $reporter,   $e->getFile(),     $e->getLine(),
            $message,    $trace,            $offset);
    }

    /**
     * Returns default handler back to PHP
     *
     * @return Eden\Core\Exception\ExceptionHandler
     */
    public function release()
    {
        restore_exception_handler();
        return $this;
    }

    /**
     * Registers this class' error handler to PHP
     *
     * @return Eden\Core\Exception\ExceptionHandler
     */
    public function register()
    {
        set_exception_handler(array($this, 'handler'));
        return $this;
    }
}