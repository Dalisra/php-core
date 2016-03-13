Welcome to Core installation.

1. Add core folder (with lib folder and all "corestuff") to your www folder (where webroot is).
2. Add .htaccess file to your www folder (where webroot is), that looks like this:

    #php_flag display_startup_errors on
    #php_flag display_errors on
    #php_flag html_errors on

    # enable PHP error logging
    #php_flag  log_errors on
    #php_value error_log  /var/www/html/logs/error.log

    #Options +FollowSymLinks -MultiViews -indexes
    RewriteEngine on
    RewriteRule ^$ default/webroot/ [L]
    RewriteRule ^(.*)$ default/webroot/$1 [L]

3a. Add a default site folder named as your client name (with default site files).

3b. If you added a folder that is NOT named "default" you will have to create a symlink to the other folder that you created.
    Make sure you stand in your www folder (where webroot is) and run this command:
    ln -s [yourfolder] default
    For windows bruk mklink kommando.

4. Create empty "logs" folder in your www folder (where webroot is).

5. Make folder core/lib/Smarty/templates_c writable: chmod 777 [path] -R
    For windows you dont need.

6. Make folder core/lib/Smarty/cache writable: chmod 777 [path] -R
    For windows you dont need.

99. Enjoy your site!

100. [Optional] Create more default site folders and change between them by changing what symlink is linking to.
    Or you can change .htaccess file that you created to support several sites.
