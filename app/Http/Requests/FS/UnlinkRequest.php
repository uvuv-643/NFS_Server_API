<?php

namespace App\Http\Requests\FS;

use app\Rules\Inode\InodeIsDirectory;
use app\Rules\Inode\InodeIssetRule;
use FileNameExistsInDirectory;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $token string
 * @property $parent integer
 * @property $name string
 */
class UnlinkRequest extends FormRequest
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
            ],
            'name' => [
                'required',
                'max:255',
                new FileNameExistsInDirectory($this->token, $this->parent),
            ]
        ];
    }
}
