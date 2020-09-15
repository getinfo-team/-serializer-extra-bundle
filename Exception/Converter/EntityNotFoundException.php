<?php

declare(strict_types=1);

namespace GetInfoTeam\SerializerExtraBundle\Exception\Converter;

use GetInfoTeam\SerializerExtraBundle\Exception\SerializerExtraBundleExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityNotFoundException extends NotFoundHttpException implements SerializerExtraBundleExceptionInterface
{
}