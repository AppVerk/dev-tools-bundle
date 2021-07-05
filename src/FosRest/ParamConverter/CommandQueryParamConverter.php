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
use Symfony\Component\Serializer\Exception\ExceptionInterface as SymfonySerializerException;

class CommandQueryParamConverter implements ParamConverterInterface
{
    private const SUPPORTED_ARGUMENTS = ['command', 'query'];

    private const SUPPORTED_NAMES = ['', 'command_query'];

    private SymfonySerializerAdapter $serializer;

    private array $context = [];

    private DataNormalizer $dataNormalizer;

    private DataExtractor $dataExtractor;

    public function __construct(
        SymfonySerializerAdapter $serializer,
        DataNormalizer $dataNormalizer,
        DataExtractor $dataExtractor,
        array $groups = null,
        string $version = null
    ) {
        $this->serializer = $serializer;

        if (!empty($groups)) {
            $this->context['groups'] = $groups;
        }

        if (!empty($version)) {
            $this->context['version'] = $version;
        }

        $this->dataNormalizer = $dataNormalizer;
        $this->dataExtractor = $dataExtractor;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, ParamConverter $configuration): bool
    {
        $options = $configuration->getOptions();

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
        $rawData = $this->dataExtractor->extract($request, $format, $context, $class, $options);

        $normalizedData = $this->dataNormalizer->normalize($rawData, $class);

        try {
            $object = $this->serializer->denormalize($normalizedData, $class, $format, $context);
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
}
