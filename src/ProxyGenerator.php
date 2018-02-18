<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Psr\Container\ContainerInterface;

/**
 * Class ProxyGenerator
 * @package App\MessagingBundle\DependencyInjection
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ProxyGenerator
{
    /**
     * @param string $referenceName
     * @param ContainerInterface $container
     * @return object
     */
    public static function createFor(string $referenceName, ContainerInterface $container)
    {
        $gatewayByName = $container->get('messaging_system')->getGatewayByName($referenceName);
        return $gatewayByName;
    }
}