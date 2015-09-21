<?php //-->
/*
 * This file is part of the Eden package.
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
		if(func_num_args() == 0) {
			return $class;
		}
	
		$args = func_get_args();
		return $class->__invoke($args);
	}
}

namespace Eden\Core {
	/**
	 * Defines the starting point of every framework call.
	 * Starts laying out how classes and methods are handled.
	 *
	 * @package    Eden
	 * @category   core
	 * @author     Christian Blanquera cblanquera@openovate.com
	 */
	class Control extends Base 
	{
		const INSTANCE = 1;
	}
}