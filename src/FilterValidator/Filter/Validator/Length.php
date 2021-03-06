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

namespace ApiPlatformFilterValidator\ApiPlatform\Core\Filter\Validator;

use Symfony\Component\HttpFoundation\Request;

class Length implements ValidatorInterface
{
    public function validate(string $name, array $filterDescription, Request $request): array
    {
        $maxLength = $filterDescription['swagger']['maxLength'] ?? null;
        $minLength = $filterDescription['swagger']['minLength'] ?? null;

        $value = $request->query->get($name);
        if (empty($value) && '0' !== $value || !\is_string($value)) {
            return [];
        }

        $errorList = [];

        if (null !== $maxLength && mb_strlen($value) > $maxLength) {
            $errorList[] = \sprintf('Query parameter "%s" length must be lower than or equal to %s', $name, $maxLength);
        }

        if (null !== $minLength && mb_strlen($value) < $minLength) {
            $errorList[] = \sprintf('Query parameter "%s" length must be greater than or equal to %s', $name, $minLength);
        }

        return $errorList;
    }
}
