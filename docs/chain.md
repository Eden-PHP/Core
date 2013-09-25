# Chainability

One of Eden's methodologies is "when in doubt return this". Returning the same object allows method calls to be expressed in a chainable format.

The next example demonstrates passing initial arguments in a class and printing the results after various method calls.

**Figure 1. Basic Chainability**

	echo eden('type', 'Hello World')->str_replace(' ','-')->strtolower()->substr(0, 8); //--> hello-wo
	 
	/* vs */
 	
	echo substr(strtolower(str_replace(' ', '-', 'Hello World')), 0, 8); //--> hello-wo

`Figure 1` above shows that we passed Hello World into `Eden\Type\StringType` then replace spaces with a dash, lower casing and show only the first eight characters, again in one line. We can do the same with regular PHP however when another developer has to read that, they have to read it inner to outer versus left to right.

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

You probably noticed when using Eden, we didn't have to create a variable. This is the same case when dealing with multiple classes. You can instantiate classes all in the same chain.