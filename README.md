# sshbunny
PHP library that provides an object-oriented wrapper to connect to SSH and run shell commands with the php ssh2 extension.

# Requirements
------------

- PHP version 5.3+
- [SSH2 extension](http://www.php.net/manual/en/book.ssh2.php).
- [composer](http://getcomposer.org).

# Install with composer
------------

The best way to add the library to your project is using [composer](http://getcomposer.org).
```bash
composer require faraweilyas/sshbunny
```
or

# Clone this repo

```bash
git clone https://github.com/faraweilyas/sshbunny.git
```

# Configuration
-------------

`SSHBunny` constructor takes four parameters and they all have default values `$method='local'`, `$authType=NULL`, `$host=NULL`, `$port=22`, `$username=NULL`
 - `$method` can be set to `local` or `remote`, `local` will execute commands on your own shell without internet connection while `remote` executes commands on the remote server that you connect to based on your configuration.
 - `$authType` can be set to `KEY`, `PASSWORD` or `KEY_PASSWORD`, `KEY` and `KEY_PASSWORD` uses [ssh2_auth_pubkey_file](http://php.net/manual/en/function.ssh2-auth-pubkey-file.php) the difference is when you set `$authType='KEY_PASSWORD'` ssh2_auth_pubkey_file takes the last parameter of password which will now be required and `PASSWORD` uses [ssh2_auth_password](http://php.net/manual/en/function.ssh2-auth-password.php).
 - `$port` should be set to your server port if your are connecting to a remote server.
 - `$username` should be set to your server username.

if your are setting connection method to `$method='remote'` and `$authType = KEY || KEY_PASSWORD` that means you will need to set your public & private key file which you can do with the setters `SSHBunny` has `$sshBunny->setKeys('public_key.pub', 'private_key')` before initialization.

# Basic usage
-------------
This is just going to run locally since connection method is set to `local`
```php
<?php

use SSHBunny\SSHBunny;

require_once 'vendor/autoload.php';

// ->getData() will return output of command executed while ->getData(TRUE) will dispay the output
$sshBunny = (new SSHBunny('local'))
    ->initialize()
    ->exec("echo 'Hello World'")
    ->getData(TRUE);
```

This is going connect to a remote server since connection method is set to `remote` and authentication type is set to `KEY`
```php
<?php

use SSHBunny\SSHBunny;

require_once 'vendor/autoload.php';

defined('TEST_HOST')    ? NULL : define('TEST_HOST',    "138.222.15.1");
defined('PORT')         ? NULL : define('PORT',         "22");
defined('USERNAME')     ? NULL : define('USERNAME',     "ubuntu");
defined('PUBLIC_KEY')   ? NULL : define('PUBLIC_KEY',   'id_ssl.pub');
defined('PRIVATE_KEY')  ? NULL : define('PRIVATE_KEY',  'id_ssl');

$sshBunny = (new SSHBunny('remote', 'KEY', HOST, PORT, USERNAME))
    ->setKeys(PUBLIC_KEY, PRIVATE_KEY)
    ->initialize()
    ->exec("echo 'Hello World'")
    ->getData(TRUE);
```

Command execution can take multiple commands or you can chain on the `exec` method with another `exec` method
```php
$sshBunny = (new SSHBunny('remote', 'KEY', HOST, PORT, USERNAME))
    ->setKeys(PUBLIC_KEY, PRIVATE_KEY)
    ->initialize()
    // Multiple commands
    ->exec("echo 'Hello World'", "cd /var/www/html")
    // Method chaining
    ->exec("ls -la")
    ->getData(TRUE);
```

## Available methods

- Executed command output
```php
// Will return the result of executed command output
$sshBunny->exec("ls -la")->getData();
// Will display the result of executed command output
$sshBunny->exec("ls -la")->getData(TRUE);
```

- Clear stored executed command output
```php
// Will clear the first executed command output and return the next executed command output
$sshBunny->exec("ls -la")->clearData()->exec("whoami")->getData(TRUE);
```

- Disconnect server connection
```php
// Will run the commands provided and display the result then disconnect from the server
$sshBunny->exec("ls -la", "whoami")->getData(TRUE)->disconnect();
```
