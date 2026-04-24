<?php

namespace Dayploy\DartDtoBundle\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class DartDto
{
    public function __construct(
        private bool $generateToJson = false,
        private bool $generateFromJson = false,
    ) {
    }

    public function getGenerateToJson(
    ): bool {
        return $this->generateToJson;
    }

    public function getGenerateFromJson(
    ): bool {
        return $this->generateFromJson;
    }
}
