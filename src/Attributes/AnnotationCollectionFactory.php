<?php declare(strict_types=1);

namespace Dayploy\DartDtoBundle\Attributes;

class AnnotationCollectionFactory
{
    public function __construct(private array $paths)
    {
    }

    public function create(): array
    {
        $classes = [];

        foreach (ReflectionClassRecursiveIterator::getReflectionClassesFromDirectories($this->paths) as $className => $reflectionClass) {
            if ($attribute = $reflectionClass->getAttributes(DartDto::class)) {
                $arguments = $attribute[0]->getArguments();
                $generateToJson = false;
                if (key_exists('generateToJson', $arguments)) {
                    $generateToJson = $arguments['generateToJson'];
                }

                $generateFromJson = false;
                if (key_exists('generateFromJson', $arguments)) {
                    $generateFromJson = $arguments['generateFromJson'];
                }

                $classes[$className] = [
                    'class' => $reflectionClass,
                    'generateToJson' => $generateToJson,
                    'generateFromJson' => $generateFromJson,
                ];
            }
        }

        return $classes;
    }
}
