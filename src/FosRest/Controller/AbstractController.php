<?php

declare(strict_types = 1);

namespace DevTools\FosRest\Controller;

use DevTools\Messenger\CommandBus;
use DevTools\Messenger\QueryBus;
use DevTools\Repository\PaginatedResult;
use DevTools\Response\Includes\MapInterface;
use DevTools\Response\Includes\Resolver;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractController extends AbstractFOSRestController
{
    /**
     * {@inheritdoc}
     */
    public static function getSubscribedServices()
    {
        $subscribedServices = parent::getSubscribedServices();

        $subscribedServices[CommandBus::class] = '?' . CommandBus::class;
        $subscribedServices[QueryBus::class] = '?' . QueryBus::class;
        $subscribedServices[Resolver::class] = Resolver::class;

        return $subscribedServices;
    }

    /**
     * @return null|mixed
     */
    protected function dispatchCommand(object $command, bool $forceSyncProcessing = false)
    {
        return $this->getCommandBus()->dispatch($command, $forceSyncProcessing);
    }

    /**
     * @return mixed
     */
    protected function dispatchQuery(object $query)
    {
        return $this->getQueryBus()->dispatch($query);
    }

    /**
     * @param mixed $data
     */
    protected function successResponse($data, MapInterface $includesMap = null, Context $context = null): Response
    {
        $result = ['data' => $data];

        if (null !== $includesMap) {
            $result['included'] = $this->getIncludesResolver()->resolve($data instanceof PaginatedResult ? $data->getItems() : $data, $includesMap);
        }

        $view = $this->view($result, Response::HTTP_OK);

        if ($context) {
            $view->setContext($context);
        }

        return $this->handleView($view);
    }

    protected function emptyResponse(): Response
    {
        $view = $this->view(null, Response::HTTP_NO_CONTENT);

        return $this->handleView($view);
    }

    protected function getCommandBus(): CommandBus
    {
        // @phpstan-ignore-next-line
        return $this->get(CommandBus::class);
    }

    protected function getQueryBus(): QueryBus
    {
        // @phpstan-ignore-next-line
        return $this->get(QueryBus::class);
    }

    protected function getIncludesResolver(): Resolver
    {
        // @phpstan-ignore-next-line
        return $this->get(Resolver::class);
    }
}
