<?php //-->
/*
 * This file is part of the Eden package.
 * (c) 2009-2011 Christian Blanquera <cblanquera@gmail.com>
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
        $class = Eden\Core\Controller::i();
        if(func_num_args() == 0) {
            return $class;
        }

        $args = func_get_args();
        return $class->__invoke($args);
    }
}

namespace Eden\Core
{
    /**
     * Defines the starting point of every framework call.
     * Starts laying out how classes and methods are handled.
     *
     * @vendor Eden
     * @package Core
     * @author Christian Blanquera cblanquera@openovate.com
     */
    class Controller extends Base
    {
        const INSTANCE = 1;

        protected static $active = NULL;

        /**
         * Sets active application
         *
         * @return void
         */
        public function __construct()
        {
            if(!self::$active) {
                self::$active = $this;
            }
        }

        /**
         * Get Active Application
         *
         * @return Eden
         */
        public function getActiveApp()
        {
            return self::$active;
        }

        /**
         * Sets the PHP timezone
         *
         * @return this
         */
        public function setTimezone($zone)
        {
            Argument::i()->test(1, 'string');

            date_default_timezone_set($zone);

            return $this;
        }
    }
}