<?php

declare(strict_types = 1);

namespace DevTools\Messenger\Bridge\Amqp\Routing;

use Symfony\Component\Serializer\NameConverter\NameConverterInterface;

class ClassBasedStrategy implements RoutingStrategyInterface
{
    /**
     * @var NameConverterInterface
     */
    private $nameConverter;

    public function __construct(NameConverterInterface $nameConverter)
    {
        $this->nameConverter = $nameConverter;
    }

    public function getClass(string $routingKey, array $context): ?string
    {
        if (empty($context['class_map'])) {
            return null;
        }

        preg_match('/(.*)\.([^.]*)$/', $routingKey, $matches);

        if (empty($matches[2])) {
            return null;
        }

        $namespace = array_search($matches[1], $context['class_map']);

        if (false === $namespace) {
            return null;
        }

        return $namespace . '\\' . $this->nameConverter->denormalize($matches[2]);
    }

    /**
     * @param class-string $class
     */
    public function getRoutingKey(string $class, array $context): ?string
    {
        if (empty($context['class_map'])) {
            return null;
        }

        $reflection = new \ReflectionClass($class);

        $routingKeyPath = $context['class_map'][$reflection->getNamespaceName()] ?? null;

        if (null === $routingKeyPath) {
            return null;
        }

        $messageName = $this->nameConverter->normalize($reflection->getShortName());

        return $routingKeyPath . '.' . $messageName;
    }
}
