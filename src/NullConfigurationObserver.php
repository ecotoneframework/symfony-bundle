<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationObserver;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;

/**
 * Class NullConfigurationObserver
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class NullConfigurationObserver implements ConfigurationObserver
{
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