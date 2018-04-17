<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ListAllAsynchronousConsumers
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ListAllAsynchronousConsumersCommand extends Command
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
            ->setHeaders(array('Consumer Names'))
            ->setRows($repackedNames)
        ;
        $table->render();
    }
}