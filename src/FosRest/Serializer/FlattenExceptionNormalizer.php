<?php

declare(strict_types = 1);

namespace DevTools\FosRest\Serializer;

use FOS\RestBundle\Serializer\Normalizer\FlattenExceptionNormalizer as DecoratedFlattenExceptionNormalizer;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;

class FlattenExceptionNormalizer implements NormalizerInterface
{
    /**
     * @var DecoratedFlattenExceptionNormalizer
     */
    private $decoratedNormalizer;

    public function __construct(DecoratedFlattenExceptionNormalizer $decoratedHandler)
    {
        $this->decoratedNormalizer = $decoratedHandler;
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
            $errors[$item->getPropertyPath()][] = $item->getMessage();
        }

        return array_merge($result, [
            'message' => 'Validation failed.',
            'errors' => $errors,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, string $format = null)
    {
        return $this->decoratedNormalizer->supportsNormalization($data, $format);
    }
}
