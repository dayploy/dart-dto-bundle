<?php

namespace Dayploy\DartDtoBundle\Generator;

use Symfony\Component\TypeInfo\Type;

class ConstructorTypeGenerator
{
    private static string $template = '    <required>this.<fieldName>,';

    public function __construct(
        private TypeConverter $typeConverter,
    ) {
    }

    public function generate(
        string $fieldName,
        Type $type,
    ): string {
        $requiredLabel = $type->isNullable() ? '': 'required ';
        $replacements = [
            '<fieldName>' => $fieldName,
            '<required>' => $requiredLabel,
        ];

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            static::$template
        );

        return $method;
    }
}
