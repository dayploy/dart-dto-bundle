<?php

namespace Dayploy\DartDtoBundle\Tests\Attributes;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Dayploy\DartDtoBundle\Attributes\AnnotationCollectionFactory;
use Dayploy\DartDtoBundle\Attributes\DartDto;
use Dayploy\DartDtoBundle\Tests\src\Entity\MyClass;

class AnnotationCollectionFactoryTest extends KernelTestCase
{
    public function testCreate(): void
    {
        $factoryAnnotation = new AnnotationCollectionFactory(['./tests/src']);
        $classes = $factoryAnnotation->create();

        $this->assertCount(5, $classes);
        $this->assertArrayHasKey(MyClass::class, $classes);
        $firstClass = $classes[MyClass::class]['class'];

        $this->assertCount(11, $firstClass->getProperties());
        $this->assertCount(1, $firstClass->getAttributes());
        $this->assertSame(DartDto::class, $firstClass->getAttributes()[0]->getName());
    }
}
