<?php

namespace DeskPRO\Hab\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class AbstractCommandTest
 *
 * @package DeskPRO\Hab\Test
 */
abstract class AbstractCommandTest extends TestCase
{
    /**
     * @var string
     */
    private $tmpProjectDir;

    /**
     * {@inheritDoc}
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $testDir = strtolower((new \ReflectionClass(static::class))->getShortName()).'-'.$this->getName();

        (new Filesystem())->mkdir(
            $this->tmpProjectDir = $_ENV['TEST_PROJECT_ROOT_DIR'].DIRECTORY_SEPARATOR.$testDir
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->tmpProjectDir);
    }

    /**
     * @return string
     */
    protected function tmpProjectDir(): string
    {
        return $this->tmpProjectDir;
    }
}
