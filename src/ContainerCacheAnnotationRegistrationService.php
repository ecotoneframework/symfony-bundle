<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationRegistration;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\AnnotationRegistrationService;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\FileSystemAnnotationRegistrationService;
use SimplyCodedSoftware\IntegrationMessaging\Support\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ContainerCacheAnnotationRegistrationService
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ContainerCacheAnnotationRegistrationService extends FileSystemAnnotationRegistrationService
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function setContainer(Container $container) : void
    {
        $this->container = $container;
    }

    /**
     * @inheritDoc
     */
    public function findRegistrationsFor(string $classAnnotationName, string $methodAnnotationClassName): array
    {
        parent::findRegistrationsFor($classAnnotationName, $methodAnnotationClassName);
    }

    /**
     * @inheritDoc
     */
    public function getAllClassesWithAnnotation(string $annotationClassName): array
    {

    }

    /**
     * @inheritDoc
     */
    public function getAnnotationForClass(string $className, string $annotationClassName)
    {
        // TODO: Implement getAnnotationForClass() method.
    }
}