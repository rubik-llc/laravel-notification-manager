<?php

namespace Rubik\NotificationManager\Exceptions;

use Exception;
use Facade\IgnitionContracts\BaseSolution;
use Facade\IgnitionContracts\ProvidesSolution;
use Facade\IgnitionContracts\Solution;

class MissingTraitOnNotifiableModel extends Exception implements ProvidesSolution
{
    protected string $modelClass;

    protected string $trait;

    public static function make(string $modelClass, string $trait): self
    {
        return (new static("The trait `{$trait}` was not found on model `{$modelClass}`, are you sure it uses the `{$trait} trait?`"))
            ->setModelClass($modelClass)
            ->setTrait($trait);
    }

    public function setModelClass(string $modelClass): self
    {
        $this->modelClass = $modelClass;

        return $this;
    }

    public function setTrait(string $trait): self
    {
        $this->trait = $trait;

        return $this;
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create('Missing trait on model')
            ->setSolutionDescription("Use the `{$this->trait}` trait on `{$this->modelClass}`");
    }
}
