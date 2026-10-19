<?php

namespace Dayploy\DartDtoBundle\Tests\Generator;

use Dayploy\DartDtoBundle\Generator\ToJsonTypeConverter;
use Dayploy\DartDtoBundle\Tests\AbstractTestCase;
use Dayploy\DartDtoBundle\Tests\src\Entity\ForeignClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\TypeIdentifier;
use Symfony\Component\Uid\Uuid;

/**
 * The expression written in `toJson()`, type by type.
 *
 * GeneratorTest compares whole files to a golden copy, which only proves that the output did not
 * change. It cannot tell a correct output from a wrong one: the expected copy of ForeignClass
 * carried `myClass.toJson()?` for a while, so the suite asserted invalid Dart instead of catching
 * it. The cases below state what each type must produce, so a wrong expression fails here even if
 * someone refreshes the golden files.
 */
class ToJsonTypeConverterTest extends AbstractTestCase
{
    #[DataProvider('provideTypes')]
    public function testConvertType(Type $type, string $expected): void
    {
        $container = self::getContainer();

        /** @var ToJsonTypeConverter $service */
        $service = $container->get(ToJsonTypeConverter::class);

        $this->assertSame($expected, $service->convertType('slot', $type));
    }

    /**
     * @return iterable<string, array{Type, string}>
     */
    public static function provideTypes(): iterable
    {
        $childDto = new ObjectType(ForeignClass::class);
        $uuid = new ObjectType(Uuid::class);
        $string = new BuiltinType(TypeIdentifier::STRING);
        $file = new ObjectType(File::class);

        yield 'child dto' => [$childDto, 'slot.toJson()'];

        // the null aware operator belongs to the receiver: `slot.toJson()?` does not compile
        yield 'nullable child dto' => [new NullableType($childDto), 'slot?.toJson()'];

        yield 'uuid' => [$uuid, 'slot.toString()'];
        yield 'nullable uuid' => [new NullableType($uuid), 'slot?.toString()'];

        // a file is a string on the Dart side: it goes out as is, nullable or not
        yield 'file' => [$file, 'slot'];
        yield 'nullable file' => [new NullableType($file), 'slot'];

        yield 'string' => [$string, 'slot'];
        yield 'nullable string' => [new NullableType($string), 'slot'];
    }
}
