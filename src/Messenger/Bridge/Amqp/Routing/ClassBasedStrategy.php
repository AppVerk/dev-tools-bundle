<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Routing;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ClassBasedStrategy implements RoutingStrategyInterface
{
    private NameConverterInterface $nameConverter;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    public function getClass(string $routingKey, array $context): ?string
    {
        $parts = explode('.', $routingKey);

        if (empty($context['class_map']) || empty($parts)) {
            return null;
        }

        $className = $this->nameConverter->denormalize(end($parts));
        $baseClass = false;

        while (!empty($parts)) {
            $mapKey = implode('.', $parts);
            $baseClass = array_search($mapKey, $context['class_map']);

            if (false === $baseClass) {
                $baseClass = array_search($mapKey . '.', $context['class_map']);
            }

            if (false !== $baseClass) {
                break;
            }

            array_pop($parts);
        }

        if (!is_string($baseClass) || !class_exists($baseClass)) {
            return null;
        }

        $reflection = new \ReflectionClass($baseClass);

        return $reflection->getShortName() === $className
            ? $baseClass
            : $reflection->getNamespaceName() . '\\' . $className;
    }

    /**
     * @param class-string $class
     */
    public function getRoutingKey(string $class, array $context): ?string
    {
        $routingKeyPath = null;

        foreach ($this->listTypes($class) as $type) {
            if (isset($context['class_map'][$type])) {
                $routingKeyPath = $context['class_map'][$type];

                break;
            }
        }

        if (null === $routingKeyPath) {
            return null;
        }

        $reflection = new \ReflectionClass($class);
        $messageName = $this->nameConverter->normalize($reflection->getShortName());

        if ('.' !== mb_substr($routingKeyPath, -1)) {
            $routingKeyPath .= '.';
        }

        return $routingKeyPath . $messageName;
    }

    /**
     * @param class-string $class
     */
    private function listTypes(string $class): array
    {
        return [$class => $class] + class_parents($class) + class_implements($class);
    }
}
