<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use App\Rules\Inode\InodeIsDirectory;
use App\Rules\Inode\InodeIssetRule;
use App\Rules\FileName\FileNameExistsInDirectory;
use App\Rules\FileName\FileNameInDirectoryIsDirectory;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @property $token string
 * @property $parent integer
 * @property $name string
 */
class RmdirRequest extends FSAbstractRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'parent' => [
                'required',
                'integer',
                new InodeIssetRule($this->token),
                new InodeIsDirectory($this->token),
            ],
            'name' => [
                'required',
                'max:255',
                new FileNameExistsInDirectory($this->token, $this->parent),
                new FileNameInDirectoryIsDirectory($this->token, $this->parent),
            ]
        ];
    }
}
