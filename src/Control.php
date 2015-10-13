<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */

namespace

{
    /**
     * The starting point of every framework call.
     *
     * @author Christian Blanquera cblanquera@openovate.com
     */
    function eden()
    {
        $class = Eden\Core\Control::i();
        if (func_num_args() == 0) {
            return $class;
        }
    
        $args = func_get_args();
        return $class->__invoke($args);
    }
}

/**
 * Defines the starting point of every framework call.
 * Starts laying out how classes and methods are handled.
 *
 * @package  Eden
 * @category Core
 * @author   Christian Blanquera <cblanquera@openovate.com>
 * @standard PSR-2
 */

namespace Eden\Core

{
    class Control extends Base
    {
        /**
         * @const int INSTANCE Flag that designates singleton when using ::i()
         */
        const INSTANCE = 1;
    }
}
