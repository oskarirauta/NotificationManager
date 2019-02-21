# NotificationManager
PHP application for sending iOS apns notifications and storing device tokens.
Android, Microsoft, etc. not supported.

NotificationManager is a set of php scripts and javascript which need to be installed on a (https) web server with support for PHP (7.x tested) with SqLite enabled (not PDO).

There are 2 scripts which can be utilized with web server:
 - manager.php
 - store.php

There's also password.php which is supposed to be executed in shell to generate a password hash for login.
NotificationManager uses Sqlite database. 

# store.php
This is used by iOS application. Application contacts store.php with parameters providing all necessary information and then store's that information to database.

# manager.php
Manager is a application that allows one to view registered tokens and other information. It can be also used to post a notification message to users. You can choose between debug or production environment.
A notification can also be sent to single device, if you can identify it from the list, by selecting id for that device.

# Setup
Always when there says product, use it in lower case letters to avoid issues.

 - Generate password hash with `password.php` from shell:
   ```# php password.php```
   Then copy generated hash to clipboard.
 - Edit `inc/settings.php` file:
   - set credentials => userid to userid of your desire.
   - paste password hash from clipboard to credentials => password
   - add product titles to products list.
   - add product titles to cert_password list.
   - add certificate passwords to cert_password list ( development and production environments must have same password )
 - store your first token (can be fake):
   - by using `store.php` from shell, but first make necessary changes in 2 places in `store.php`. They are commented out, first one is where you force SSL as detected and second part is near end of file, manually enter apropriate fake data for fake token entry.
 - Add certificates to `certs/`:
   For each product, 2 certificates are required with password matching with entry in `inc/settings.php`. Name them like this:
   - product.pem
   - product_debug.pem
   
# License
NotificationMaster's apns code is partially copied from another project, which might be under influence of a licenses of a kind. NotificationMaster uses nanoajax js library. Rest of project is work of Oskari Rauta and code is release as-is in the github for everyone. In case that you improve NotificationMaster, do not hesitate to PR. NotificationMaster is available free for distribution, using or modification. Would be glad to get some credits though ;)
