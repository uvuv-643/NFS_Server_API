<?php

namespace App\Rules;

use Closure;

/**
 * @property $token string
 * @property $name string
 */
class FileNameAbstractRule extends InodeAbstractRule
{

    public string $token;
    public int $parent;

    public function __construct(string $token, int $parent)
    {
        parent::__construct($token);
        $this->parent = $parent;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

    }

}
