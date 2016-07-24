![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Core
====
[![Build Status](https://api.travis-ci.org/Eden-PHP/Core.png)](https://travis-ci.org/Eden-PHP/Core) [![Latest Stable Version](https://poser.pugx.org/eden/core/v/stable)](https://packagist.org/packages/eden/core) [![Total Downloads](https://poser.pugx.org/eden/core/downloads)](https://packagist.org/packages/eden/core) [![Latest Unstable Version](https://poser.pugx.org/eden/core/v/unstable)](https://packagist.org/packages/eden/core) [![License](https://poser.pugx.org/eden/core/license)](https://packagist.org/packages/eden/core)
====

- [Install](#install)
- [Introduction](#intro)
- [API](#api)
    - [Add Method](#add-method)
    - [Call Array](#call-array)
    - [Inspect](#inspect)
    - [Load State](#load-state)
    - [Loop](#loop)
    - [Off](#off)
    - [On](#on)
    - [Save State](#save-state)
    - [Trigger](#trigger)
    - [When](#when)
- [Contributing](#contributing)

====

<a name="install"></a>
## Install

`composer install eden/core`

====

## Enable Eden

The following documentation uses `eden()` in its example reference. Enabling this function requires an extra step as descirbed in this section which is not required if you access this package using the following.

```
Eden\Core\Control::i();
```

When using composer, there is not an easy way to access functions from packages. As a workaround, adding this constant in your code will allow `eden()` to be available after. 

```
Eden::DECORATOR;
```

For example:

```
Eden::DECORATOR;

eden()->inspect('Hello World');
```

====

<a name="intro"></a>
## Introduction

Some of the greatest features, as well as supporting web services out of the box, 
lies in Eden's core. The core's purpose is to give the end developer an easier 
experience in OOP and design patterns while tackling some of the most complicated 
problems that exist in web development today.

### Instantiating your PHP Classes

Eden has a mechanism for easily instantiating your classes ready to be used in a chainable manner.

```
eden('YOUR_CLASS_NAME');
eden('YOUR\\CLASS\\NAME');
eden('YOUR\\CLASS_NAME');
```

You can pass arguments to you constructor in the following manner, where 
`123, array('foo' => 'bar'), 'abc'` is what you would like to pass on to 
your `__construct`.

```
eden('YOUR\\CLASS\\NAME', 123, array('foo' => 'bar'), 'abc');
```

Optional instantiation patterns can be expressed like this. 

```
eden()->Your_Class_Name(123, array('foo' => 'bar'), 'abc'));
```

Notice how `_` are replaced with `\\` automatically. This also works for classes not using namespaces, 
however the above pattern does not work when instantiating a mix of `_` and `\\` in the same class name.

### Extending your PHP Classes

If you would like to utilize Eden's core features you may do so in the following manner.

```
class Foo extends \Eden\Core\Base {}
```

Doing so will allow you to access features like `when` and `loop` within your class in 
the following manner.

```
class Foo extends \Eden\Core\Base 
{
    protected $zoo = 4;
    
    public function bar()
    {
        $this
            ->loop(function($i) {
                if($i === 5) {
                    return false;
                }
                
                $this->zoo += $i;
            })
            ->when($this->zoo > 10, function() {
                echo $this->zoo;
            });
    }
}
```

Eden uses a special pattern available in PHP 5.4 that allows class properties and methods 
to be accessed within the loop and when callback. 

```
NOTE: You cannot access private methods or properties inside of a callback method
```

You can also instantiate other classes in your class in the following manner 
instead of using the `eden()` function.

```
class Foo extends \Eden\Core\Base 
{
    public function bar()
    {
        $this('bar');
    }
}
```

====

<a name="api"></a>
## API

Advanced examples can be found in the `test` folder. It's recommended to review that folder as well.

====
<a name="add-method"></a>
### Add Method

Add a virutal method to an existing instantiation.

#### Usage

```
eden()->addMethod(string $name, callable $callback);
```

#### Example

```
eden()->addMethod('output', function($string) {
    echo $string;
    return $this;
});

eden()->inspect('Hello World');
```

#### Parameters

`string $name` - Required - The name of the method.

`callable $callback` - Required - The method definition. If an array callable is passed, 
the scope inside that method will be as defined in the callable array. Otherwise, the current 
instance scope will be used.

====
<a name="call-array"></a>
### Call Array

A chainable version of call_user_func_array(). Used for calling current methods vertically.

#### Usage

```
eden()->callArray(string $method[, array $args]);
```

#### Example

```
eden('core_event')->callArray('inspect', array('observers'));
```

#### Parameters

`string $method` - Required - The name of the method.

`array $args` - Optional - The argument array that will be passed to the specified method horizontally.

====
<a name="inspect"></a>
### Inspect

For debug purposes, using this will output the raw values specified.

#### Usage

```
eden()->inspect([mixed $property = $this]);
```

#### Example

```
eden('core_event')->inspect('observers');
```

#### Parameters

`mixed $property` - Optional - if a string is provided and is really the name of a property in your 
class the raw value will be outputted. If no value is provided, this method will output all the 
properties in the current class.

====
<a name="load-state"></a>
### Load State

Used in conjuction with `eden()->saveState()`, this method will 
recall a saved instance previously saved for further processing.

#### Usage

```
eden()->loadState(string $name);
```

#### Example

```
eden()->loadState('foobar');
```

#### Parameters

`string $name` - Required - The name of the saved state.

====
<a name="loop"></a>
### Loop

A chainable for statement. It is possible to have an infinite loop with this method so test wisely.

#### Usage

```
eden()->loop(callable $callback, int $incrementor = 0);
```

#### Example

```
eden()->loop(
    function($i) {
        if($i < 4) {
            return;
        }
        
        return false;
    }, 2);
```

#### Parameters

`callable $callback` - Required - The loop callback.

`int $incrementor` - Optional - Default 0 - Used as an optional nice to have that auto 
increments by 1 after each time the callback above is called.

====
<a name="off"></a>
### Off

Used in conjunction with `eden()->on()` and `eden()->trigger()`, this removes event 
listeners on a particular event. Events are stored in on a global scale.

#### Usage

```
eden()->off([string $event, callable $handler]);
```

#### Example

```
eden()->off('complete');
```

#### Parameters

`string $event` - Optional - if a string is provided this will remove all events that match this string.
if no event is provided, all event handlers will be removed.

`callable $handler` - Optional - Adding this will remove a particular handler from the specified event.

====
<a name="on"></a>
### On

Used in conjunction with `eden()->off()` and `eden()->trigger()`, this adds an event 
listener given the particular event. Events are stored in on a global scale.

#### Usage

```
eden()->on(string $event, callable $handler);
```

#### Example

```
eden()->on('complete', function($string, $number) {
    echo $string . ' ' . $number;
});
```

#### Parameters

`string $event` - Required - The name of the event. This 
can be any name you want so long that it's a valid string.

`callable $handler` - Required - The handler that will be 
called if the event is triggered.

====
<a name="save-state"></a>
### Save State

Used in conjuction with `eden()->loadState()`, this method will 
save the current instance for further recall later.

#### Usage

```
eden()->saveState(string $name);
```

#### Example

```
eden()->saveState('foobar');
```

#### Parameters

`string $name` - Required - The name of the saved state. This can be any name you 
like so long as it's a valid string.

====
<a name="trigger"></a>
### Trigger

Used in conjunction with `eden()->off()` and `eden()->on()`, this triggers the event passing
any arguments provided.

#### Usage

```
eden()->trigger(string $event[, mixed $arg..]);
```

#### Example

```
eden()->trigger('complete', 'Foo', 123);
```

#### Parameters

`string $event` - Required - The name of the event. This 
can be any name you want so long that it's a valid string.

`mixed $arg` - Optional - any arguments you want passed to 
all handlers that are listening to the specified event.

====
<a name="when"></a>
### When

A chainable if/else statement.

#### Usage

```
eden()->when(bool|callable $condition, callable $success[, callable $fail]);
```

#### Example

```
eden()->when(
    function() {
        return true;
    }, 
    function() {
        //this is called if true
    }, 
    function() {
        //this is called if false
    });
```

#### Parameters

`bool|callable $condition` - Required - The conditional test

`callable $success` - Required - This will be called if the conditional test evaluates to true.

`callable $fail` - Optional - This will be called if the conditional test does not evaluates to true.

====

### This is not a God Object

![God Object](http://blog.schauderhaft.de/wp-content/uploads/2013/03/godObject-160x300.png)

#####How many operation do you need on a simple iterator?

The question can be answered easily by looking at any Iterator API in a 
given language. You need 3 methods:

1. Get the current value
2. Move the iterator to the next element
3. Check if the Iterator has more elements

That's all you need. If you can perform those 3 operations, you can go 
through any sequence of elements.

But that is not only what you usually want to do with a sequence of elements, 
is it? You usually have a much higher level goal to achieve. You may want to 
do something with every element, you may want to filter them according to some 
condition, or one of several other methods. See the IEnumerable interface in the 
LINQ library in .NET for more examples.

Do you see how many there are? And that is just a subset of all the methods they 
could have put on the IEnumerable interface, because you usually combine them to 
achieve even higher goals.

But here is the twist. Those methods are not on the IEnumerable interface. They 
are simple utility methods that actually take a IEnumerable as input and do 
something with it. So while in the C# language it feels like there are a 
bajillion methods on the IEnumerable interface, IEnumerable is not a god 
object.

##### Is jQuery a God Class? 

Lets ask that question again, this time with class processes.

How many operation do you need on a DOM element?

Again the answer is pretty straightforward. All the methods you need are methods 
to read/modify the attributes and the child elements. That's about it. Everything 
else is only a combination of those basic operations.

But how much higher level stuff would you want to do with a DOM elements? Well, 
same as an Iterator: a bajillion different things. And that's where jQuery comes in. 
jQuery, in essence provide two things:

A very nice collections of utilities methods that you may want to call on a DOM element, and;
Syntactic sugar so that using it is a much better experience than using the standard DOM API.
If you take out the sugared form, you realise that jQuery could easily have been written 
as a bunch of functions that select/modify DOM elements. For example:

    $("#body").html("<p>hello</p>");

...could have been written as:

    html($("#body"), "<p>hello</p>");

Semantically it's the exact same thing. However the first form has the big advantage that the 
order left-to-right of the statements follow the order the operations will be executed. The second 
start in the middle, which makes for very hard to read code if you combine lots of operations 
together.

So what does it all mean? That jQuery (like LINQ) is not the God object anti-pattern. It's instead 
a case of a very respected pattern called the Decorator.

...

From (http://programmers.stackexchange.com/questions/179601/is-jquery-an-example-of-god-object-antipattern)

##### Back to Eden

Much like jQuery, Eden follows a similar decorator pattern. You can use classes in the Eden library 
in the normal way of `new Eden\Core\Event` or `new Eden\Mysql\Factory`. Or use it this way `eden('mysql')`

====

<a name="contributing"></a>
#Contributing to Eden

Contributions to *Eden* are following the Github work flow. Please read up before contributing.

##Setting up your machine with the Eden repository and your fork

1. Fork the repository
2. Fire up your local terminal create a new branch from the `v4` branch of your 
fork with a branch name describing what your changes are. 
 Possible branch name types:
    - bugfix
    - feature
    - improvement
3. Make your changes. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")

##Making pull requests

1. Please ensure to run `phpunit` before making a pull request.
2. Push your code to your remote forked version.
3. Go back to your forked version on GitHub and submit a pull request.
4. An Eden developer will review your code and merge it in when it has been classified as suitable.
