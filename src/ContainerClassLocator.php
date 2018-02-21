<?php

namespace SimplyCodedSoftware\IntegrationMessaging\Symfony;

use Doctrine\Common\Annotations\Reader;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\ClassLocator;
use SimplyCodedSoftware\IntegrationMessaging\Config\Annotation\FileSystemClassLocator;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class ContainerClassLocator
 * @package SimplyCodedSoftware\IntegrationMessaging\Symfony
 * @author  Dariusz Gafka <dgafka.mail@gmail.com>
 */
class ContainerClassLocator implements ClassLocator
{
    private const ALL_CLASSES = "messaging-system-all-classes";

    /**
     * @var Container
     */
    private $container;
    /**
     * @var FileSystemClassLocator
     */
    private $fileSystemClassLocator;
    /**
     * @var Reader
     */
    private $annotationReader;

    /**
     * ContainerClassLocator constructor.
     *
     * @param Container $container
     * @param Reader    $annotationReader
     *
     */
    public function __construct(Container $container, Reader $annotationReader)
    {
        $this->container        = $container;
        $this->annotationReader = $annotationReader;
    }

    /**
     * @inheritDoc
     */
    public function getAllClasses(): array
    {
        if ($this->container->hasParameter(self::ALL_CLASSES)) {
            return $this->container->getParameter(self::ALL_CLASSES);
        }

        $allClasses = $this->fileSystemClassLocator()->getAllClasses();

        $this->container->setParameter(self::ALL_CLASSES, $allClasses);

        return $allClasses;
    }

    /**
     * @return FileSystemClassLocator
     */
    private function fileSystemClassLocator(): FileSystemClassLocator
    {
        if (!$this->fileSystemClassLocator) {
            $namespaces = array_merge([
                FileSystemClassLocator::SIMPLY_CODED_SOFTWARE_NAMESPACE,
                FileSystemClassLocator::INTEGRATION_MESSAGING_NAMESPACE
            ], $this->container->getParameter('messaging.application.context.namespace'));

            $this->fileSystemClassLocator = new FileSystemClassLocator(
                $this->annotationReader,
                [
                    realpath($this->container->getParameter('kernel.root_dir') . "/.."),
                    realpath($this->container->getParameter('kernel.root_dir') . DIRECTORY_SEPARATOR . "../vendor")
                ],
                $namespaces
            );
        }

        return $this->fileSystemClassLocator;
    }

    /**
     * @inheritDoc
     */
    public function getAllClassesWithAnnotation(string $annotationName): array
    {
        $key = "class-locator-" . str_replace("\\", ".", strtolower($annotationName));

        if ($this->container->hasParameter($key)) {
            return $this->container->getParameter($key);
        }

        $allClassesWithAnnotation = $this->fileSystemClassLocator()->getAllClassesWithAnnotation($annotationName);

        $this->container->setParameter($key, $allClassesWithAnnotation);

        return $allClassesWithAnnotation;
    }
}