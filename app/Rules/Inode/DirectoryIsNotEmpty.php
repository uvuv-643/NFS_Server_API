<?php

namespace app\Rules\Inode;

use App\Rules\InodeAbstractRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

class DirectoryIsNotEmpty extends InodeAbstractRule
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
