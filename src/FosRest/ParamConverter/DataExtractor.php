<?php

declare(strict_types = 1);

namespace DevTools\FosRest\ParamConverter;

use DevTools\FosRest\Serializer\SymfonySerializerAdapter;
use FOS\RestBundle\Context\Context;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DataExtractor
{
    private const DEFAULT_USER_LOGGED_FIELD = 'userId';

    private SymfonySerializerAdapter $serializer;

    private TokenStorageInterface $tokenStorage;

    public function __construct(SymfonySerializerAdapter $serializer, TokenStorageInterface $tokenStorage)
    {
        $this->serializer = $serializer;
        $this->tokenStorage = $tokenStorage;
    }

    public function extract(Request $request, string $format, Context $context, string $class, array $options): array
    {
        $content = $request->getContent();

        $baseData = Request::METHOD_GET === $request->getMethod()
            ? $request->query->all()
            : ('' === $content ? [] : $this->serializer->decode($content, $format, $context));

        $attributesMap = $options['map'] ?? [];
        $loggedField = $options['loggedUserField'] ?? self::DEFAULT_USER_LOGGED_FIELD;

        return $this->enrichDataFromRequestAttributes($class, $baseData, $request, $attributesMap, $loggedField);
    }

    protected function enrichDataFromRequestAttributes(
        string $class,
        array $baseData,
        Request $request,
        array $attributesMap,
        string $loggedField
    ): array {
        $reflectionClass = new \ReflectionClass($class);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (isset($baseData[$property->getName()])) {
                continue;
            }

            $value = $request->attributes->get($this->resolveAttributeName($property, $attributesMap));

            if (null !== $value) {
                $baseData[$property->getName()] = $this->normalizeValue($value);

                continue;
            }

            if ($property->getName() === $loggedField) {
                $user = $this->tokenStorage->getToken()->getUser();

                if (!method_exists($user, 'getId')) {
                    continue;
                }

                $baseData[$property->getName()] = $this->normalizeValue($user->getId());
            }
        }

        return $baseData;
    }

    private function resolveAttributeName(\ReflectionProperty $property, array $map): string
    {
        return $map[$property->getName()] ?? $property->getName();
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function normalizeValue($value)
    {
        if (is_scalar($value)) {
            return $value;
        }

        if (is_object($value) && method_exists($value, '__toString')) {
            return (string) $value;
        }

        return $value;
    }
}
