<?php

namespace Dayploy\DartDtoBundle\Tests\Generator;

use Dayploy\DartDtoBundle\Generator\FilenameService;
use Dayploy\DartDtoBundle\Tests\AbstractTestCase;

class FilenameServiceTest extends AbstractTestCase
{
    public function testGetObjectFromClassname(): void
    {
        $container = self::getContainer();

        /** @var FilenameService */
        $service = $container->get(FilenameService::class);

        $this->assertSame('SomeString', $service->getObjectFromClassname('SomeString'));
        $this->assertSame('Class', $service->getObjectFromClassname('Path\To\Class'));

        $this->assertSame([
            'SomeString' => '/model/some_string.dart',
            'Class' => '/model/path/to/class.dart',
        ], $service->getImports());
    }
}
