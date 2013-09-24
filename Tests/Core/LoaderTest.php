<?php //-->
/*
 * This file is part of the Core package of the Eden PHP Library.
 * (c) 2013-2014 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE
 * distributed with this package.
 */
 
class Eden_Core_Tests_Core_LoaderTest extends \PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
		$loader = Eden\Core\Loader::i()
			->load('Eden\\Core\\Route')
			->load('Eden_Core_Route'); 
			
		$this->assertTrue($loader->handler('Eden\\Core\\Route'));
		$this->assertTrue($loader->handler('Eden_Core_Route'));
		$this->assertFalse($loader->handler('Eden_Core_Something'));
    }
}