## Installing PHP-Core
This guide will show you how to get startet with a clean installation of the PHP-Core and a default site project.
It assumes that no other projects are previously installed on your webserver.

If you already have the core and a site installed, and wish to add another site,
we recommend that you follow the php-site installation guide instead: https://github.com/Dalisra/php-site

#### 1. Adding the core folder
Clone/Install the php-core project into a folder named "core" in your webserver root.
For example in a Wamp environment on Windows, this would be "C:\wamp\www", "/var/www" in Lamp on Linux etc.

This folder holds all the core framework files, db, bootstrap, config-files and so on.

_Optional:_
You can also give the core folder a name of your choosing, and use symlinks to get the same behaviour, though this can produce some issues.
See _Symlinking the core and site folders_ in the _Additional guides_ section.

#### 2. Adding .htaccess file at webserver root
_As for now this guide assumes you are working with an apache web server, and thus uses .htaccess and Apache's Rewrite module.
How to do rewriting with other web-servers might be documented later._

Go to your webservers root folder, and add a new file called ".htaccess".
Then open the file, paste the following content and save the file.

    #php_flag display_startup_errors on
    #php_flag display_errors on
    #php_flag html_errors on

    # enable PHP error logging
    #php_flag  log_errors on
    #php_value error_log  /var/www/html/logs/error.log

    #Options +FollowSymLinks -MultiViews -indexes
    RewriteEngine on
    RewriteRule ^$ site-default/webroot/ [L]
    RewriteRule ^(.*)$ site-default/webroot/$1 [L]

#### 3. Adding the site folder
Clone/Install the php-site project into a folder named "site-default" (ref. the .htaccess file you just added in step 2. ) in your webserver root.

The php-site project: https://github.com/Dalisra/php-site

This folder holds all the site specific files and folders. I.e. some default templates and controllers for you to use.
It also comes prepackaged with bootstrap, font-awesome, and jQuery

_Optional:_
You can also give the site folder a name of your choosing, and use symlinks to get the same behaviour, though this can produce some issues.
See _Symlinking the core and site folders_ in the _Additional guides_ section.

#### 4. Create log folder
PHP-Core uses the log4php library for logging, and will put log files in the "logs" folder of your webserver root.
This may not be necessary on all operating systems, but it is good practice to make sure that you have the folder installed.

The folder should be named "logs" in lowercase.

See _The complete directory structure_ for referencing.

#### 5. Make Smarty folders writable
This should just be necessary if you are on a Unix based system. Windows users, disregard this.

Open your preferred Terminal app, go to your webserver root and run these commands:

    chmod 777 core/lib/Smarty/templates_c -R
    chmod 777 core/lib/Smarty/cache -R

#### 6. Enjoy your site!
Also, be sure to check out [the php-site project](https://github.com/Dalisra/php-site) and install lots of sites on the same webserver using the same core and **be awesome!**


## Additional guides
### Symlinking the core and site folders
If you added a folder that is NOT named "site-default", you will have to create a symlink to the other folder that you created.
Make sure you stand in your www folder (where webroot is) and run the following command.

_NB! Linked folders is not compliant with all web servers, and proper function cannot be guaranteed if using this method._

On Linux:

    ln -s [your_folder_name] site-default

On Windows:

    mklink /D site-default [your_folder_name]

### The complete directory structure

    \some\path\to\your\www\
    ├───core
    │   ├───config
    │   ├───controllers
    │   ├───lib
    │   │   ├───log4php
    │   │   │   └───2.3.0
    │   │   │       ├───appenders
    │   │   │       ├───configurators
    │   │   │       ├───filters
    │   │   │       ├───helpers
    │   │   │       ├───layouts
    │   │   │       ├───pattern
    │   │   │       ├───renderers
    │   │   │       └───xml
    │   │   └───Smarty
    │   │       ├───cache
    │   │       ├───config
    │   │       ├───plugins
    │   │       ├───Smarty-3.1.14
    │   │       │   ├───plugins
    │   │       │   └───sysplugins
    │   │       ├───Smarty-3.1.29
    │   │       │   ├───plugins
    │   │       │   └───sysplugins
    │   │       └───templates_c
    │   ├───models
    │   ├───tests
    │   └───views
    │       ├───admin
    │       └───error_pages
    └───site-default
    │   ├───config
    │   ├───controllers
    │   ├───views
    │   │   ├───components
    │   │   ├───error_pages
    │   │   ├───layouts
    │   │   └───pages
    │   └───webroot
    │      ├───bootstrap-3.3.6
    │      │   ├───css
    │      │   ├───fonts
    │      │   └───js
    │      ├───css
    │      ├───font-awesome-4.5.0
    │      │   ├───css
    │      │   └───fonts
    │      ├───jquery-1.12.1
    │      └───js
    ├───logs