<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class RunAsynchronousConsumerCommand
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class RunAsynchronousConsumerCommand extends Command
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
            ->setName('integration-messaging:run-consumer')
            ->addArgument('name', InputArgument::REQUIRED, 'Pass consumer name to run')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->configuredMessagingSystem->runSeparatelyRunningConsumerBy($input->getArgument("name"));
    }
}