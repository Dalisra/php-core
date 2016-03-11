Welcome to Core installation.

1. Add core folder to your www folder (where webroot is).
2. Add .htaccess file to your www folder (where webroot is), that looks like this:

    #php_flag display_startup_errors on
    #php_flag display_errors on
    #php_flag html_errors on

    # enable PHP error logging
    #php_flag  log_errors on
    #php_value error_log  /var/www/html/error.log

    #Options +FollowSymLinks -MultiViews -indexes
    RewriteEngine on
    RewriteRule ^$ default/webroot/ [L]
    RewriteRule ^(.*)$ default/webroot/$1 [L]

3a. Add a default site folder named "default" or your client name.

3b. If you added a folder that is not named "default" you will have to create a symlink to your clients folder.

4. Enjoy your site!
