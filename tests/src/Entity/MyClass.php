<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Dayploy\DartDtoBundle\Attributes\DartDto;
use Dayploy\DartDtoBundle\Attributes\DartDtoIgnore;

#[DartDto]
class MyClass
{
    private Uuid $id;

    private int $numberInt;
    private float $numberFloat;
    private \DateTimeImmutable $maDate;
    private string $name;
    private ?string $nullableString;

    /**
     * @var Collection<ForeignClass>
     */
    private Collection $foreignClasses;

    /**
     * @var array<int>
     */
    private array $references;

    private IntValuesEnum $intEnum;
    private StringValuesEnum $stringEnum;

    #[DartDtoIgnore]
    private string $propertyToIgnore;
}
