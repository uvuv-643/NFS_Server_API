<?php

namespace App\Http\Requests;

use App\Models\UserToken;
use App\Rules\FileName\FileNameExistsInDirectory;
use App\Rules\FileName\FileNameNotExistsInDirectory;
use App\Rules\Inode\DirectoryIsEmpty;
use App\Rules\Inode\DirectoryIsNotFull;
use App\Rules\Inode\InodeIsDirectory;
use App\Rules\Inode\InodeIsFile;
use App\Rules\Inode\InodeIssetRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;

/**
 * @property $token string
 * @property $user_token_id integer
 */
class FSAbstractRequest extends FormRequest
{

    const RESPONSE_MISSED_REQUIRED = -1;
    const RESPONSE_SUCCESS = 0;
    const RESPONSE_INODE_NOT_FOUND = 1;
    const RESPONSE_INODE_IS_NOT_FILE = 2;
    const RESPONSE_INODE_IS_NOT_DIRECTORY = 3;
    const RESPONSE_DIRECTORY_HAS_NO_FILE = 4;
    const RESPONSE_DIRECTORY_HAS_SUCH_FILE = 5;
    const RESPONSE_FILE_SIZE_LIMIT = 6;
    const RESPONSE_FILE_CNT_LIMIT = 7;
    const RESPONSE_DIRECTORY_IS_NOT_EMPTY = 8;
    const RESPONSE_FILE_NAME_LIMIT = 9;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $token = UserToken::query()->where('token', $this->token)->first();
        return $token != null;
    }

    /**
     * @throws HttpResponseException
     */
    public function failedAuthorization(): JsonResponse
    {
        throw new HttpResponseException(
            response()->json([
                'status' => -1,
                'response' => 'API token is not correct'
            ], 422)
        );
    }

    protected function failValidationWithStatus(int $status, Validator $validator)
    {
        $actualErrors = collect($validator->getMessageBag()->getMessages())
            ->map(function (array $item) {
                return $item[0];
            });

        throw new HttpResponseException(
            response()->json([
                'status' => $status,
                'errors' => $actualErrors
            ], 400)
        );
    }


    protected function failedValidation(Validator $validator): JsonResponse
    {
        $failed = $validator->failed();

        if (isset($failed['parent'][InodeIssetRule::class]) || isset($failed['inode'][InodeIssetRule::class]) || isset($failed['source'][InodeIssetRule::class])) {
            // 1 — Не найден объект по номеру inode.
            $this->failValidationWithStatus(self::RESPONSE_INODE_NOT_FOUND, $validator);
        }

        if (isset($failed['inode'][InodeIsFile::class]) || isset($failed['source'][InodeIsFile::class])) {
            // 2 — Объект не является файлом.
            $this->failValidationWithStatus(self::RESPONSE_INODE_IS_NOT_FILE, $validator);
        }

        if (isset($failed['parent'][InodeIsDirectory::class]) || isset($failed['inode'][InodeIsDirectory::class]) || isset($failed['source'][InodeIsDirectory::class])) {
            // 3 — Объект не является директорией.
            $this->failValidationWithStatus(self::RESPONSE_INODE_IS_NOT_DIRECTORY, $validator);
        }

        if (isset($failed['name'][FileNameExistsInDirectory::class])) {
            // 4 — В указанной директории нет записи с таким именем.
            $this->failValidationWithStatus(self::RESPONSE_DIRECTORY_HAS_NO_FILE, $validator);
        }

        if (isset($failed['name'][FileNameNotExistsInDirectory::class])) {
            // 5 — В указанной директории уже есть запись с таким именем.
            $this->failValidationWithStatus(self::RESPONSE_DIRECTORY_HAS_SUCH_FILE, $validator);
        }

        if (isset($failed['content']['Max'])) {
            // 6 — Превышен лимит на размер файла (512 байт).
            $this->failValidationWithStatus(self::RESPONSE_FILE_SIZE_LIMIT, $validator);
        }

        if (isset($failed['parent'][DirectoryIsNotFull::class])) {
            // 7 — Превышен лимит на количество записей в директории (16).
            $this->failValidationWithStatus(self::RESPONSE_FILE_CNT_LIMIT, $validator);
        }

        if (isset($failed['parent'][DirectoryIsEmpty::class])) {
            // 8 — Директория не пуста.
            $this->failValidationWithStatus(self::RESPONSE_DIRECTORY_IS_NOT_EMPTY, $validator);
        }

        if (isset($failed['name']['Max'])) {
            // 9 — Превышен лимит на длину названия файла (255 символов).
            $this->failValidationWithStatus(self::RESPONSE_FILE_NAME_LIMIT, $validator);
        }

        $this->failValidationWithStatus(self::RESPONSE_MISSED_REQUIRED, $validator);

    }

    protected function passedValidation(): void
    {
        $this->merge([
            'user_token_id' => UserToken::query()->where('token', $this->token)->first()
        ]);
    }

}
