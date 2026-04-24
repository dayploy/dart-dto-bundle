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
                if ($type->getClassName() === Collection::class) {
                    return 'List';
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

                return $this->filenameService->getObjectFromClassname(
                    classname: $type->getClassName(),
                );
            case BuiltinType::class:
                /** @var BuiltinType $type */
                if ($type->getTypeIdentifier()->value === 'int') {
                    return $fieldName;
                }
                if ($type->getTypeIdentifier()->value === 'float') {
                    return $fieldName;
                }
                if ($type->getTypeIdentifier()->value === 'array') {
                    return $fieldName.'.map((e) => e.toJson()).toList()';
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
                return $fieldName.'.name';
            case EnumType::class:
                /** @var EnumType $type */
                return '\\'.$type->getClassName();
            case CollectionType::class:
                /** @var CollectionType $type */
                if ($type->isList()) {
                    return $this->convertType($fieldName, $type->getWrappedType());
                }

                return $this->convertType($fieldName, $type->getWrappedType());
            case GenericType::class:
                /** @var GenericType $type */
                return $this->convertType($fieldName, $type->getWrappedType());
            case NullableType::class:
                $wrappedType = $type->getWrappedType();

                if ($wrappedType instanceof ObjectType && $wrappedType->getClassName() === DateTimeImmutable::class) {
                    return $this->convertType($fieldName, $wrappedType);
                }
                if ($wrappedType instanceof BuiltinType && $wrappedType->getTypeIdentifier()->value === 'string') {
                    return $fieldName;
                }
                if ($wrappedType instanceof BuiltinType && $wrappedType->getTypeIdentifier()->value === 'int') {
                    return $fieldName;
                }

                /** @var NullableType $type */
                return $this->convertType($fieldName, $type->getWrappedType()).'?';
        }

        throw new \LogicException('Class '.$type::class.' not handled');
    }
}
