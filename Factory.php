<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

namespace Eden\Core;

use Eden\Core\Exception\ErrorHandler;
use Eden\Core\Exception\ExceptionHandler;

/**
 * Core Factory Class
 *
 * @vendor Eden
 * @package Core
 * @author Christian Blanquera cblanquera@openovate.com
 */
class Factory extends Base
{
    const INSTANCE = 1;

    /**
     * Returns the argument validation class
     *
     * @return Eden\Core\Argument
     */
    public function argument()
    {
        return Argument::i();
    }

    /**
     * Returns the class router class
     *
     * @return Eden\Core\Route
     */
    public function error()
    {
        return ErrorHandler::i();
    }

    /**
     * Returns the event handler class
     *
     * @return Eden\Core\Event
     */
    public function event()
    {
        return Event::i();
    }

    /**
     * Returns the class router class
     *
     * @return Eden\Core\Route
     */
    public function exception()
    {
        return ExceptionHandler::i();
    }

    /**
     * Returns the class loader class
     *
     * @return Eden\Core\Event
     */
    public function loader()
    {
        return Loader::i();
    }

    /**
     * Returns the class router class
     *
     * @return Eden\Core\Route
     */
    public function route()
    {
        return Route::i();
    }
}