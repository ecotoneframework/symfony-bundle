<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use ProxyManager\Factory\RemoteObject\AdapterInterface;
use Symfony\Component\DependencyInjection\Container;
use SimplyCodedSoftware\Messaging\Config\MessagingSystem;

/**
 * Class ProxyGenerator
 * @package App\MessagingBundle\DependencyInjection
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ProxyGenerator
{
    /**
     * @param string    $referenceName
     * @param Container $container
     * @param string    $interface
     *
     * @return object
     */
    public static function createFor(string $referenceName, Container $container, string $interface)
    {
        $factory = new \ProxyManager\Factory\RemoteObjectFactory(new class ($container, $referenceName) implements AdapterInterface  {

            /**
             * @var Container
             */
            private $container;
            /**
             * @var string
             */
            private $referenceName;

            /**
             *  constructor.
             *
             * @param Container $container
             * @param string    $referenceName
             */
            public function __construct(Container $container, string $referenceName)
            {
                $this->container = $container;
                $this->referenceName = $referenceName;
            }

            /**
             * @inheritDoc
             */
            public function call(string $wrappedClass, string $method, array $params = [])
            {
                /** @var MessagingSystem $messagingSystem */
                $messagingSystem = $this->container->get(IntegrationMessagingBundle::MESSAGING_SYSTEM_SERVICE_NAME);

                return call_user_func_array([$messagingSystem->getGatewayByName($this->referenceName), $method], $params);
            }
        });

        return $factory->createProxy($interface);
    }
}