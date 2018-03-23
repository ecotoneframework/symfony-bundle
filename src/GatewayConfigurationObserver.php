<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationObserver;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;
use SimplyCodedSoftware\IntegrationMessaging\Config\GatewayReference;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class NullConfigurationObserver
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class GatewayConfigurationObserver implements ConfigurationObserver
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function notifyGatewayWasBuilt(GatewayReference $gatewayReference): void
    {
        $this->container->set($gatewayReference->getReferenceName(), $gatewayReference->getGateway());
    }

    public function notifyGatewayBuilderWasRegistered(string $referenceName, string $gatewayType, string $interfaceName): void
    {
        return;
    }

    public function notifyMessageChannelWasRegistered(string $messageChannelName, string $channelType): void
    {
        return;
    }

    public function notifyConfigurationWasFinished(ConfiguredMessagingSystem $configuredMessagingSystem): void
    {
        return;
    }

    public function notifyRequiredAvailableReference(string $referenceName): void
    {
        return;
    }
}