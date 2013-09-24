<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Core_Tests_Core_ControllerTest extends \PHPUnit_Framework_TestCase
{    
	public function testGetActiveApp()
	{
        $class = eden()->getActiveApp();
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
	}

	public function testSetTimezone()
	{
        $class = eden()->setTimezone('GMT');
		$this->assertInstanceOf('Eden\\Core\\Controller', $class);
	}
}