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

    /**
     * No generated expression may carry the null aware operator after a call.
     *
     * `field.toJson()?` is not valid Dart: the question mark belongs to the receiver. The check
     * runs on the generated output itself rather than on the expected copies, because those copies
     * are refreshed from that same output. One of them carried the broken form for a while, which
     * made the suite assert the bug instead of catching it.
     */
    public function testGeneratedFilesNeverPutTheNullAwareOperatorAfterACall(): void
    {
        $container = self::getContainer();

        /** @var Generator */
        $service = $container->get(Generator::class);
        $service->generate(['./tests/src']);

        $files = \glob(__DIR__.'/../src/entity/*.dart');
        $this->assertNotEmpty($files);

        foreach ($files as $file) {
            $generated = \file_get_contents($file);
            $this->assertStringNotContainsString(
                ')?',
                $generated,
                \basename($file).' carries a null aware operator placed after a call, which Dart refuses to compile.',
            );
        }
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
