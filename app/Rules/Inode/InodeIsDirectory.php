<?php

namespace app\Rules\Inode;

use App\Rules\InodeAbstractRule;
use Closure;
use Illuminate\Translation\PotentiallyTranslatedString;

class InodeIsDirectory extends InodeAbstractRule
{

    /**
     * Run the validation rule
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetNode = $this->getNode($value);
        if ($targetNode && $targetNode->type == 'directory') return;
        $fail('Inode must be a directory');
    }

}
