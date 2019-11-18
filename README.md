Deskpro Hab
===========

[![Build Status](https://travis-ci.org/AshleyDawson/deskpro-hab.svg?branch=master)](https://travis-ci.org/AshleyDawson/deskpro-hab)

Hab(itat) is a virtual development environment bootstrapper for setting up and configuring a guest [Vagrant](https://www.vagrantup.com/) instance. The Vagrant instance includes all
development dependencies to run Deskpro and develop against it. Hab is designed for Linux and MacOS host machines.

![Deskpro Hab VM SSH Screenshot](https://github.com/AshleyDawson/deskpro-hab/raw/master/src/Resources/img/deskpro-hab-vm-screenshot.png)

Installation
------------

Please install both [Vagrant](https://www.vagrantup.com/downloads.html) and [VirtualBox](https://www.virtualbox.org/wiki/Downloads) before using Hab.

Hab is packaged as a .phar and is used to bootstrap the virtual development infrastructure from within the Deskpro project.

[Download the latest version hab.phar](https://github.com/AshleyDawson/deskpro-hab/releases) and place it within the root of your Deskpro project directory.

"Quick" Start Guide (Linux & MacOS)
------------------------------------

1. Install [Vagrant](https://www.vagrantup.com/downloads.html) and [VirtualBox](https://www.virtualbox.org/wiki/Downloads) on your computer
2. Clone the [Deskpro repository](https://github.com/deskpro/deskpro)
3. Go to the Deskpro project root and [download Hab](https://github.com/AshleyDawson/deskpro-hab/releases) to this location
4. Initialise and update Git submodules by running `git submodule init && git submodule update`
5. Run `php ./hab.phar init` to initialise Vagrant and provisioning scripts
6. Run `vagrant up`
7. Run `vagrant ssh` to access the virtual machine
8. Download and install [Composer](https://getcomposer.org/download/) globally on the virtual machine
9. Run `cd /var/www/deskpro/app/BUILD`
10. Run `composer install -o`
11. Run `cd /var/www/deskpro/www/assets/BUILD/web`
12. Run `npm install` (this could take a while)
13. Run `bower install --config.interactive=false --allow-root`
14. Run `npm run gulp`
15. Run `cd /var/www/deskpro/www/assets/BUILD/pub`
16. Run `npm install` (this could take a while)
17. Run `ASSET_SERVER_HOSTNAME=deskpro.local npm run dev` to start the asset server
18. In another terminal on the host machine (your computer)
19. Run `vagrant ssh` to access the virtual machine as another session
20. Run `bin/install --install-source dev` to install Deskpro, when prompted:
    * enter the project URL "http://deskpro.local/"
    * enter "127.0.0.1" as the database hostname and "root" as both the database username and password. The database name is "deskpro"
21. Check that the `asset_paths` are pointed to `deskpro.local:9666` in `config/config.paths.php`
22. Add the following line to the bottom of `config/advanced/config.settings.php`:
```
$SETTINGS['DESKPRO_APP_ASSETS_URL'] = 'http://deskpro.local/assets/BUILD/pub/build/';
```
23. You should now be able to access Deskpro via `http://deskpro.local/`

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

Once the VM is booted, you can then SSH into it and run the usual installation and setup scripts as described in the [Deskpro setup instructions](https://github.com/deskpro/deskpro/blob/develop/README.md).

**Note:** When running the dev assets server, do the following to override the default hostname:

```bash
$ ASSET_SERVER_HOSTNAME=deskpro.local npm run dev
```

Assets server env. variables are as follows:

* `ASSET_SERVER_HOSTNAME` - Override the asset server hostname (default is `localhost`)
* `ASSET_SERVER_PORT` - Override the asset server port (default is `9666`)

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

Default Service Connectivity
----------------------------

* MySQL DSN: `mysql://root:root@deskpro.local:3306/deskpro`
* Elasticsearch URL: `http://deskpro.local:9200/` _(requires service start)_

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
