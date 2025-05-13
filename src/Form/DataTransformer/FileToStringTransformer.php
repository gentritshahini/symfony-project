<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\HttpFoundation\File\File;

class FileToStringTransformer implements DataTransformerInterface
{
    private string $uploadDir;

    public function __construct(string $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function transform($value): mixed
    {
        if (!$value) {
            return null;
        }

        return new File($this->uploadDir . '/' . $value);
    }

    public function reverseTransform($value): mixed
    {
        if (!$value) {
            return null;
        }

        if ($value instanceof File) {
            return $value;
        }

        return $value;
    }
}