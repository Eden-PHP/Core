<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

class Eden_Tests_Core_Route_ClassRouteTest extends \PHPUnit_Framework_TestCase
{
	public function testCall()
	{
		$class = eden('core')->route()->getClass()->call('Eden\\Core\\Exception', 'Something');
		$this->assertInstanceOf('Eden\\Core\\Exception', $class);
	}
	   
	public function testCallArray()
	{
		$class = eden('core')->route()->getClass()->callArray('Eden\\Core\\Exception', array('Something'));
		$this->assertInstanceOf('Eden\\Core\\Exception', $class);
	}
	
	public function testRoute()
	{
		//renaming a class example
		$class = eden('core')->route()->getClass()->route('Test', 'Eden\\Core\\Exception');
		$this->assertInstanceOf('Eden\\Core\\Route\\ClassRoute', $class);
		
		$string = eden()->Test()->getType();
		$this->assertEquals('LOGIC', $string);
		
		//shortcut example
		eden('core')->route()->getClass()->route('Event', 'Eden\\Core\\Event');
		$this->assertInstanceOf('Eden\\Core\\Event', eden()->Event());
	}
	
	public function testGetRoute()
	{
		$route = eden('core')->route()->getClass();
		$this->assertEquals('Eden\\Core\\Exception', $route->getRoute('test'));
	}
	
	public function testGetRoutes()
	{
		$route = eden('core')->route()->getClass();
		$this->assertArrayHasKey('test', $route->getRoutes());
	}
	
	public function testIsRoute()
	{
		$route = eden('core')->route()->getClass();
		$this->assertTrue($route->isRoute('test'));
		$this->assertFalse($route->isRoute('another'));
	}
	
	public function testRelease()
	{
		$route = eden('core')->route()->getClass()->release('test');
		$this->assertFalse($route->isRoute('test'));
	}
	
}