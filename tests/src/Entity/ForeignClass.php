<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Symfony\Component\Uid\Uuid;
use Dayploy\DartDtoBundle\Attributes\DartDto;

#[DartDto(generateToJson: true)]
class ForeignClass
{
    private Uuid $id;
    private ?MyClass $myClass;
}
