<?php

namespace Dayploy\DartDtoBundle\Generator;

use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\TypeInfo\Type;
use Symfony\Component\TypeInfo\Type\BackedEnumType;
use Symfony\Component\TypeInfo\Type\BuiltinType;
use Symfony\Component\TypeInfo\Type\CollectionType;
use Symfony\Component\TypeInfo\Type\EnumType;
use Symfony\Component\TypeInfo\Type\GenericType;
use Symfony\Component\TypeInfo\Type\NullableType;
use Symfony\Component\TypeInfo\Type\ObjectType;
use Symfony\Component\TypeInfo\Type\UnionType;
use Symfony\Component\Uid\Uuid;

class FromJsonTypeConverter
{
    public function __construct(
        private FilenameService $filenameService,
    ) {}

    public function convertType(
        string $fieldName,
        Type $type,
    ): string {
        switch ($type::class) {
            case ObjectType::class:
                /** @var ObjectType $type */
                if ($type->getClassName() === Uuid::class) {
                    $this->filenameService->addUuidImport();
                    return sprintf('UuidValue.fromString(json[\'%s\'] as String)', $fieldName, 'UuidValue');
                }
                if ($type->getClassName() === Collection::class) {
                    return 'List';
                }
                if ($type->getClassName() === DateTimeImmutable::class) {
                    $this->filenameService->addApiDateServiceImport();
                    return sprintf('DateTime.parse(json[\'%s\'] as String)', $fieldName);
                }

                if ($type->getClassName() === UploadedFile::class) {
                    return 'String';
                }
                if ($type->getClassName() === File::class) {
                    return 'String';
                }

                $classname = $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );

                return sprintf('%s.fromJson(json[\'%s\'] as Map<String, dynamic>)', $classname, $fieldName);
            case BuiltinType::class:
                /** @var BuiltinType $type */
                if ($type->getTypeIdentifier()->value === 'int') {
                    return sprintf('json[\'%s\'] as %s', $fieldName, 'int');
                }
                if ($type->getTypeIdentifier()->value === 'float') {
                    return sprintf('json[\'%s\'] as %s', $fieldName, 'double');
                }
                if ($type->getTypeIdentifier()->value === 'array') {
                    return 'List';
                }
                if ($type->getTypeIdentifier()->value === 'bool') {
                    return sprintf('json[\'%s\'] as %s', $fieldName, 'bool');
                }
                if ($type->getTypeIdentifier()->value === 'string') {
                    return sprintf('json[\'%s\'] as %s', $fieldName, 'String');
                }

                return $type->__toString();
            case UnionType::class:
                /** @var UnionType $type */
                $types = $type->getTypes();
                $str = '';
                foreach ($types as $index => $subType) {
                    $str .= $this->convertType($fieldName, $subType);
                    if (($index + 1) < count($types)) {
                        $str .= ' | ';
                    }
                }

                return $str;
            case BackedEnumType::class:
                /** @var BackedEnumType $type */
                $classname =  $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );

                return sprintf('%s.fromValue(json[\'%s\'])', $classname, $fieldName);
            case EnumType::class:
                /** @var EnumType $type */
                return '\\' . $type->getClassName();
            case CollectionType::class:
                /** @var CollectionType $type */
                $wrappedType = $type->getWrappedType();
                $variableType = $wrappedType->getVariableTypes()[1];
                $classname = $this->filenameService->getObjectFromClassname(
                    classname: $variableType->getClassName(),
                );

                return sprintf(
                    '(json[\'%s\'] as List<dynamic>)
            .map((e) => %s.fromJson(e as Map<String, dynamic>))
            .toList()
                            ',
                    $fieldName,
                    $classname
                );

                return $this->convertType($fieldName, $type->getWrappedType());
            case GenericType::class:
                /** @var GenericType $type */
                $variableType = $type->getVariableTypes() ? $type->getVariableTypes()[1] : null;

                return $this->convertType($fieldName, $type->getWrappedType());
            case NullableType::class:
                /** @var NullableType $type */
                $wrappedType = $type->getWrappedType();

                if ($wrappedType instanceof ObjectType && $wrappedType->getClassName() === DateTimeImmutable::class) {
                    return sprintf(
                        'json[\'%s\'] != null
          ? DateTime.tryParse(json[\'%s\'] as String)
          : null',
                        $fieldName,
                        $fieldName,
                        $fieldName
                    );
                }

                return sprintf(
                    'json[\'%s\'] != null ? %s : null',
                    $fieldName,
                    $this->convertType($fieldName, $type->getWrappedType()),
                );
        }

        throw new \LogicException('Class ' . $type::class . ' not handled');
    }
}
