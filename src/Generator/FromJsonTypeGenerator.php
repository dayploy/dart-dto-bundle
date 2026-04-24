<?php

namespace Dayploy\DartDtoBundle\Generator;

use Symfony\Component\TypeInfo\Type;

class FromJsonTypeGenerator
{
    private static string $template = '
    if (json.containsKey(\'<fieldName>\')) {
      entity.<fieldName> = <type>;
    }';

    public function __construct(
        private FromJsonTypeConverter $typeConverter,
    ) {
    }

    public function generate(
        string $fieldName,
        Type $type,
    ): string {
        $replacements = [
            '<type>' => $this->typeConverter->convertType(
                type: $type,
                fieldName: $fieldName,
            ),
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
