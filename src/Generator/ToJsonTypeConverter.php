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

class ToJsonTypeConverter
{
    public function __construct(
        private FilenameService $filenameService,
    ) {
    }

    public function convertType(
        string $fieldName,
        Type $type,
    ): string {
        switch ($type::class) {
            case ObjectType::class:
                /** @var ObjectType $type */
                if ($type->getClassName() === Uuid::class) {
                    return $fieldName.'.toString()';
                }
                if ($type->getClassName() === DateTimeImmutable::class) {
                    $this->filenameService->addApiDateServiceImport();
                    return 'ApiDateService.convertToApi('.$fieldName.')';
                }

                if ($type->getClassName() === UploadedFile::class) {
                    return 'String';
                }
                if ($type->getClassName() === File::class) {
                    return 'String';
                }
                if (is_subclass_of($type->getClassName(), \BackedEnum::class)) {
                    return $fieldName.'.value';
                }

                return $fieldName.'.toJson()';
            case BuiltinType::class:
                /** @var BuiltinType $type */
                if ($type->getTypeIdentifier()->value === 'int') {
                    return $fieldName;
                }
                if ($type->getTypeIdentifier()->value === 'float') {
                    return $fieldName;
                }
                if ($type->getTypeIdentifier()->value === 'array') {
                    throw new \LogicException(sprintf('The array property "%s" is missing a specific type. Please add a PHPDoc to specify its elements (e.g., /** @var MyDto[] */).', $fieldName));
                }
                if ($type->getTypeIdentifier()->value === 'bool') {
                    return $fieldName;
                }
                if ($type->getTypeIdentifier()->value === 'string') {
                    return $fieldName;
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
                return $fieldName.'.value';
            case EnumType::class:
                /** @var EnumType $type */
                return '\\'.$type->getClassName();
            case CollectionType::class:
                /** @var CollectionType $type */
                $valueType = $type->getCollectionValueType();
                if ($valueType && !($valueType instanceof BuiltinType && $valueType->getTypeIdentifier()->value === 'mixed')) {
                    $mappedE = $this->convertType('e', $valueType);
                    return $fieldName.'.map((e) => '.$mappedE.').toList()';
                }

                throw new \LogicException(sprintf('The collection/array property "%s" is missing a specific type. Please add a PHPDoc to specify its elements (e.g., /** @var MyDto[] */).', $fieldName));
            case GenericType::class:
                /** @var GenericType $type */
                return $this->convertType($fieldName, $type->getWrappedType());
            case NullableType::class:
                $wrappedType = $type->getWrappedType();

                if ($wrappedType instanceof ObjectType && $wrappedType->getClassName() === DateTimeImmutable::class) {
                    return $this->convertType($fieldName, $wrappedType);
                }
                if ($wrappedType instanceof ObjectType && $wrappedType->getClassName() === Uuid::class) {
                    return $fieldName.'?.toString()';
                }
                if ($wrappedType instanceof BuiltinType && $wrappedType->getTypeIdentifier()->value === 'string') {
                    return $fieldName;
                }
                if ($wrappedType instanceof BuiltinType && $wrappedType->getTypeIdentifier()->value === 'int') {
                    return $fieldName;
                }
                if ($wrappedType instanceof BackedEnumType) {
                    return $fieldName.'?.value';
                }

                /** @var NullableType $type */
                return $this->convertType($fieldName, $type->getWrappedType()).'?';
        }

        throw new \LogicException('Class '.$type::class.' not handled');
    }
}
