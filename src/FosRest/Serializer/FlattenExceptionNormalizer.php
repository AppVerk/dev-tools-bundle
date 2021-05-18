<?php

declare(strict_types = 1);

namespace DevTools\FosRest\Serializer;

use DevTools\Domain\Exception\TranslatableExceptionInterface;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\Messenger\Exception\ValidationFailedException;
use Symfony\Component\Messenger\Transport\Serialization\Serializer;
use Symfony\Component\Serializer\NameConverter\NameConverterInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Contracts\Translation\TranslatorInterface;

class FlattenExceptionNormalizer implements ContextAwareNormalizerInterface
{
    private NormalizerInterface $decoratedNormalizer;

    private NameConverterInterface $nameConverter;

    private TranslatorInterface $translator;

    public function __construct(
        NormalizerInterface $decoratedHandler,
        NameConverterInterface $nameConverter,
        TranslatorInterface $translator
    ) {
        $this->decoratedNormalizer = $decoratedHandler;
        $this->nameConverter = $nameConverter;
        $this->translator = $translator;
    }

    /**
     * @param FlattenException $object
     *
     * @return array
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $exception = $context['exception'] ?? null;

        if ($exception instanceof TranslatableExceptionInterface) {
            $message = $this->translator->trans(
                $exception->getMessageKey(),
                $exception->getMessageData(),
                $exception->getDomain()
            );

            $object->setMessage($message);
        }

        $result = (array) $this->decoratedNormalizer->normalize($object, $format, $context);

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
