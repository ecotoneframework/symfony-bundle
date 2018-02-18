<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationObserver;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfiguredMessagingSystem;

/**
 * Class ContainerConfiguratorForMessagingObserver
 * @package App\MessagingBundle\DependencyInjection
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ContainerConfiguratorForMessagingObserver implements ConfigurationObserver
{
    /**
     * @var self
     */
    private static $observer;
    /**
     * @var string[]
     */
    private $registeredGateways = [];
    /**
     * @var array|string[]
     */
    private $requiredReferences = [];

    private function __construct()
    {
    }

    public static function create(): self
    {
        if (!self::$observer) {
            self::$observer = new self();
        }

        return self::$observer;
    }

    /**
     * @return array|string[]
     */
    public function getRegisteredGateways(): array
    {
        return $this->registeredGateways;
    }

    public function getRequiredReferences(): array
    {
        return $this->requiredReferences;
    }

    public function notifyGatewayBuilderWasRegistered(string $referenceName, string $gatewayType, string $interfaceName): void
    {
        $this->registeredGateways[$referenceName] = $interfaceName;
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
        $this->requiredReferences[] = $referenceName;
    }
}