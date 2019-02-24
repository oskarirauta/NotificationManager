## NotificationManager
PHP application for sending iOS apns notifications and storing device tokens.
Android, Microsoft, etc. not supported.

NotificationManager is a set of php scripts and javascript which need to be installed on a (https) web server with support for PHP (7.x tested) with SqLite enabled (not PDO).

There are 2 scripts which can be utilized with web server:
 - manager.php
 - store.php

There's also password.php which is supposed to be executed in shell to generate a password hash for login.
NotificationManager uses Sqlite database. 

## Requirements
PHP 7.x or newer.
SSL/https on server.

### store.php
This is used by iOS application. Application contacts store.php with parameters providing all necessary information and then store's that information to database.
Required parameters (get, post and request supported for delivery):
 - product = identical to product name in settings.php
 - debugOnly (optional ) = 1 for debug tokens. Empty or missing for production tokens.
 - uuid = device's identifier for developer's
 - deviceToken = notification registration token
 - version = app version (string)
 - build = build number (integer)
 - password = product's store password (stored as hash in settings.php)

Example:
https://myserver.com/NotificationManager/store.php?product=myproduct&debugOnly=1&uuid=1234&deviceToken=4321&version=1.0&build=100&password=password

### manager.php
Manager is a application that allows one to view registered tokens and other information. It can be also used to post a notification message to users. You can switch between debug or production environment.
A notification can also be sent to single device, if you can identify it from the list, by selecting id for that device.

### Setup
To avoid issues, store userid's and products in lowercase with following character set: `abcdefghijklmnopqrstuvwxyz@,.1234567890`
Steps to take system into use:

 - Create users in settings.php, store every user's password as hash which can be generated with password.php
   ```# php password.php```
   I recommend copy & paste of hashes.
 - Create appropriate products in settings.php's product list variable. Set store passwords as hashes just like you did for user's passwords. Certificate passwords must be stored in clear text.
 - Add .pem certificates to certs/ for all products. This means 2 certificates/product.
   You should name them like this:
    - production: product.pem
    - debug environment: product_debug.pem
   
### License
NotificationMaster's apns code is partially copied from another project, which might be under influence of a licenses of a kind. NotificationMaster uses nanoajax js library. Rest of project is work of Oskari Rauta and code is release as-is in the github for everyone. In case that you improve NotificationMaster, do not hesitate to PR. NotificationMaster is available free for distribution, using or modification. Would be glad to get some credits though ;)
