<?php //-->
/**
 * This file is part of the Eden PHP Library.
 * (c) 2014-2016 Openovate Labs
 *
 * Copyright and license information can be found at LICENSE.txt
 * distributed with this package.
 */
class EdenCoreBaseTest extends PHPUnit_Framework_TestCase 
{
    public function test__Call() 
    {
        $class = eden()->Core_Event();
        $this->assertInstanceOf('Eden\\Core\\Event', $class);
        
        $name = 'Foo\Bar\Zoo';
        $class = eden()->$name();
        $this->assertInstanceOf('Foo\\Bar\\Zoo', $class);
        
        $class = eden()->Foo_Bar_Zoo();
        $this->assertInstanceOf('Foo\\Bar\\Zoo', $class);
        
        $class = eden('Foo\\Bar\\Zoo');
        $this->assertInstanceOf('Foo\\Bar\\Zoo', $class);
        
        $class = eden('foo_bar_zoo');
        $this->assertInstanceOf('Foo\\Bar\\Zoo', $class);
        
        $class = eden('Foo\\Bar');
        $this->assertInstanceOf('Foo\\Bar\\Index', $class);
        
        $class = eden('foo_bar');
        $this->assertInstanceOf('Foo\\Bar\\Index', $class);
        
        //Legacy
        $class = eden()->Bar_Foo_Zoo();
        $this->assertInstanceOf('Bar_Foo_Zoo', $class);
        
        $class = eden('bar_foo_zoo');
        $this->assertInstanceOf('Bar_Foo_Zoo', $class);
        
        $class = eden('bar_foo');
        $this->assertInstanceOf('Bar_Foo_Index', $class);
        
        //Mix
        $name = 'Foo\Bar\Foo_Zoo';
        $class = eden()->$name();
        $this->assertInstanceOf('Foo\\Bar\\Foo_Zoo', $class);
        
        $class = eden('Foo\\Bar\\Foo_Zoo');
        $this->assertInstanceOf('Foo\\Bar\\Foo_Zoo', $class);
    }
    
    public function test__Invoke() 
    {
        //eden('core')->event();        means    Eden\Core\Factory         -> Eden\Core\Event
        //eden('core')->loader();        means    Eden\Core\Factory         -> Eden\Core\Loader
        //eden('core')->route();         means     Eden\Core\Factory         -> Eden\Core\Route
        //eden('facebook')->graph();    means    Eden\Facebook\Factory     -> Eden\Facebook\Graph
        //eden('utility')->session();    means    Eden\Utility\Factory     -> Eden\Utility\Session
        $class = eden('core');
        $this->assertInstanceOf('Eden\\Core\\Index', $class);
    }
    
    public function test__ToString() 
    {
        $this->assertEquals('Eden\\Core\\Control', (string) eden());
    }
    
    public function testAddMethod() 
    {
        $self = $this;
        eden()->addMethod('foo', function() use ($self) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            
            return self::INSTANCE;
        });
        
        $this->assertEquals(1, eden()->foo());
        
        eden('core')->event()->addMethod('E');
        $this->assertInstanceOf('Eden\\Core\\Event', eden()->E());
    }
    
    public function testCallArray() 
    {
        $class = eden()->callArray('trigger', array('test'));
        $this->assertInstanceOf('Eden\\Core\\Control', $class);
    }
    
    public function testLoop() 
    {
        $self = $this;
        eden()->loop(function($i) use ($self) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            
            if ($i == 2) {
                return false;
            }
        });
    }
    
    public function testInspect() 
    {
        ob_start();
        eden('core')->event()->inspect('Something');
        $contents = ob_get_contents();
        ob_end_clean();
        $this->assertEquals('<pre>INSPECTING Variable:</pre><pre>Something</pre>', $contents);
    }
    
    public function testOn() 
    {
        $self = $this;
        
        $class = eden()->on('some-event', function($foo) use ($self) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            $self->assertEquals('bar', $foo);
        });
        
        $this->assertInstanceOf('Eden\\Core\\Control', $class);
    }

    public function testTrigger() 
    {
        $class = eden()->trigger('some-event', 'bar');
        $this->assertInstanceOf('Eden\\Core\\Control', $class);
    }

    public function testOff()
    {
        $class = eden()->off('some-event');
        $this->assertInstanceOf('Eden\\Core\\Control', $class);
        eden()->trigger('some-event', 'bar');
    }
    
    public function testWhen() 
    {
        $self = $this;
        $test = 'Good';
        eden()->when(function() use ($self) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            return false;
        }, function() use ($self, &$test) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            $test = 'Bad';
        });
        
        $this->assertSame('Good', $test);
        
        $test = 'Good';
        eden()->when(null, function() use (&$test) {
            $test = 'Bad';
        });
        
        $this->assertSame('Good', $test);
        
        $test = 'Good';
        eden()->when(false, function() use (&$test) {
            $test = 'Bad';
        });
        
        $this->assertSame('Good', $test);
        
        
        $test = 'Good';
        eden()->when(function() {
            return true;
        }, function() use (&$test) {
            $test = 'Bad';
        });
        
        $this->assertSame('Bad', $test);
        
        $test = 'Good';
        eden()->when('hi', function() use (&$test) {
            $test = 'Bad';
        });
        
        $this->assertSame('Bad', $test);
        
        $test = 'Good';
        eden()->when(true, function() use (&$test) {
            $test = 'Bad';
        });
        
        $this->assertSame('Bad', $test);
        
        $test = 'Not Sure';
        eden()->when(function() use ($self) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            return false;
        }, function() use ($self, &$test) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            $test = 'Good';
        }, function() use ($self, &$test) {
            $self->assertInstanceOf('Eden\\Core\\Control', $this);
            $test = 'Bad';
        });
        
        $this->assertSame('Bad', $test);
        
        eden()
            ->Foo_Bar_Zoo()
            ->when(true, function() use ($self) {
                $self->assertEquals(4, $this->foobar);
            });
    }
}
