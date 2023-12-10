<?php

namespace App\Http\Requests\FS;

use app\Rules\Inode\DirectoryIsNotFull;
use app\Rules\Inode\InodeIsDirectory;
use app\Rules\Inode\InodeIsFile;
use app\Rules\Inode\InodeIssetRule;
use FileNameNotExistsInDirectory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $token string
 * @property $source integer
 * @property $parent integer
 * @property $name string
 */
class LinkRequest extends FormRequest
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
            'source' => [
                'required',
                new InodeIssetRule($this->token),
                new InodeIsFile($this->token),
            ],
            'parent' => [
                'required',
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
