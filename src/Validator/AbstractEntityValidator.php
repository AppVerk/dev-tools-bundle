<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\ConstraintValidator;

abstract class AbstractEntityValidator extends ConstraintValidator
{
    protected ManagerRegistry $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * @param mixed      $value
     * @param null|mixed $excludeValue
     */
    protected function assertEntityExists(
        ObjectManager $em,
        string $entityClass,
        string $repositoryMethod,
        $value,
        $excludeValue = null
    ): bool {
        $repository = $em->getRepository($entityClass);

        $arguments = $this->normalizeMethodArguments($repository, $repositoryMethod, $value, $excludeValue);

        $result = call_user_func_array([$repository, $repositoryMethod], $arguments);

        return (bool) $result;
    }

    /**
     * @param mixed $value
     */
    protected function formatWithIdentifiers(ObjectManager $em, ClassMetadata $class, $value): string
    {
        if (!\is_object($value) || $value instanceof \DateTimeInterface) {
            return $this->formatValue($value, self::PRETTY_DATE);
        }

        if (method_exists($value, '__toString')) {
            return (string) $value;
        }

        if ($class->getName() !== $idClass = \get_class($value)) {
            // non unique value might be a composite PK that consists of other entity objects
            if ($em->getMetadataFactory()->hasMetadataFor($idClass)) {
                $identifiers = $em->getClassMetadata($idClass)->getIdentifierValues($value);
            } else {
                // this case might happen if the non unique column has a custom doctrine type and its value is an object
                // in which case we cannot get any identifiers for it
                $identifiers = [];
            }
        } else {
            $identifiers = $class->getIdentifierValues($value);
        }

        if (!$identifiers) {
            return sprintf('object("%s")', $idClass);
        }

        array_walk($identifiers, function (&$id, $field): void {
            if (!\is_object($id) || $id instanceof \DateTimeInterface) {
                $idAsString = $this->formatValue($id, self::PRETTY_DATE);
            } else {
                $idAsString = sprintf('object("%s")', \get_class($id));
            }

            $id = sprintf('%s => %s', $field, $idAsString);
        });

        return sprintf('object("%s") identified by (%s)', $idClass, implode(', ', $identifiers));
    }

    /**
     * @param mixed $value
     * @param mixed $excludeValue
     */
    protected function normalizeMethodArguments(
        ObjectRepository $repository,
        string $repositoryMethod,
        $value,
        $excludeValue
    ): array {
        $params = (new \ReflectionMethod($repository, $repositoryMethod))->getParameters();
        $arguments = [$value, $excludeValue];

        foreach ($params as $index => $param) {
            $type = $param->getType();

            if (null === $type || Uuid::class !== $type->getName()) {
                continue;
            }

            if (!is_string($arguments[$index] ?? null)) {
                continue;
            }

            $arguments[$index] = Uuid::fromString($arguments[$index]);
        }

        return $arguments;
    }
}
