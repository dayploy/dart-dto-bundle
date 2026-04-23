<?php

namespace Dayploy\DartDtoBundle\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DartDto
{
    public function __construct(
        private bool $generateToJson = false,
    ) {
    }

    public function getGenerateToJson(
    ): bool {
        return $this->generateToJson;
    }
}
