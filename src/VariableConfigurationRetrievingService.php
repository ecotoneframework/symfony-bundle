<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationVariableRetrievingService;
use SimplyCodedSoftware\IntegrationMessaging\Support\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class VariableConfigurationRetrievingService
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class VariableConfigurationRetrievingService implements ConfigurationVariableRetrievingService
{
    /**
     * @var Container
     */
    private $container;

    /**
     * VariableConfigurationRetrievingService constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function has(string $variableName): bool
    {
        return $this->container->hasParameter($variableName);
    }

    public function get(string $variableName)
    {
        return $this->container->getParameter($variableName);
    }
}