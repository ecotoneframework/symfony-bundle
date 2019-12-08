<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;


use Ecotone\Messaging\Handler\ReferenceSearchService;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SymfonyReferenceSearchService implements ReferenceSearchService
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     *  constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function get(string $reference) : object
    {
        return $this->container->get($reference . '-proxy');
    }

    public function has(string $referenceName): bool
    {
        return $this->container->has($referenceName);
    }
}