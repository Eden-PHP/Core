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
 * Core Factory Class
 *
 * @package  Eden
 * @category Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */
class Index extends Base
{
    /**
     * @const int INSTANCE Flag that designates singleton when using ::i()
     */
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
     * Returns the event class
     *
     * @return Eden\Core\Event
     */
    public function event()
    {
        return Event::i();
    }

    /**
     * Returns the router class
     *
     * @return Eden\Core\Route
     */
    public function route()
    {
        return Route::i();
    }
}
