<?php

declare(strict_types = 1);

namespace DevTools\FosRest\Serializer;

use FOS\RestBundle\Serializer\Normalizer\FlattenExceptionNormalizer as DecoratedFlattenExceptionNormalizer;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;

class FlattenExceptionNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @var DecoratedFlattenExceptionNormalizer
     */
    private $decoratedNormalizer;

    private NameConverterInterface $nameConverter;

    public function __construct(
        DecoratedFlattenExceptionNormalizer $decoratedHandler,
        NameConverterInterface $nameConverter
    ) {
        $this->decoratedNormalizer = $decoratedHandler;
        $this->nameConverter = $nameConverter;
    }

    /**
     * {@inheritdoc}
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $result = (array) $this->decoratedNormalizer->normalize($object, $format, $context);

        $exception = $context['exception'] ?? null;

        if (!$exception instanceof ValidationFailedException) {
            return $result;
        }

        $errors = [];
        /** @var ConstraintViolation $item */
        foreach ($exception->getViolations() as $item) {
            $property = $this->nameConverter->normalize($item->getPropertyPath());
            $errors[$property][] = $item->getMessage();
        }

        return array_merge($result, [
            'message' => 'Validation failed.',
            'errors' => $errors,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format)
            && empty($context[Serializer::MESSENGER_SERIALIZATION_CONTEXT]);
    }
}
