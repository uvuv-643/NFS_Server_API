<?php

namespace App\Rules\Inode;

use Closure;
use App\Rules\InodeAbstractRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DirectoryIsEmpty extends InodeAbstractRule
{

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetNode = $this->getNode($value);
        if ($targetNode && $targetNode->type == 'directory') {
            if (!$targetNode->children->count()) return;
        }
        $fail('Directory is not empty');
    }

}
