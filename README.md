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
