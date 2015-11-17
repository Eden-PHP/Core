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
     * This is used to call on the eden() function easier
     *
     * @package  Eden
     * @category Core
     * @author   Christian Blanquera <cblanquera@openovate.com>
     * @standard PSR-2
     */
    class Eden extends \Eden\Core\Control
    {
        /**
         * @const int DECOR shorthand for DECORATOR
         */
        const DECOR = 1;
    
        /**
         * @const int DECORATOR add this const to your code to enable eden()
         */
        const DECORATOR = 1;
        
        /**
         * Wrapper for eden()
         *
         * @return mixed
         */
        public static function call()
        {
            //just pass it along
            $args = func_get_args();
            return call_user_func_array('eden', $args);
        }
    }
}
