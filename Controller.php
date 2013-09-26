<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
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
    class Controller extends Event
    {
        const INSTANCE = 1;

        protected static $active = null;

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
		 * @param *string
         * @return Eden\Core\Controller
         */
        public function setTimezone($zone = 'GMT')
        {
            Argument::i()->test(1, 'string');

            date_default_timezone_set($zone);

            return $this;
        }
	
		/**
		 * Starts a session
		 *
		 * @return Control
		 */
		public function startSession() 
		{
			session_start();
			
			return $this;
		}
    }
}