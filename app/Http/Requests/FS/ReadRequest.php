<?php

namespace App\Http\Requests\FS;

use app\Rules\Inode\InodeIsFile;
use app\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

/**
 * @property $token string
 * @property $inode integer
 */
class ReadRequest extends FormRequest
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
                new InodeIsFile($this->token),
            ],
        ];
    }
}
