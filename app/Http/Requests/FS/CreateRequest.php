<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use App\Rules\Inode\DirectoryIsNotFull;
use App\Rules\Inode\InodeIsDirectory;
use App\Rules\Inode\InodeIssetRule;
use App\Rules\FileName\FileNameNotExistsInDirectory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\Rule;

/**
 * @property $token string
 * @property $parent integer
 * @property $name string
 * @property $type string
 */
class CreateRequest extends FSAbstractRequest
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
                new DirectoryIsNotFull($this->token),
            ],
            'name' => [
                'required',
                new FileNameNotExistsInDirectory($this->token, $this->parent),
            ],
            'type' => [
                'required',
                Rule::in(['directory', 'file']),
            ]
        ];
    }

}
