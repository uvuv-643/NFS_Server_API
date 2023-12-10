<?php


use App\Models\Node;
use App\Rules\FileNameAbstractRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class FileNameInDirectoryIsDirectory extends FileNameAbstractRule
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
                return $node->type == 'file' && $node->name == $value;
            });
            if ($elementsWithSameFileName->count()) {
                $targetElement = $elementsWithSameFileName->first();
                if ($targetElement->type == 'directory') return;
            }
        }
        $fail('File with given file name is not directory');
    }

}
