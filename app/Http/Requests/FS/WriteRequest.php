<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use app\Rules\Inode\InodeIsFile;
use app\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @property $token string
 * @property $inode integer
 * @property $content string
 */
class WriteRequest extends FSAbstractRequest
{

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
            'content' => [
                'required', 'max:512'
            ]
        ];
    }
}
