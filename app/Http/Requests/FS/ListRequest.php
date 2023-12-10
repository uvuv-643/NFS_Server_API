<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use App\Rules\Inode\InodeIsDirectory;
use App\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @property $token string
 * @property $inode integer
 */
class ListRequest extends FSAbstractRequest
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
                new InodeIsDirectory($this->token),
            ]
        ];
    }
}
