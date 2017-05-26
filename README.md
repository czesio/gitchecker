gitchecker
=========

# Installation

To get more from project, please add git api token to parameter.yml file. 
It let you make more repository comparision. Git api has strict limit request per hour for unauthorising users. 

Remember to add proper access to log, cache and session directory:

[File permission problem](http://symfony.com/doc/current/setup/file_permissions.html)

# Api

 To see aviable api function, in browser go to addres http://your_repo_domain/api/doc
 
 Clicking on "Sandbox" you can make test api request and see results.

# Test

 If you have installed phpunit then in project dir:
 
 ```bash
 $ phpunit
 ```
 You can also download PHAR file directly to project dir and run:
 
```bash
$ wget https://phar.phpunit.de/phpunit.phar
$ php phpunit.phar
```