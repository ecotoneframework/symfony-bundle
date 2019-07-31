<?php

namespace Ecotone\Symfony;

use Ecotone\Messaging\Config\ReferenceTypeFromNameResolver;
use Ecotone\Messaging\Handler\TypeDescriptor;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class SymfonyReferenceTypeResolver
 * @package Ecotone\Symfony
 * @author Dariusz Gafka <dgafka.mail@gmail.com>
 */
class SymfonyReferenceTypeResolver implements ReferenceTypeFromNameResolver
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * SymfonyReferenceTypeResolver constructor.
     * @param ContainerBuilder $container
     */
    public function __construct(ContainerBuilder $container)
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
        return TypeDescriptor::create($this->container->getDefinition($referenceName)->getClass());
    }
}