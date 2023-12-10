<?php

namespace App\Http\Requests\FS;

use app\Rules\Inode\DirectoryIsNotFull;
use app\Rules\Inode\InodeIsDirectory;
use app\Rules\Inode\InodeIssetRule;
use FileNameNotExistsInDirectory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * @property $token string
 * @property $parent integer
 * @property $name string
 * @property $type string
 */
class CreateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

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
