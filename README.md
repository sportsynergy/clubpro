
[![Build Status](https://travis-ci.org/sportsynergy/clubpro.png?branch=master)](https://travis-ci.org/sportsynergy/clubpro)

Clubpro 
=============

We make software to manage your court schedules, run your leagues and keep your club running smoothly. For you and your club there is nothing to install or configure. We are so sure that you will find our service indispensable that we offer a money back guarantee.

About Clubpro
--------
Clubpro is a online court reservation platform.  


Getting this running
--------
1. Create a mysql database and load data/base.sql
2. Rename the application.php.default to application.php
3. Update the database setting in step 2 with step 1
4. Add an apache configuration file that looks something like this:


Deploying
-------

1. Rename build.properties.default to build.properties
2. Make sure everything looks ok in the properties file
3. Run ant



## Troubleshooting

#### If you get this...

BUILD FAILED
/Users/adampreston/Repository/clubpro-build/build.xml:89: Problem: failed to create task or type scp
Cause: the class org.apache.tools.ant.taskdefs.optional.ssh.Scp was not found.
        This looks like one of Ant's optional components.
Action: Check that the appropriate optional JAR exists in
        -/usr/share/ant/lib
        -/Users/adampreston/.ant/lib
        -a directory added on the command line with the -lib argument

Do not panic, this is a common problem.
The commonest cause is a missing JAR.

This is not a bug; it is a configuration problem


#### Do this

Copy this file to your ant lib folder (/usr/share/ant/lib) 

* jsch-0.1.50.jar (http://www.jcraft.com/jsch/)




