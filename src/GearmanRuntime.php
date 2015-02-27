<?php

namespace Smart\Core;

use Sinergi\Gearman\Command\RestartCommand;
use Sinergi\Gearman\Command\StartCommand;
use Sinergi\Gearman\Command\StopCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;

class GearmanRuntime implements RuntimeInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function configure()
    {
        $errorHandler = new ErrorHandler($this->container->getGearmanLogger());
        set_error_handler([$errorHandler, 'error']);
        set_exception_handler([$errorHandler, 'exception']);
        register_shutdown_function([$errorHandler, 'shutdown']);
    }

    public function run()
    {
        $daemon = true;
        $command = $_SERVER['argv'][1];
        if (stripos($_SERVER['argv'][2], '--daemon=') === 0) {
            if ($_SERVER['argv'][2] === '--daemon=false' || $_SERVER['argv'][2] === '--daemon=0') {
                $daemon = false;
            }
        }

        if ($command === 'start') {
            $this->startGearman(false, $daemon);
        } elseif ($command === 'stop') {
            $this->stopGearman();
        } elseif ($command === 'restart') {
            $this->startGearman(true, $daemon);
        }
    }

    private function stopGearman()
    {
        $command = new StopCommand();
        $command->setConfig($this->container->getGearman()->getConfig());
        $command->setGearmanApplication($this->container->getGearman()->getApplication());
        $command->setProcess($this->container->getGearman()->getProcess());
        $command->run(new ArrayInput([]), new ConsoleOutput());
    }

    /**
     * @param bool $restart
     * @param bool $daemon
     */
    private function startGearman($restart = false, $daemon = true)
    {
        if (!$restart) {
            $command = new StartCommand();
        } else {
            $command = new RestartCommand();
        }
        $command->setConfig($this->container->getGearman()->getConfig());
        $command->setGearmanApplication($this->container->getGearman()->getApplication());
        $command->setProcess($this->container->getGearman()->getProcess());
        $command->setIsDaemon($daemon);
        $command->run(new ArrayInput([]), new ConsoleOutput());
    }
}
