<?php

namespace App\Rules\Inode;

use Closure;
use App\Rules\InodeAbstractRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DirectoryIsNotFull extends InodeAbstractRule
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
            if ($targetNode->children->count() < config('nfs.file_cnt_limit')) return;
        }
        $fail('Directory is full (max 16 files)');
    }

}
