<?php


namespace Ecotone\SymfonyBundle\DepedencyInjection\Compiler;


use Ecotone\Messaging\Handler\ReferenceSearchService;
use Symfony\Component\DependencyInjection\Container;

class SymfonyReferenceSearchService implements ReferenceSearchService
{
    /**
     * @var Container
     */
    private $container;

    /**
     *  constructor.
     * @param Container $container
     */
    public function __construct(Container $container)
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