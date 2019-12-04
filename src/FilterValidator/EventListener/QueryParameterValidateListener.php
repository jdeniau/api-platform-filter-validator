<?php

/*
 * This file is part of the API Platform project.
 *
 * (c) Kévin Dunglas <dunglas@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace ApiPlatformFilterValidator\ApiPlatform\Core\EventListener;

use ApiPlatform\Core\Metadata\Resource\Factory\ResourceMetadataFactoryInterface;
use ApiPlatform\Core\Util\RequestAttributesExtractor;
use ApiPlatformFilterValidator\ApiPlatform\Core\Filter\QueryParameterValidator;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Validates query parameters depending on filter description.
 *
 * @author Julien Deniau <julien.deniau@mapado.com>
 */
final class QueryParameterValidateListener
{
    private $resourceMetadataFactory;

    private $queryParameterValidator;

    public function __construct(ResourceMetadataFactoryInterface $resourceMetadataFactory, QueryParameterValidator $queryParameterValidator)
    {
        $this->resourceMetadataFactory = $resourceMetadataFactory;
        $this->queryParameterValidator = $queryParameterValidator;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (
            !$request->isMethodSafe(false)
            || !($attributes = RequestAttributesExtractor::extractAttributes($request))
            || !isset($attributes['collection_operation_name'])
            || !($operationName = $attributes['collection_operation_name'])
            || $request->getMethod() != 'GET'
        ) {
            return;
        }

        $resourceMetadata = $this->resourceMetadataFactory->create($attributes['resource_class']);
        $resourceFilters = $resourceMetadata->getCollectionOperationAttribute($operationName, 'filters', [], true);

        $this->queryParameterValidator->validateFilters($attributes['resource_class'], $resourceFilters, $request);
    }
}
