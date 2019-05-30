#!/usr/bin/env php
<?php

require __DIR__.'/../vendor/autoload.php';

use DeskPRO\Hab\Command\InitialiseCommand;
use DeskPRO\Hab\Command\SelfUpdateCommand;
use Symfony\Component\Console\Application;

$application = new Application('Deskpro Hab - Virtual Development Infrastructure Bootstrapper');

$application->add(new InitialiseCommand());
$application->add(new SelfUpdateCommand());

$application->run();
