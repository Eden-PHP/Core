![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Core
====

- [Introduction](#intro)
- [Chainability](#chain)
- [Inspector](#inspect)
- [Conditional](#when)
- [Looping](#loop)
- [States](#state)
- [Routing](#route)
- [Classes](#class)
- [Event Driven](#event)
- [Contributing](#contributing)

====
<a name="intro"></a>
## Introduction

Some of the greatest features, as well as supporting web services out of the box, 
lies in Eden's core. The core's purpose is to give the end developer an easier 
experience in OOP and design patterns while tackling some of the most complicated 
problems that exist in web development today.

### Extending your PHP

Setting your classes as a child of `Eden\Core\Base` empowers your classes to do more. When accepting `Eden\Core\Base` as a parent you can now instantiate your classes in this manner.

	eden()->YOUR_CLASS_NAME();

Now you can harness everything eden has to offer:

    eden()->YOUR_CLASS_NAME()
    
    // Loop till false is returned
    ->loop(function($i, $instance) { 
        //exit loop
        return false; 
    })
    
    // Conditionals
    ->when(function($instance) {
        return true;
    }, function($instance) {
        //do something
    })
    
    // Property Inspector
    ->inspect('AnyProperty')
    
    // Event Handling
    ->listen('some-event', function($event, $instance) {
        //do something
    })
    ->trigger('some-event')
    
    // Aliasing Classes
    ->route('AnotherClass')
    ->AnotherClass()
    
    // Setting and retrieving states
    ->setState('populated')
    ->loadState('populated')
    
    // Jumping to another class
    ->Eden_Core_Inspect()
    ->output('Hello World');

====

### Not a God Object

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
<a name="chain"></a>
## Chainability

One of *Eden's* methodologies is "when in doubt return this". Returning the same object allows method calls to be expressed in a chainable format.

The next example demonstrates passing initial arguments in a class and printing the results after various method calls.

**Figure 1. Basic Chainability**

	echo eden('type', 'Hello World')->str_replace(' ','-')->strtolower()->substr(0, 8); //--> hello-wo
	 
	/* vs */
 	
	echo substr(strtolower(str_replace(' ', '-', 'Hello World')), 0, 8); //--> hello-wo

`Figure 1` above shows that we passed *Hello World* into `Eden\Type\StringType` then replace spaces with a dash, lower casing and show only the first eight characters, again in one line. We can do the same with regular PHP however when another developer has to read that, they have to read it inner to outer versus left to right.

For both sets of code in `Figure 1` it's bad practice to put so much code on the same line. Our next example shows the same code as `Figure 1` except in a more vertical fashion.

**Figure 2. Vertical**

	echo eden('type', 'Hello World')
    	->str_replace(' ','-')
    	->strtolower()
    	->substr(0, 8); //--> hello-wo
	
	/* vs */
	
	$string = 'Hello World';
	$string = str_replace(' ', '-', $string);
	$string = strtolower($string);
	$string = substr($string, 0, 8);
	echo $string; //--> hello-wo

You probably noticed when using *Eden*, we didn't have to create a variable. This is the same case when dealing with multiple classes. You can instantiate classes all in the same chain.

====
<a name="inspect"></a>
## Inspecting

A useful tool when troubleshooting code is Eden's handy inspector feature. At face value you can inspect any kind of variable which would output the value in a readable format

**Figure 2. Output any variable**

	$hello = 'world';
	eden()->inspect($hello);

Executing `Figure 2` would output `world`, which is semi-impressive. Arrays and objects as in `Figure 3` would show nice formatting when used.

**Figure 3. Output array**

	$hello = array(4, 3, 2, 1);
	eden()->inspect($hello);

would show

	Array
	(
		[0] => 4,
		[1] => 3,
		[2] => 2,
		[3] => 1
	)

More than just outputting random variables, The inspector can expose data whether private or protected when used within classes.

**Figure 4. Inspecting inside the class**

	class MyClass extends Eden\Core\Base {
		protected $id = 4;
		
		public function getId() {
			$this->inspect('id');
			
			return $this->id;
		}
	}

For classes like the one in `Figure 4`, you can inspect the next upcoming results like `Figure 5`.

**Figure 5. Inspect getId()**

	eden()->MyClass()->inspect(true)->getId();

Using `Figure 5` would output the results from `getId()`, no need to echo.

====
<a name="when"></a>
## Conditional

More with chainability is *Eden's* conditional method called *when*. A basic usage of when can be found in `Figure 6`.

**Figure 6. Basic Usage**

	$isTrue = !!rand();
	
    eden()->when($isTrue, function($instance) {
        //do something
    });

The first argument is a conditional that should be set to either `true` or `false`. The second argument in `Figure 6` will be called if argument 1 is `true`. As we can see in the callback *Eden* automatically passes the instance to the callback. We can express the first argument in `Figure 6` also as a condition callback like in `Figure 7`.

**Figure 7. Conditional Callback**

    eden()->when(function($instance) {
		return true;
	}, function($instance) {
        //do something
    });

When argument 1 is a callable function, the conditional callback should return either `true` or `false`. *Eden* automatically passes the instance to the conditional callback as well. A more prime use case for `when` could be a message class.

**Figure 8. Message Handler**

	class Message extends Eden\Core\Base {
		protected $type = null;
		protected $message = null;
		
		public function __construct($message, $type) {
			$this->message = $message;
			$this->type = $type;
		}
		
		public function getType() {
			return $this->type;
		}
		
		public function getMessage() {
			return $this->message;
		}
	}
	
	eden()->Message('error', 'There was an error')
		->when(function($instance) {
			return $instance->getType() == 'error';
		}, function($instance) {
			echo '<div class="message-'.$this->getType().'">'.$this->message.'</div>';
		});

Though `Figure 8` is a mouthful, the first part of it is merely defining a class extending `Eden\Core\Base`. When we instantiate `Message`, we only want to echo when the message is an error.

====
<a name="loop"></a>
## Looping

Looping is possible with Eden without leaving the chain. The following figure shows a basic example of how this can be acheived.

**Figure 9. Basic Example**

	eden()->loop(function($i) {
		echo "Hello World<br />\n";
		
		if($i > 10) {
			return false;
		}
	});

It's important that your loop callback eventually returns false to tell Eden that you are done your iteration and to continue to the next chain process. Also in PHP 5.3 you can pass variables into the loop callback manually as in `Figure 10` and `Figure 11`.

**Figure 10. Passing variables**

	$list = array(1, 2, 3, 4);
	
	eden()->loop(function($i) use ($list) {
		if($list[$i] == 3) {
			return false;
		}
	});

In `Figure 10`, we created an array of numbers called `$list` and then passed `$list` into our loop callback using PHP 5.3 `use ($list)`. Sometimes you may want the loop callback to actually manipulate the `$list` array, `Figure 11` can show how this can be acheived.

**Figure 11. Reference Pass**

	$list = array(1, 2, 3, 4);
	
	eden()->loop(function($i) use (&$list) {
		$list[] = $i + 5;
		if($list[$i] == 3) {
			return false;
		}
	});
	
	print_r($list);

`Figure 11` looks similar like `Figure 10` with the exception of `use (&$list)`, which allows our loop callback to manipulate the array on a global level. Loops also automatically passes the object from where the origin of the loop began.

**Figure 12. Using Instances**

	class User extends Eden\Core\Base {
		public $friends = array('Sarah', 'John', 'Cole');
	}
	
	eden()->User()->loop(function($i, $instance) {
		if(!isset($instance->friends[$i])) {
			return false;
		}
		
		echo $instance->friends[$i].' ';
	});

The first thing we did in `Figure 12` was create a class called *User* which extends `Eden\Core\Base`. From here we can instantiate that class and call the `loop()` method which passes our instance of *User* to be used inside of the scope.

====
<a name="state"></a>
## States

A new feature of *Eden*, promoting chainability is the use of handling chain states. A basic usage can be found in `Figure 13`.

**Figure 13. Basic Usage**

	eden()->setState('[YOUR STATE NAME]')->getState('[YOUR STATE NAME]');

`Figure 13` is pretty ambiguous, but let's say you are working on a user registration process. Usually with registration you add the new user to the database and send them an email. We should also mark whether if the email has been sent out or not. We can use states to acheived this process as in `Figure 14`,

**Figure 14. Epic State Chain**

	//1. load up mysql
	eden('mysql', '127.0.0.1', 'app', 'root', '')
		//2. load up model
		->model()
		//3. set user slug
		->setUserSlug('chris')
		//4. set user name
		->setUserName('Chris')
		//5. save model to user table in database
		->save('user')
		//6. save the user model state
		->setState('user')
		
		//7. load up email
		->Eden_Mail()
		//8. load up SMTP
		->smtp(
			'imap.gmail.com', 
			'[YOUR EMAIL]', 
			'[YOUR PASSWORD]', 
			993, 
			true)
		//9. add recipient
		->addTo('cblanquera@openovate.com')
		//10. set the subject
		->setSubject('New Registration')
		//11. set the body
		->setBody('Welcome!')
		//12. send out the email
		->send()
		//13. load up the user model state
		//we saved earlier
		->loadState('user')
		//14. set user validation
		->setUserValidation(1)
		//15. save model to user table in database
		->save('user');
	
`Figure 14` is quite a mouthful, but let's go over the main parts regarding states. The first 5 steps are about initially saving the user data into our MySQL database. Step 6 `6. save the user model state` saves the instance internally to a key called `user` as in `->setState('user')`. Next, step 7 to 12 are about sending an email out using `Eden\Mail\Smtp`. Then we return back to our user state in step 13 using `->loadState('user')` so we can confirm the email has been sent out by updating that row with step 14 `->setUserValidation(1)`.

As we can see we never had to leave the chain to accomplish multiple tasks and we never needed to set a variable as well. We can even set any variables to a state.

> **Note:** Okay almost any variable. Setting a state value to *NULL* will set the state to the instance.

**Figure 15. Setting state to a variable**

	$userId = 123;
	echo eden()->setState('user_id', $userId)->getState('user_id');

In `Figure 15`, we simply created a state and set it to `$userId`. We can now call that state at any time using `->getState('user_id')`.

> **Note:** All states are shared across every class. Because of ths, *Eden* core does not use states, so you are free to use any key name without fear.

States can also be set to an evaluated value using callback as in `Figure 16`.

**Figure 16. Evaluated State**

	echo eden()
		//1. load user state from Figure 14.
		->loadState('user')
		//2. set user id
		->setState('user_id', function($instance) {
			return $instance->getUserId();
		})
		//3. get the user_id state
		->getState('user_id');

The figure above is a continuation of `Figure 14`. We first load the user model state and we set a new state called `user_id`. The second argument step 2 we can use a callback to help evaluate the value that would be set to `user_id`.

====
<a name="route"></a>
## Class Routing

Class routing from *Eden* version 2 to version 3 has become simpler. Routing in *Eden* is similar to page routing in typical MVC frameworks, however in this subject technically called *Polymorphic Routing*. *Eden* has adopted class naming conventions made popular from *Zend Framework* and the *PSR-2* which is in relation to a cascading file system. One annoyance in this system is that class names can get really long. Long class names are harder to remember and ultimately increases the learning curve. Making virtual classes (or aliases to class names) not only makes it easier to remember, but allows developers to customize *Eden* in their own way.

**Figure 17. Virtual Classes**

	//Make an alias for Eden_Session called Session
	eden('core')->route('My', 'My_Long_Class_Name');
	
	// -- OR --
	
	eden('core')->route('My', 'My\\Long\\Class\\Name');
	 
	//... some where later in your code ...
	 
	eden()->My(); // My_Long_Class_Name
	
	// -- OR --
	
	$this->My();

> **Note** Aliasing an alias will result in both aliases pointing to the same class

In the example above, we first made an alias to `My\Long\Class\Name` called `My`. After that line, anytime that alias is called, *Eden* will know to instantiate `My\Long\Class\Name` instead. The next figure shows how we can add extra methods to a class *on-the-fly*.

**Figure 18. Virtual Methods**

	//Make an alias for Eden_Tool->output called Eden->out
	eden()->My()->route('out', function($string) {
		echo $string;
	});
	 
	//... some where later in your code ...
	 
	eden()->My()->out('This method doesn\'t really exist'); //--> This method doesn't really exist

In `Figure 18` above, we show how add a method in `My\\Long\\Class\\Name` called `out()` and then calling that virtual method.

> **Note** If there was a previously defined method called My\\Long\\Class\\Name::out() this method would be called instead.

<a name="class"></a>
## Instantiating Classes

In *Eden*, there are many ways to instantiate a class because one of *Eden's* methodologies is that *Eden* does not tell you how to build an application. Eden simply provides tools for you to build it faster. First off any class ie. `class Sample\Class {}` can be called as in `Figure 1`.

**Figure 19. Standard Instantiation**

	new Sample\Class();

Like `Figure 19` you can instantiate classes in the normal PHP way using `new`. The problem with `new` is that it also requires you to use classes in at least 2 lines of code. In Eden, we have the availability to call classes using the controller as in `Figure 20`.

**Figure 20. Use the Controller**

	eden()->Sample_Class();

As long as you start with `eden()`, you can easily instantiate classes and call methods, possible in one line. Any class you define that extends `Eden\Core\Base` can instantiate classes within the definition as in `Figure 21`.

**Figure 21. Inside the Class**

	class MyClass extends Eden\Core\Base 
	{
		public function call() {
			$this->Sample_Class();
		}
	}

Extending `Eden\Core\Base` transforms your `$this` into something as powerful as `eden()`. Much like how Eden calls classes you can instantiate classes with `$this` as in `$this('core')->inspect()` or `$this()->setTimezone('GMT')`. Also any Eden Class as well as any class you define that extends `Eden\Core\Base` can be instantiated as in `Figure 22`.

**Figure 22. The i() Method**

	MyClass::i();

Passing arguments to a constructor using any of the above figures can be achieved by simply adding it between the parameters.

**Figure 23. Passing Arguments**

	new Sample\Class('argument 1', array());

	eden()->Sample_Class('argument 1', array());

	$this->Sample_Class('argument 1', array());

	MyClass::i('argument 1', array());

====
<a name="event"></a>
## Event Driven

Events in Eden are for executing sub routines not originally part of an application scope. A basic usage can be found in `Figure 24`.

**Figure 24. Basic Usage**

	eden()->listen('to-something', function($instance, $action) {
		//do something
	})
	
	->trigger('to-something');

In `Figure 24`, there are two parts to an event. The first one is the listener. You can have many listeners to a single event. The order that it will be called is in a first come first serve order. 

**Figure 25. Importance**

	eden()->listen('to-something', function($instance, $action) {
		//do something
	}, true);

Adding `true` as the last argument in the listener method will serve up that callback first, pending if it was the last method to set it to `true`. You can also pass extra variables to a listener whenever you trigger an event. `Figure 26` explains how this can be done.

**Figure 26. Passing Arguments**

	eden()->listen('to-something', function($instance, $action, $extra, $extraMore) {
		echo $extra.' '.$extraMore; //--> something extra
	})
	
	->trigger('to-something', 'something', 'extra');

Emailing when a comment as been made on your post or logging when an error has been thrown would be valid implementations for an event driven design. With *Eden*, you can easily design your applications to be plugin ready. The example below shows a skinned down version of an order processing function.

**Figure 27. Processing Orders**

	function processOrder($email, $price) {
		if($price < 1) {
			return false;
		}
		 
		//insert into database
		 
		return true;
	}
	 
	if(processOrder('info@openovate.com', 56.99)) {
		echo 'Success!';
	}

When given a set of functional requirements we would think to add it in `processOrder()` linearly. In event driven design, a function should only perform the main process stated on the function name. `processOrder()`, for example should just insert the order into the database and nothing more. After it is done, we should trigger that this action has been performed.

**Figure 28. Adding a Trigger**

	function processOrder($email, $price) {
		if($price < 1) {
			return false;
		}
		 
		//insert into database
		 
		eden()->trigger('success', $email, 'Success!', 'We triggered a success.');
		 
		return true;
	}
	 
	if(processOrder('info@openovate.com', 56.99)) {
		echo 'Success!';
	}

In `Figure 28`, we add a trigger passing all possible variables to whatever other method is listening to that. This is the only requirement to make your application event driven. The example below represents how to build a plugin that will listen for a success event to occur.

**Figure 29. Listen**

	eden()
	->listen('success', function(
		$event, 
		$email, 
		$subject, 
		$message
	) {
		mail($email, $subject, $message);
	});

> **Note:** Eden\Core\Event, when called will be a singleton. This makes it a global event handler. You can create a separate set of events by simply extending this class.

In the example above, we created a callback that will listen to a success event. When a success event is triggered the callback method will be called passing all the arguments as specified by the trigger.

====

#Contibuting to Eden

##Setting up your machine with the Eden repository and your fork

1. Fork the main Eden repository (https://github.com/Eden-PHP/Core)
2. Fire up your local terminal and clone the *MAIN EDEN REPOSITORY* (git clone git://github.com/Eden-PHP/Core.git)
3. Add your *FORKED EDEN REPOSITORY* as a remote (git remote add fork git@github.com:*github_username*/Core.git)

##Making pull requests

1. Before anything, make sure to update the *MAIN EDEN REPOSITORY*. (git checkout master; git pull origin master)
2. If PHP Unit testing is included in this package please make sure to update it and run the test to ensure everything still works (phpunit)
3. Once updated with the latest code, create a new branch with a branch name describing what your changes are (git checkout -b bugfix/fix-twitter-auth)
    Possible types:
    - bugfix
    - feature
    - improvement
4. Make your code changes. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")
5. Once you've committed all the code to this branch, push the branch to your *FORKED EDEN REPOSITORY* (git push fork bugfix/fix-twitter-auth)
6. Go back to your *FORKED EDEN REPOSITORY* on GitHub and submit a pull request.
7. An Eden developer will review your code and merge it in when it has been classified as suitable.