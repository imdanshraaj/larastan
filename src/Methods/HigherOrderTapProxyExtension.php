<?php

declare(strict_types=1);

namespace Larastan\Larastan\Methods;

use Illuminate\Support\HigherOrderTapProxy;
use PHPStan\Analyser\OutOfClassScope;
use PHPStan\Reflection\ClassReflection;
use PHPStan\Reflection\MethodReflection;
use PHPStan\Reflection\MethodsClassReflectionExtension;
use PHPStan\ShouldNotHappenException;

final class HigherOrderTapProxyExtension implements MethodsClassReflectionExtension
{
    public function hasMethod(ClassReflection $classReflection, string $methodName): bool
    {
        if ($classReflection->getName() !== HigherOrderTapProxy::class) {
            return false;
        }

        $templateTypeMap = $classReflection->getActiveTemplateTypeMap();

        $templateType = $templateTypeMap->getType('TClass');

        if ($templateType === null || $templateType->getObjectClassReflections() === []) {
            return false;
        }

        return $templateType->hasMethod($methodName)->yes();
    }

    public function getMethod(
        ClassReflection $classReflection,
        string $methodName,
    ): MethodReflection {
        $templateType = $classReflection->getActiveTemplateTypeMap()->getType('TClass');

        if ($templateType === null || $templateType->getObjectClassReflections() === [] || ! $templateType->hasMethod($methodName)->yes()) {
            throw new ShouldNotHappenException();
        }

        return $templateType->getMethod($methodName, new OutOfClassScope());
    }
}
