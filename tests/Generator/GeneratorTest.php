<?php

namespace Dayploy\DartDtoBundle\Tests\Generator;

use Dayploy\DartDtoBundle\Generator\Generator;
use Dayploy\DartDtoBundle\Tests\AbstractTestCase;

class GeneratorTest extends AbstractTestCase
{
    public function testGenerate(): void
    {
        $container = self::getContainer();

        $this->assertTrue($container->has(Generator::class));

        /** @var Generator */
        $service = $container->get(Generator::class);
        $service->generate(['./tests/src']);

        $this->assertGeneratedFile('my_class');
        $this->assertGeneratedFile('foreign_class');
        $this->assertGeneratedFile('autoref_class');
        $this->assertGeneratedFile('int_values_enum');
        $this->assertGeneratedFile('string_values_enum');
    }

    private function assertGeneratedFile(string $filename): void
    {
        $expectedForeignClassTrait = \file_get_contents(__DIR__.'/Expected.'.$filename.'.dart');
        $foreignClassTrait = \file_get_contents(__DIR__.'/../src/entity/'.$filename.'.dart');

        // line kept for dev purpose
        // file_put_contents(__DIR__.'/Expected.'.$filename.'.dart', $foreignClassTrait);

        $this->assertSame($expectedForeignClassTrait, $foreignClassTrait);
    }
}
