<?php

namespace Ecotone\SymfonyBundle\Command;

use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunAsynchronousConsumerCommand
 * @package Ecotone\SymfonyBundle
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class RunPollableEndpointCommand extends Command
{
    /**
     * @var ConfiguredMessagingSystem
     */
    private $configuredMessagingSystem;

    /**
     * ListAllAsynchronousConsumers constructor.
     *
     * @param ConfiguredMessagingSystem $configuredMessagingSystem
     */
    public function __construct($configuredMessagingSystem)
    {
        $this->configuredMessagingSystem = $configuredMessagingSystem;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setName('ecotone:run-endpoint')
            ->addArgument('name', InputArgument::REQUIRED, 'Pass endpoint name to run');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configuredMessagingSystem->runSeparatelyRunningEndpointBy($input->getArgument("name"));
        
        return 0;
    }
}