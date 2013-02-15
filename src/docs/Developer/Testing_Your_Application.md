# Testing Your Applications

Bonfire makes testing your projects using [SimpleTest](http://simpletest.org) and [Mockery](https://github.com/padraic/mockery) as easy as possible, both from the command line and from the browser.

## Installing Simpletest

SimpleTest can be installed either manually, or via [Composer](http://getcomposer.org).

### via Composer

This step assumes that you have Composer installed already on your development machine. If you do not, first [install using Composer's guide](http://getcomposer.org/doc/00-intro.md).

Bonfire's <tt>composer.json</tt> file has everything setup for you to install both Composer and Mockery in your development environment. Simply run a

    composer install --dev

on the command line within the <tt>src</tt> folder of your project. This will install both packages into the <tt>/Vendor</tt> folder.

### Manually

To install SimpleTest by hand, [download the latest release](http://sourceforge.net/projects/simpletest/files/simpletest/simpletest_1.1/simpletest_1.1.0.tar.gz/download) and uncompress it somewhere on your computer. Copy the <tt>simpletest</tt> folder to <tt>src/vendor/simpletest</tt>. This should leave the folder path looking like:

    src/
        vendor/
            simpletest/
                simpletest/
                    . . . simpletest's files . . .

The extra folder is necessary to work with the recommended Composer installation.

Installing Mockery is the same procedure.

- [Download Mockery](https://github.com/padraic/mockery/archive/master.zip)
- Uncompress it somewhere.
- Rename the folder from <tt>mockery-master</tt> to <tt>mocker</tt>
- Copy it to <tt>src/vendor/mockery</tt>

Your folder structure should now look like:

    src/
        vendor/
            mockery/
                mockery/
                    . . . mockery's files . . .

## Organizing Tests

We have adopted the common PHPUnit method of mirroring the folder/file structure of the application. This makes finding the proper test file simple for anyone coming into the project fresh, or yourself coming back after months away.

Files must be named with one of the following strings found inside the file name: either <tt>Test</tt> or <tt>_test</tt>. The first option matches common PHPUnit settings, while the second version is what is recommended by the SimpleTest site. Either naming convention works just fine within Bonfire's <tt>run.php</tt> script.

    // Invalid test file names
    TestingUsers.php
    test_users.php

    // Valid test file names
    UsersTest.php
    users_test.php

The class name inside of the file MUST match the name of the file itself. If your file is named <tt>UsersTest.php</tt> then the class must be named <tt>UsersTest</tt>.


## Running Tests

Tests can be ran in a number of ways, including from the command line and from your web browser.

### From the Command Line

At it's simplest, you can run the <tt>tests/run.php</tt> script and all of your tests will be ran, as well as all of Bonfire's core tests. The tests are found by running through the entire folder structure looking for valid test files.

    php run.php


#### Ignoring Bonfire Tests

You generally are not going to want to test both your entire application as well as the Bonfire core at the same. To make this a little simpler, but still run a complete set of tests at once, we have provided two CLI arguments.

<tt>-a, --app_only</tt>

Will run all tests in the tests folder except for those located in the <tt>bonfire</tt> folder.

    php tests/run.php -a
    php tests/run.php --app_only

<tt>-b, --bf_only</tt>

Will run all tests in the tests folder except for those located in the <tt>application</tt> folder. This is intended to make contributing to Bonfire easier.

    php tests/run.php -b
    php tests/run.php -bf_only


### From the Browser

Due to security precautions, the tests folder is not available from the web root by default. To get around this, you can create a new file in the web root that simply loads the existing <tt>run.php</tt> file in the <tt>tests</tt> folder.

First, create a new file at <tt>src/public/tests.php</tt>. This file only needs a single line:

    <?php require('../../tests/run.php');

Now, when you access <tt>http://mybonfiresite.com/tests.php</tt> the tests GUI should appear.