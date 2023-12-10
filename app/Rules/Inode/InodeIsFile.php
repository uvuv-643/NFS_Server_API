<?php

namespace App\Rules\Inode;

use App\Rules\InodeAbstractRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

class InodeIsFile extends InodeAbstractRule
{

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetNode = $this->getNode($value);
        if ($targetNode && $targetNode->type == 'file') return;
        $fail('Inode must be a directory');
    }

}
