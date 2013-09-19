![logo](http://eden.openovate.com/assets/images/cloud-social.png) Eden Core
====

## Introduction

Some of the greatest features, as well as supporting web services out of the box, 
lies in Eden's core. The core's purpose is to give the end developer an easier 
experience in OOP and design patterns while tackling some of the most complicated 
problems that exist in web development today.

#### Extending your PHP

Setting your classes as a child of Eden\Core\Base empowers your classes to do more. When accepting Eden\Core\Base 
as a parent you can now instantiate your classes in this manner.

`eden()->YOUR_CLASS_NAME();`

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

###Not a God Object

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

#####Is jQuery a God Class? 
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

#####Back to Eden

Much like jQuery, Eden follows a similar decorator pattern. You can use classes in the Eden library 
in the normal way of `new Eden\Core\Event` or `new Eden\Mysql\Factory`. Or use it this way `eden('mysql')`

====

#Contibuting to Eden

##Setting up your machine with the Eden repository and your fork

1. Fork the main Eden repository (https://github.com/Eden-PHP/Core)
2. Fire up your local terminal and clone the *MAIN EDEN REPOSITORY* (git clone git://github.com/Eden-PHP/Core.git)
3. Add your *FORKED EDEN REPOSITORY* as a remote (git remote add fork git@github.com:*github_username*/Core.git)

##Making pull requests

1. Before anything, make sure to update the *MAIN EDEN REPOSITORY*. (git checkout master; git pull origin master)
2. Once updated with the latest code, create a new branch with a branch name describing what your changes are (git checkout -b bugfix/fix-twitter-auth)
    Possible types:
    - bugfix
    - feature
    - improvement
3. Make your code changes. Always make sure to sign-off (-s) on all commits made (git commit -s -m "Commit message")
4. Once you've committed all the code to this branch, push the branch to your *FORKED EDEN REPOSITORY* (git push fork bugfix/fix-twitter-auth)
5. Go back to your *FORKED EDEN REPOSITORY* on GitHub and submit a pull request.
6. An Eden developer will review your code and merge it in when it has been classified as suitable.
