<?php

declare(strict_types = 1);

namespace DevTools\FosRest\ParamConverter;

use DevTools\FosRest\Serializer\SymfonySerializerAdapter;
use FOS\RestBundle\Context\Context;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;

class CommandQueryParamConverter implements ParamConverterInterface
{
    private const SUPPORTED_ARGUMENTS = ['command', 'query'];

    private const SUPPORTED_NAMES = ['', 'command_query'];

    private const DEFAULT_USER_LOGGED_FIELD = 'userId';

    /**
     * @var TokenStorageInterface
     */
    private $token;

    /**
     * @var SymfonySerializerAdapter
     */
    private $serializer;

    /**
     * @var array
     */
    private $context = [];

    public function __construct(
        SymfonySerializerAdapter $serializer,
        TokenStorageInterface $token,
        array $groups = null,
        string $version = null
    ) {
        $this->serializer = $serializer;
        $this->token = $token;

        if (!empty($groups)) {
            $this->context['groups'] = (array) $groups;
        }

        if (!empty($version)) {
            $this->context['version'] = $version;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $options = (array) $configuration->getOptions();

        if (isset($options['deserializationContext']) && is_array($options['deserializationContext'])) {
            $arrayContext = array_merge($this->context, $options['deserializationContext']);
        } else {
            $arrayContext = $this->context;
        }

        $this->configureContext($context = new Context(), $arrayContext);

        $format = $request->getRequestFormat(null);

        if (null === $format) {
            return $this->throwException(new UnsupportedMediaTypeHttpException(), $configuration);
        }

        $class = $configuration->getClass();
        $content = Request::METHOD_GET === $request->getMethod()
            ? $request->query->all()
            : $request->getContent();

        try {
            $object = is_array($content)
                ? $this->serializer->denormalize($content, $class, $format, $context)
                : $this->serializer->deserialize('' === $content ? '{}' : $content, $class, $format, $context);

            if (is_object($object)) {
                $this->setDataFromAttributes(
                    $object,
                    $request,
                    $options['loggedUserField'] ?? self::DEFAULT_USER_LOGGED_FIELD,
                    $options['map'] ?? []
                );
            }
        } catch (SymfonySerializerException $e) {
            return $this->throwException(new BadRequestHttpException($e->getMessage(), $e), $configuration);
        }

        $request->attributes->set($configuration->getName(), $object);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function supports(ParamConverter $configuration): bool
    {
        return null !== $configuration->getClass()
            && in_array($configuration->getConverter(), self::SUPPORTED_NAMES)
            && in_array($configuration->getName(), self::SUPPORTED_ARGUMENTS);
    }

    private function configureContext(Context $context, array $options): void
    {
        foreach ($options as $key => $value) {
            if ('groups' === $key) {
                $context->addGroups($options['groups']);
            } elseif ('version' === $key) {
                $context->setVersion($options['version']);
            } elseif ('enableMaxDepth' === $key) {
                $options['enableMaxDepth'] ? $context->enableMaxDepth() : $context->disableMaxDepth();
            } elseif ('serializeNull' === $key) {
                $context->setSerializeNull($options['serializeNull']);
            } else {
                $context->setAttribute((string) $key, $value);
            }
        }
    }

    private function throwException(\Exception $exception, ParamConverter $configuration): bool
    {
        if ($configuration->isOptional()) {
            return false;
        }

        throw $exception;
    }

    private function setDataFromAttributes(
        object $object,
        Request $request,
        string $loggedField,
        array $attributesMap = []
    ): void {
        $reflectionClass = new \ReflectionClass($object);
        $properties = $reflectionClass->getProperties(\ReflectionProperty::IS_PUBLIC);

        foreach ($properties as $property) {
            if (null !== $property->getValue($object)) {
                continue;
            }

            $value = $request->attributes->get($this->resolveAttributeName($property, $attributesMap));

            if (null !== $value) {
                $property->setValue($object, $this->normalizeValue($value, $property->getType()));

                continue;
            }

            if ($property->getName() === $loggedField) {
                $user = $this->token->getToken()->getUser();

                if (!method_exists($user, 'getId')) {
                    continue;
                }

                $property->setValue($object, $this->normalizeValue($user->getId(), $property->getType()));
            }
        }
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private function normalizeValue($value, ?\ReflectionType $type)
    {
        if (null === $type) {
            return ctype_digit($value) ? (int) $value : $value;
        }

        $typeName = $type->getName();

        if ('int' === $typeName) {
            return (int) $value;
        }

        if ('float' === $typeName) {
            return (float) $value;
        }

        return $value;
    }

    private function resolveAttributeName(\ReflectionProperty $property, array $map): string
    {
        return $map[$property->getName()] ?? $property->getName();
    }
}
