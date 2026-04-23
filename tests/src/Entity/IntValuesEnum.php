<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Dayploy\DartDtoBundle\Attributes\DartDto;

#[DartDto]
enum IntValuesEnum: int
{
    case none = 0;
    case warning = 10;
    case error = 20;
}
