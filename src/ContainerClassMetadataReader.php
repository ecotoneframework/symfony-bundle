<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\Reader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\ClassMetadataReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\DoctrineClassMetadataReader;
use SimplyCodedSoftware\IntegrationMessaging\Config\ConfigurationException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ContainerClassMetadataReader
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ContainerClassMetadataReader implements ClassMetadataReader
{
    /**
     * @var Container
     */
    private $container;
    /**
     * @var Reader
     */
    private $reader;

    /**
     * ContainerClassMetadataReader constructor.
     *
     * @param Container                          $container
     * @param Reader|DoctrineClassMetadataReader $reader
     */
    public function __construct(Container $container, DoctrineClassMetadataReader $reader)
    {
        $this->container = $container;
        $this->reader = $reader;
    }

    /**
     * @inheritDoc
     */
    public function getMethodsWithAnnotation(string $className, string $annotationName): array
    {
        $key = "meta-data-" . str_replace("\\", ".", strtolower($className));

        if ($this->container->hasParameter($key)) {
            return $this->container->getParameter($key);
        }

        $methods = $this->reader->getMethodsWithAnnotation($className, $annotationName);
        $this->container->setParameter($key, $methods);

        return $methods;
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationForMethod(string $className, string $methodName, string $annotationName)
    {
        return $this->reader->getAnnotationForMethod($className, $methodName, $annotationName);
    }

    /**
     * @inheritDoc
     */
    public function getAnnotationForClass(string $className, string $annotationName)
    {
        return $this->reader->getAnnotationForClass($className, $annotationName);
    }
}