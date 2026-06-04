<?php

namespace Dayploy\DartDtoBundle\Tests\src\Entity;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Uid\Uuid;
use Dayploy\DartDtoBundle\Attributes\DartDto;
use Dayploy\DartDtoBundle\Attributes\DartDtoIgnore;

#[DartDto(generateToJson: true, generateFromJson: true)]
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

    private ForeignClass $singleForeignClass;

    /**
     * @var array<int>
     */
    private array $references;

    private IntValuesEnum $intEnum;
    private StringValuesEnum $stringEnum;

    private ?StringValuesEnum $stringEnumNullable;

    private ?Uuid $uuidNullable;

    /**
     * @var ForeignClass[]
     */
    private array $dtoList;

    /**
     * @var Uuid[]
     */
    private array $uuidList;

    #[DartDtoIgnore]
    private string $propertyToIgnore;
}
