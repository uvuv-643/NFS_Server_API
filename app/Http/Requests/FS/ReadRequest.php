<?php

namespace App\Http\Requests\FS;

use App\Http\Requests\FSAbstractRequest;
use App\Rules\Inode\InodeIsFile;
use App\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * @property $token string
 * @property $inode integer
 */
class ReadRequest extends FSAbstractRequest
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
                'integer',
                new InodeIssetRule($this->token),
                new InodeIsFile($this->token),
            ],
        ];
    }
}
