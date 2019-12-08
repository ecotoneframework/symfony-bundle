<?php

namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;

use Ecotone\Messaging\Config\ReferenceTypeFromNameResolver;
use Ecotone\Messaging\Handler\TypeDescriptor;
use Psr\Container\ContainerInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SymfonyReferenceTypeResolver
 * @package Ecotone\SymfonyBundle
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class SymfonyReferenceTypeResolver implements ReferenceTypeFromNameResolver
{
    /**
     * @var ContainerBuilder|ContainerInterface
     */
    private $container;

    /**
     * SymfonyReferenceTypeResolver constructor.
     * @param $container
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * @param string $referenceName
     * @return TypeDescriptor
     * @throws \Ecotone\Messaging\Handler\TypeDefinitionException
     * @throws \Ecotone\Messaging\MessagingException
     */
    public function resolve(string $referenceName): TypeDescriptor
    {
        if ($this->container instanceof ContainerBuilder) {
            return TypeDescriptor::create($this->container->getDefinition($referenceName)->getClass());
        }else {
            return TypeDescriptor::create(get_class($this->container->get($referenceName)));
        }
    }
}