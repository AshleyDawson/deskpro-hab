Deskpro Hab
===========

[![Build Status](https://travis-ci.org/AshleyDawson/deskpro-hab.svg?branch=master)](https://travis-ci.org/AshleyDawson/deskpro-hab)

Hab(itat) is a virtual development environment bootstrapper for setting up and configuring a guest [Vagrant](https://www.vagrantup.com/) instance. The Vagrant instance includes all
development dependencies to run Deskpro and develop against it.

![Deskpro Hab VM SSH Screenshot](https://github.com/AshleyDawson/deskpro-hab/raw/master/src/Resources/img/deskpro-hab-vm-screenshot.png)

Installation
------------

Please install both [Vagrant](https://www.vagrantup.com/downloads.html) and [VirtualBox](https://www.virtualbox.org/wiki/Downloads) before using Hab.

Hab is packaged as a .phar and is used to bootstrap the virtual development infrastructure from within the Deskpro project.

[Download the latest version hab.phar](https://github.com/AshleyDawson/deskpro-hab/releases) and place it within the root of your Deskpro project directory.

Usage
-----

To initialise the Vagrantfile, settings and provisioning scripts, run the `init` command:

```bash
$ php hab.phar init
```

This will install the necessary files ready to start your virtual machine using the standard Vagrant commands:

```bash
$ vagrant up && vagrant ssh
```

You can pass along several options to adjust the virtual machine instance settings that are subsequently stored in the `hab.json` file.

```bash
php hab.phar init \
    --project-dir /path/to/project/dir \
    --hostname deskpro.local \
    --ip 192.168.2.34 \
    --memory 2048 \
    --cpus 4 \
    --force
```

The options are as follows:

* **project-dir** - The location of your Deskpro project directory, default is `current directory`
* **hostname** - Hostname of the VM, default is `deskpro.local`
* **ip** - Private IP address of the guest VM, default is `10.40.1.23`
* **memory** - How much memory (in megabytes) is allocated to the VM, default is `4096`
* **cpus** - How many CPUs the VM has, default is `2`
* **force** - Force an overwrite of the hab bootstrap files, default is `false`

After Hab has been initialised you should exclude the following files from version control (usually by adding to a `.gitignore` file):

```text
/.vagrant
/.hab
/hab.phar
/hab.json
/Vagrantfile
```

**Note:** To run the Deskpro test suite you will need to configure the application to use `--login-path` option for things like
`mysqldump`, etc. as this will suppress the "password in command prompt is insecure" warnings. This is done by adding the
`--login-path=local` option to the test configuration database parameters (after copying the .dist config):

```php
# app/BUILD/tests/config/config.all.php

// ...

$CONFIG['database'] = [
    'host'     => '127.0.0.1',
    'user'     => 'root',
    'password' => 'root',
    'dbname'   => $dbName.$dbPostfix,
    'login-path' => 'local', // Append this parameter
];

// ...
```

Updating
--------

The `hab.phar` package may be self updated using the following command:

```bash
$ php hab.phar self-update
```

After updating, you'd usually want to force initialise the modified bootstrap files:

```bash
$ php hab.phar init --force
```

Testing
-------

You may run the Hab functional test suite using the following:

```bash
$ composer install
$ vendor/bin/phpunit -c .
```
