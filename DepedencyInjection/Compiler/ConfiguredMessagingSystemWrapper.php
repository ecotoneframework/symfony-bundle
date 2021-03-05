<?php

namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;

use Ecotone\Messaging\Config\ConfiguredMessagingSystem;
use Ecotone\Messaging\MessageChannel;
use Ecotone\SymfonyBundle\EcotoneSymfonyBundle;
use Symfony\Component\DependencyInjection\Container;

class ConfiguredMessagingSystemWrapper implements ConfiguredMessagingSystem
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function getGatewayByName(string $gatewayReferenceName): object
    {
        return $this->getConfiguredSystem()->getGatewayByName($gatewayReferenceName);
    }

    public function getNonProxyGatewayByName(string $gatewayReferenceName): \Ecotone\Messaging\Config\NonProxyCombinedGateway
    {
        return $this->getConfiguredSystem()->getNonProxyGatewayByName($gatewayReferenceName);
    }

    public function getGatewayList(): iterable
    {
        return $this->getConfiguredSystem()->getGatewayList();
    }

    public function getMessageChannelByName(string $channelName): MessageChannel
    {
        return $this->getConfiguredSystem()->getMessageChannelByName($channelName);
    }

    public function run(string $endpointId): void
    {
        $this->getConfiguredSystem()->run($endpointId);
    }

    public function list(): array
    {
        return $this->getConfiguredSystem()->list();
    }

    public function runConsoleCommand(string $commandName, array $parameters): mixed
    {
        return $this->getConfiguredSystem()->runConsoleCommand($commandName, $parameters);
    }

    private function getConfiguredSystem(): ConfiguredMessagingSystem
    {
        return $this->container->get(EcotoneSymfonyBundle::CONFIGURED_MESSAGING_SYSTEM);
    }
}