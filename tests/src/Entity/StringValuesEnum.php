<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Dayploy\DartDtoBundle\Attributes\DartDto;

#[DartDto]
enum StringValuesEnum: string
{
    case none = 'none';
    case warning = 'warning';
    case error = 'error';
}
