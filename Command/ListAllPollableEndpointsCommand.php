<?php

namespace Ecotone\SymfonyBundle\Command;

use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListAllAsynchronousConsumers
 * @package Ecotone\SymfonyBundle
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ListAllPollableEndpointsCommand extends Command
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
            ->setName('ecotone:list-all-pollable-endpoints');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $repackedNames = [];
        foreach ($this->configuredMessagingSystem->getListOfSeparatelyRunningConsumers() as $consumerName) {
            $repackedNames[] = [$consumerName];
        }

        $table = new Table($output);
        $table
            ->setHeaders(array('Endpoint Names'))
            ->setRows($repackedNames)
        ;
        $table->render();

        return 0;
    }
}