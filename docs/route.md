# Class Routing

Class routing from *Eden* version 2 to version 3 has become simpler. Routing in *Eden* is similar to page routing in typical MVC frameworks, however in this subject technically called *Polymorphic Routing*. *Eden* has adopted class naming conventions made popular from *Zend Framework* and the *PSR-2* which is in relation to a cascading file system. One annoyance in this system is that class names can get really long. Long class names are harder to remember and ultimately increases the learning curve. Making virtual classes (or aliases to class names) not only makes it easier to remember, but allows developers to customize *Eden* in their own way.

**Figure 1. Virtual Classes**

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

**Figure 2. Virtual Methods**

	//Make an alias for Eden_Tool->output called Eden->out
	eden()->My()->route('out', function($string) {
		echo $string;
	});
	 
	//... some where later in your code ...
	 
	eden()->My()->out('This method doesn\'t really exist'); //--> This method doesn't really exist

In `Figure 2` above, we show how add a method in `My\\Long\\Class\\Name` called `out()` and then calling that virtual method.

> **Note** If there was a previously defined method called My\\Long\\Class\\Name::out() this method would be called instead.