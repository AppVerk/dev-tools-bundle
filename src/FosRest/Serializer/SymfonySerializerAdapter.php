<?php

declare(strict_types = 1);

namespace DevTools\FosRest\Serializer;

use FOS\RestBundle\Context\Context;
use Symfony\Component\Serializer\Serializer;

class SymfonySerializerAdapter
{
    private Serializer $serializer;

    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param mixed $data
     */
    public function serialize($data, string $format, Context $context): string
    {
        $newContext = $this->convertContext($context);

        return $this->serializer->serialize($data, $format, $newContext);
    }

    /**
     * @return mixed
     */
    public function deserialize(string $data, string $type, string $format, Context $context)
    {
        $newContext = $this->convertContext($context);

        return $this->serializer->deserialize($data, $type, $format, $newContext);
    }

    /**
     * @param mixed $data
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @return mixed
     */
    public function normalize($data, string $format, Context $context)
    {
        $newContext = $this->convertContext($context);

        return $this->serializer->normalize($data, $format, $newContext);
    }

    /**
     * @param mixed $data
     *
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     *
     * @return mixed
     */
    public function denormalize($data, string $type, string $format, Context $context)
    {
        $newContext = $this->convertContext($context);

        return $this->serializer->denormalize($data, $type, $format, $newContext);
    }

    /**
     * @return mixed
     */
    public function decode(string $data, string $format, Context $context)
    {
        $newContext = $this->convertContext($context);

        return $this->serializer->decode($data, $format, $newContext);
    }

    protected function convertContext(Context $context): array
    {
        $newContext = [];
        foreach ($context->getAttributes() as $key => $value) {
            $newContext[$key] = $value;
        }

        if (null !== $context->getGroups()) {
            $newContext['groups'] = $context->getGroups();
        }

        $newContext['version'] = $context->getVersion();
        $newContext['enable_max_depth'] = $context->isMaxDepthEnabled();
        $newContext['skip_null_values'] = !$context->getSerializeNull();

        return $newContext;
    }
}
