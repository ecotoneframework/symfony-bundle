<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\Reader;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ContainerAnnotationReader
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ContainerAnnotationReader implements Reader
{
    private const CONTAINER_ANNOTATION_CACHE = "container_annotation_cache_";

    /**
     * @var Container
     */
    private $container;
    /**
     * @var Reader
     */
    private $reader;

    /**
     * ContainerAnnotationReader constructor.
     *
     * @param Container $container
     * @param Reader    $reader
     */
    private function __construct(Container $container, Reader $reader)
    {
        $this->container = $container;
        $this->reader = $reader;
    }

    /**
     * @inheritDoc
     */
    function getClassAnnotations(\ReflectionClass $class)
    {
        // TODO: Implement getClassAnnotations() method.
    }

    /**
     * @inheritDoc
     */
    function getClassAnnotation(\ReflectionClass $class, $annotationName)
    {
        // TODO: Implement getClassAnnotation() method.
    }

    /**
     * @inheritDoc
     */
    function getMethodAnnotations(\ReflectionMethod $method)
    {
        // TODO: Implement getMethodAnnotations() method.
    }

    /**
     * @inheritDoc
     */
    function getMethodAnnotation(\ReflectionMethod $method, $annotationName)
    {
        // TODO: Implement getMethodAnnotation() method.
    }

    /**
     * @inheritDoc
     */
    function getPropertyAnnotations(\ReflectionProperty $property)
    {
        // TODO: Implement getPropertyAnnotations() method.
    }

    /**
     * @inheritDoc
     */
    function getPropertyAnnotation(\ReflectionProperty $property, $annotationName)
    {
        // TODO: Implement getPropertyAnnotation() method.
    }
}