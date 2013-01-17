<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

class Eden_Tests_Core_Route_FunctionRouteTest extends \PHPUnit_Framework_TestCase
{
	public function testCall()
	{
		$string = eden('core')->route()->getFunction()->call('str_replace', 'some', 'no', 'something');
		$this->assertEquals('nothing', $string);
	}
	   
	public function testCallArray()
	{
		$string = eden('core')->route()->getFunction()->callArray('str_replace', array('some', 'no', 'something'));
		$this->assertEquals('nothing', $string);
	}
	
	public function testRoute()
	{
		$route = eden('core')->route()->getFunction()->route('string_replace', 'str_replace');
		$this->assertInstanceOf('Eden\\Core\\Route\\FunctionRoute', $route);
		
		$string = eden('core')->route()->getFunction()->call('string_replace', 'some', 'no', 'something');
		$this->assertEquals('nothing', $string);
	}
	
	public function testGetRoute()
	{
		$route = eden('core')->route()->getFunction();
		$this->assertEquals('str_replace', $route->getRoute('string_replace'));
	}
	
	public function testGetRoutes()
	{
		$route = eden('core')->route()->getFunction();
		$this->assertArrayHasKey('string_replace', $route->getRoutes());
	}
	
	public function testIsRoute()
	{
		$route = eden('core')->route()->getFunction();
		$this->assertTrue($route->isRoute('string_replace'));
		$this->assertFalse($route->isRoute('another'));
	}
	
	public function testRelease()
	{
		$route = eden('core')->route()->getClass()->release('string_replace');
		$this->assertFalse($route->isRoute('string_replace'));
	}
	
}