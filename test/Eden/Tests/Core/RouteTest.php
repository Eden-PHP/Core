<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

class Eden_Tests_Core_RouteTest extends \PHPUnit_Framework_TestCase
{   
	public function testGetClass()
    {
		$class = eden('core')->route()->getClass();
		$this->assertInstanceOf('Eden\\Core\\Route\\ClassRoute', $class);
		
		//instantiate class
		$class = eden('core')->route()->getClass('Eden\\Core\\Event');
		$this->assertInstanceOf('Eden\\Core\\Event', $class);
		
		//instantiate class with args
		$class = eden('core')->route()->getClass('Eden\\Core\\When', array($class));
		$this->assertInstanceOf('Eden\\Core\\When', $class);
    }
	
	public function testGetMethod()
    {
		$class = eden('core')->route()->getMethod();
		$this->assertInstanceOf('Eden\\Core\\Route\\MethodRoute', $class);
		
		//call method with arguments
		$class = eden('core')->route()->getMethod('Eden\\Core\\Exception', 'setType', array('test'));
		$this->assertInstanceOf('Eden\\Core\\Exception', $class);
		
		//call method with arguments
		$string = eden('core')->route()->getMethod('Eden\\Core\\Exception', 'getType');
		$this->assertEquals('LOGIC', $string);
    }
	
	public function testGetFunction()
    {
		$class = eden('core')->route()->getFunction();
		$this->assertInstanceOf('Eden\\Core\\Route\\FunctionRoute', $class);
		
		$string = eden('core')->route()->getFunction('str_replace', array('some', 'no', 'something'));
		$this->assertEquals('nothing', $string);
    }
}