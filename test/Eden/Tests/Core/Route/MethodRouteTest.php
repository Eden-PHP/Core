<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */

class Eden_Tests_Core_Route_MethodRouteTest extends \PHPUnit_Framework_TestCase
{  
	public function testCallStatic()
	{
		$class = eden('core')->route()->getMethod()->callStatic('Eden\\Core\\Exception', 'i', array('Something'));
		$this->assertInstanceOf('Eden\\Core\\Exception', $class);
	}
	
	public function testCall()
	{	
		$string = eden('core')->route()->getMethod()->call(Eden\Core\Exception::i(), 'getType');
		$this->assertEquals('LOGIC', $string);
	}
	
	public function testRoute()
	{
		$route = eden('core')->route()->getMethod()
		->route('Eden\\Core\\Event', 'getType2', 'Eden\\Core\\Exception', 'getType');
		$this->assertInstanceOf('Eden\\Core\\Route\\MethodRoute', $route);
		
		$string = eden('core')->event()->getType2();
		$this->assertEquals('LOGIC', $string);
	}
	
	public function testGetRoute()
	{
		$route = eden('core')->route()->getMethod();
		$this->assertContains('getType', $route->getRoute('Eden\\Core\\Event', 'getType2'));
	}
	
	public function testGetRoutes()
	{
		$route = eden('core')->route()->getMethod();
		$this->assertArrayHasKey('eden\\core\\event', $route->getRoutes());
	}
	
	public function testIsRoute()
	{
		$route = eden('core')->route()->getMethod();
		$this->assertTrue($route->isRoute('Eden\\Core\\Event', 'getType2'));
		$this->assertFalse($route->isRoute('Eden\\Core\\Event', 'getType3'));
	}
	
	public function testRelease()
	{
		$route = eden('core')->route()->getMethod()->release('Eden\\Core\\Event', 'getType2');
		$this->assertFalse($route->isRoute('Eden\\Core\\Event', 'getType2'));
	}
	
}