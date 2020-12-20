<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;


use Ecotone\Messaging\Handler\ReferenceSearchService;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyReferenceSearchService implements ReferenceSearchService
{
    public function __construct(private ContainerInterface $container){}

    public function get(string $reference) : object
    {
        return $this->container->get($reference . '-proxy');
    }

    public function has(string $referenceName): bool
    {
        return $this->container->has($referenceName . '-proxy');
    }
}