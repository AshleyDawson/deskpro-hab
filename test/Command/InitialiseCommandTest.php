<?php

namespace DeskPRO\Hab\Test\Command;

use DeskPRO\Hab\Command\InitialiseCommand;
use DeskPRO\Hab\Test\AbstractCommandTest;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class InitialiseCommandTest
 *
 * @package DeskPRO\Hab\Test\Command
 */
class InitialiseCommandTest extends AbstractCommandTest
{
    /**
     * @var CommandTester
     */
    private $command;

    /**
     * {@inheritDoc}
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new CommandTester(new InitialiseCommand());
    }

    /**
     * @test
     */
    public function it_creates_a_vagrant_file_if_one_does_not_exist()
    {
        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $this->assertFileExists($this->tmpProjectDir().DIRECTORY_SEPARATOR.'Vagrantfile');
    }

    /**
     * @test
     */
    public function it_does_not_create_a_vagrant_file_if_one_already_exists()
    {
        $vagrantFilePath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'Vagrantfile';

        file_put_contents($vagrantFilePath, 'EXISTING');

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $this->assertEquals('EXISTING', file_get_contents($vagrantFilePath));
    }

    /**
     * @test
     */
    public function it_overwrites_vagrant_file_if_forced()
    {
        $vagrantFilePath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'Vagrantfile';

        file_put_contents($vagrantFilePath, 'EXISTING');

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
            '--force' => true,
        ]);

        $this->assertNotEquals('EXISTING', file_get_contents($vagrantFilePath));
    }

    /**
     * @test
     */
    public function it_creates_hab_file_if_one_does_not_exist()
    {
        $habFilepath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'hab.json';

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $this->assertFileExists($habFilepath);

        $hab = json_decode(file_get_contents($habFilepath), true);

        $this->assertEquals('deskpro.local', $hab['hostname']);
        $this->assertEquals('10.40.1.23', $hab['ip']);
        $this->assertEquals(4096, $hab['memory']);
        $this->assertEquals(2, $hab['cpus']);
    }

    /**
     * @test
     */
    public function it_does_not_create_hab_file_if_one_already_exists()
    {
        $habFilepath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'hab.json';

        file_put_contents($habFilepath, json_encode([
            'example' => 'EXISTING',
        ]));

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $hab = json_decode(file_get_contents($habFilepath), true);

        $this->assertEquals('EXISTING', $hab['example']);
    }

    /**
     * @test
     */
    public function it_overwrites_hab_file_if_forced()
    {
        $habFilepath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'hab.json';

        file_put_contents($habFilepath, json_encode([
            'example' => 'EXISTING',
        ]));

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
            '--force' => true,
        ]);

        $hab = json_decode(file_get_contents($habFilepath), true);

        $this->assertEquals('deskpro.local', $hab['hostname']);
        $this->assertEquals('10.40.1.23', $hab['ip']);
        $this->assertEquals(4096, $hab['memory']);
        $this->assertEquals(2, $hab['cpus']);
    }

    /**
     * @test
     */
    public function it_creates_hab_file_with_changed_settings()
    {
        $habFilepath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'hab.json';

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
            '--hostname' => 'example.local',
            '--ip' => '192.168.123.321',
            '--memory' => 2048,
            '--cpus' => 4,
        ]);

        $this->assertFileExists($habFilepath);

        $hab = json_decode(file_get_contents($habFilepath), true);

        $this->assertEquals('example.local', $hab['hostname']);
        $this->assertEquals('192.168.123.321', $hab['ip']);
        $this->assertEquals(2048, $hab['memory']);
        $this->assertEquals(4, $hab['cpus']);
    }

    /**
     * @test
     */
    public function it_creates_hab_file_and_sets_memory_and_cpus_to_sensible_values()
    {
        $habFilepath = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'hab.json';

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
            '--memory' => 128,
            '--cpus' => 0,
        ]);

        $this->assertFileExists($habFilepath);

        $hab = json_decode(file_get_contents($habFilepath), true);

        $this->assertEquals(1024, $hab['memory']);
        $this->assertEquals(1, $hab['cpus']);
    }

    /**
     * @test
     */
    public function it_creates_a_hab_dir_if_one_does_not_exist()
    {
        $habDir = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'.hab';

        $this->assertDirectoryNotExists($habDir);

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $this->assertDirectoryExists($habDir);
        $this->assertFileExists($habDir.DIRECTORY_SEPARATOR.'provision'.DIRECTORY_SEPARATOR.'main.sh');
    }

    /**
     * @test
     */
    public function it_does_not_create_a_hab_dir_if_one_already_exists()
    {
        $habDir = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'.hab';
        $habDirTestFile = $habDir.DIRECTORY_SEPARATOR.'test_file';

        (new Filesystem())->dumpFile($habDirTestFile, 'EXISTING');

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
        ]);

        $this->assertFileExists($habDirTestFile);
        $this->assertEquals('EXISTING', file_get_contents($habDirTestFile));
        $this->assertFileNotExists($habDir.DIRECTORY_SEPARATOR.'provision'.DIRECTORY_SEPARATOR.'main.sh');
    }

    /**
     * @test
     */
    public function it_overwrites_hab_dir_if_forced()
    {
        $habDir = $this->tmpProjectDir().DIRECTORY_SEPARATOR.'.hab';
        $habDirTestFile = $habDir.DIRECTORY_SEPARATOR.'test_file';

        (new Filesystem())->dumpFile($habDirTestFile, 'EXISTING');

        $this->command->execute([
            '--project-dir' => $this->tmpProjectDir(),
            '--force' => true,
        ]);

        $this->assertFileNotExists($habDirTestFile);
        $this->assertFileExists($habDir.DIRECTORY_SEPARATOR.'provision'.DIRECTORY_SEPARATOR.'main.sh');
    }
}
