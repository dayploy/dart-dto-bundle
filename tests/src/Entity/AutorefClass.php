<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Symfony\Component\Uid\Uuid;
use Dayploy\DartDtoBundle\Attributes\DartDto;

#[DartDto]
class AutorefClass
{
    private Uuid $id;
    private ?AutorefClass $autoref;
}
