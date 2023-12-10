<?php

namespace App\Http\Requests\FS;

use app\Rules\Inode\InodeIsDirectory;
use app\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $token string
 * @property $inode integer
 */
class ListRequest extends FormRequest
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
            'inode' => [
                'required',
                new InodeIssetRule($this->token),
                new InodeIsDirectory($this->token),
            ]
        ];
    }
}
