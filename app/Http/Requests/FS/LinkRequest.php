<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use App\Rules\Inode\DirectoryIsNotFull;
use App\Rules\Inode\InodeIsDirectory;
use App\Rules\Inode\InodeIsFile;
use App\Rules\Inode\InodeIssetRule;
use App\Rules\FileName\FileNameNotExistsInDirectory;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @property $token string
 * @property $source integer
 * @property $parent integer
 * @property $name string
 */
class LinkRequest extends FSAbstractRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'source' => [
                'required',
                'integer',
                new InodeIssetRule($this->token),
                new InodeIsFile($this->token),
            ],
            'parent' => [
                'required',
                'integer',
                new InodeIssetRule($this->token),
                new InodeIsDirectory($this->token),
                new DirectoryIsNotFull($this->token),
            ],
            'name' => [
                'required',
                'max:255',
                new FileNameNotExistsInDirectory($this->token, $this->parent),
            ]
        ];
    }

}
