<?php

declare(strict_types = 1);

namespace DevTools\Validator;

use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\Mapping\ClassMetadata;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PropertyAccess\Exception\NoSuchPropertyException;
use Symfony\Component\PropertyAccess\PropertyAccessorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Exception\ConstraintDefinitionException;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilder;

class UniqueEntityValidator extends AbstractEntityValidator
{
    private PropertyAccessorInterface $propertyAccessor;

    private array $valuesPool = [];

    public function __construct(ManagerRegistry $registry, PropertyAccessorInterface $propertyAccessor)
    {
        parent::__construct($registry);

        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @param mixed $value
     *
     * @throws UnexpectedTypeException
     * @throws ConstraintDefinitionException
     */
    public function validate($value, Constraint $constraint): void
    {
        if (!$constraint instanceof UniqueEntity) {
            throw new UnexpectedTypeException($constraint, UniqueEntity::class);
        }

        $entityClass = $constraint->entityClass;

        if (null === $entityClass) {
            throw new ConstraintDefinitionException('Entity class should be fined.');
        }

        $collectionPath = $constraint->inCollection;

        if (!is_scalar($value)) {
            return;
        }

        if (null === $collectionPath) {
            $em = $this->registry->getManagerForClass($entityClass);
            $class = $em->getClassMetadata($entityClass);

            $this->validateItem($em, $class, $constraint, $value);

            return;
        }

        if (!isset($this->valuesPool[$collectionPath])) {
            $root = $this->context->getRoot();
            $collection = $this->extractValue(is_object($root) ? $root : null, $collectionPath);

            $this->valuesPool[$collectionPath] = [
                'max_count' => is_array($collection) ? count($collection) : 0,
                'items' => [],
            ];
        }

        $pool = &$this->valuesPool[$collectionPath];

        $pool['items'][] = [
            'value' => $value,
            'violation' => $this->context->buildViolation($constraint->message),
        ];

        if ($pool['max_count'] === count($pool['items'])) {
            $em = $this->registry->getManagerForClass($entityClass);
            $class = $em->getClassMetadata($entityClass);

            $this->validateCollection($em, $class, $constraint, $pool['items']);

            unset($this->valuesPool[$collectionPath]);
        }
    }

    /**
     * @return mixed
     */
    private function extractValue(?object $object, ?string $excludeField)
    {
        if (null === $object || null === $excludeField) {
            return null;
        }

        try {
            return $this->propertyAccessor->getValue($object, $excludeField);
        } catch (NoSuchPropertyException $exception) {
            throw new ConstraintDefinitionException(
                sprintf('Invalid excluded field "%s" provided.', $excludeField),
                0,
                $exception
            );
        }
    }

    /**
     * @param mixed $value
     */
    private function validateItem(ObjectManager $em, ClassMetadata $class, UniqueEntity $constraint, $value): void
    {
        $excludeValue = $this->extractValue($this->context->getRoot(), $constraint->excludeField);

        $result = $this->assertItemExists($em, $class->getName(), $constraint->repositoryMethod, $value, $excludeValue);

        if (!$result) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $value))
            ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
            ->setCause($value)
            ->addViolation()
        ;
    }

    /**
     * @param mixed[] $items
     */
    private function validateCollection(ObjectManager $em, ClassMetadata $class, UniqueEntity $constraint, array $items): void
    {
        $result = $this->assertCollectionExist(
            $em,
            $class->getName(),
            $constraint->repositoryMethod,
            array_column($items, 'value')
        );

        foreach ($items as $item) {
            if (!($result[(string) $item['value']] ?? true)) {
                continue;
            }

            /** @var ConstraintViolationBuilder $builder */
            $builder = $item['violation'];

            $builder
                ->setParameter('{{ value }}', $this->formatWithIdentifiers($em, $class, $item))
                ->setCode(UniqueEntity::NOT_UNIQUE_ERROR)
                ->setCause($result)
                ->addViolation()
            ;
        }
    }
}
