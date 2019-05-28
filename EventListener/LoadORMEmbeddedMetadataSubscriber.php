<?php
/**
 * This file is part of the YAP package
 *
 * (c) Sébastien COTON <sebastien.coton@gmail.com>
 * (c) Ouat's UP
 * (c) SAS Chaucodel
 *
 * L'usage et la diffusion de ce fichier ainsi que de l'ensemble des fichiers
 * liés au présent projet doit faire l'objet d'un accord express
 * de l'ensemble des parties citées dans le (c) ci-dessus
 *
 */

namespace Joschi127\DoctrineEntityOverrideBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadORMEmbeddedMetadataSubscriber implements EventSubscriber {

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $overriddenEntities;

    /**
     * @var array
     */
    protected $parentClassesByClass = [];

    /**
     * Constructor
     *
     * @param array $overriddenEntities
     */
    public function __construct(ContainerInterface $container, array $overriddenEntities)
    {
        $this->container = $container;
        $this->overriddenEntities = $overriddenEntities;
        foreach ($overriddenEntities as $interface => $class) {
            $class = $this->getClass($class);
            $this->parentClassesByClass[$class] = array_values(class_parents($class));
        }
    }

    /**
     * @return array
     */
    public function getSubscribedEvents()
    {
        return array(
            'loadClassMetadata'
        );
    }

    /**
     * @param LoadClassMetadataEventArgs $eventArgs
     */
    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $em = $eventArgs->getEntityManager();

//        if (false === $eventArgs->getClassMetadata()->isEmbeddedClass)
//            return ;

        $metadata = $eventArgs->getClassMetadata() ;

        foreach($metadata->embeddedClasses as $propertyName => $embeddedMapping) {

            $overridingClass = $this->getOverridingClass($embeddedMapping['class']);

            if (!$overridingClass)
                continue ;

            $targetMetadata = $eventArgs->getEntityManager()->getClassMetadata($overridingClass);

            // Removing previous mappings

            foreach($metadata->fieldMappings as $name => $mapping) {

                if (!isset($mapping['declaredField']) || $mapping['declaredField']!=$propertyName)
                    continue;

                unset($metadata->fieldMappings[$mapping['fieldName']]);
                unset($metadata->columnNames[$mapping['fieldName']]);
                unset($metadata->fieldNames[$mapping['columnName']]);
            }

            // Put the new mappings

            $metadata->inlineEmbeddable($propertyName,$targetMetadata);
        }


    }




    protected function getOverridingClass($className)
    {
        foreach ($this->overriddenEntities as $interface => $class) {
            $interface = $this->getInterface($interface);
            $class = $this->getClass($class);

            if ($interface === $className) {
                return $class;
            }

            foreach($this->parentClassesByClass[$class] as $parentClass) {
                if ($parentClass === $className) {
                    return $class;
                }
            }
        }

        return null;
    }

    /**
     * @param string           $key
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getInterface($key)
    {
        if ($this->container->hasParameter($key)) {
            return $this->container->getParameter($key);
        }

        if (interface_exists($key) || class_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(
            sprintf('The interface or class %s does not exists.', $key)
        );
    }

    /**
     * @param string           $key
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    protected function getClass($key)
    {
        if ($this->container->hasParameter($key)) {
            return $this->container->getParameter($key);
        }

        if (class_exists($key)) {
            return $key;
        }

        throw new \InvalidArgumentException(
            sprintf('The class %s does not exists.', $key)
        );
    }
}
