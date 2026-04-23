<?php

namespace Dayploy\DartDtoBundle\Generator;

use Symfony\Component\TypeInfo\Type;

class TypeGenerator
{
    private static string $template = '  final <type> <fieldName>;';

    public function __construct(
        private TypeConverter $typeConverter,
    ) {
    }

    public function generate(
        string $fieldName,
        Type $type,
    ): string {
        $replacements = [
            '<type>' => $this->typeConverter->convertType($type),
            '<fieldName>' => $fieldName,
        ];

        $method = str_replace(
            array_keys($replacements),
            array_values($replacements),
            static::$template
        );

        return $method;
    }
}
