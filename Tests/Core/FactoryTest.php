<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Core_Tests_Core_FactoryTest extends \PHPUnit_Framework_TestCase
{
	
	public function testArgument()
    {
        $class = eden('core')->argument();
		$this->assertInstanceOf('Eden\\Core\\Argument', $class);
    }
	
    public function testEvent()
    {
        $class = eden('core')->event();
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
    }
	
    public function testLoader()
    {
        $class = eden('core')->loader();
		$this->assertInstanceOf('Eden\\Core\\Loader', $class);
    }
	
    public function testRoute()
    {
        $class = eden('core')->route();
		$this->assertInstanceOf('Eden\\Core\\Route', $class);
    }
}