<?php

namespace App\Rules\FileName;

use Closure;
use App\Models\Node;
use App\Rules\FileNameAbstractRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class FileNameNotExistsInDirectory extends FileNameAbstractRule
{

    /**
     * Run the validation rule.
     *
     * @param Closure(string): PotentiallyTranslatedString $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $targetNode = $this->getNode($this->parent);
        if ($targetNode && $targetNode->type == 'directory') {
            $elementsWithSameFileName = $targetNode->children->filter(function (Node $node) use ($value) {
                return $node->name == $value;
            });
            if (!$elementsWithSameFileName->count()) return;
        }
        $fail('File with same file name already exists in given directory');
    }

}
