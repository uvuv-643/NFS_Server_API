<?php

namespace App\Http\Controllers;

use App\Http\Requests\FS\CreateRequest;
use App\Http\Requests\FS\LinkRequest;
use App\Http\Requests\FS\ListRequest;
use App\Http\Requests\FS\LookupRequest;
use App\Http\Requests\FS\ReadRequest;
use App\Http\Requests\FS\RmdirRequest;
use App\Http\Requests\FS\UnlinkRequest;
use App\Http\Requests\FS\WriteRequest;
use App\Models\Node;
use Illuminate\Http\JsonResponse;

class FSController extends Controller
{

    const RESPONSE_SUCCESS = 0;

    public function list(ListRequest $request) : JsonResponse
    {

        $entries = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('parent_id', $request->inode)
            ->get();

        return response()->json([
            'status' => self::RESPONSE_SUCCESS,
            'response' => [
                'entries' => [
                    'entries_count' => $entries->count(),
                    'entries' => $entries->map(function (Node $entry) {
                        return [
                            'entry_type' => $entry->type == 'file' ? 'f' : 'd',
                            'ino' => $entry->inode,
                            'name' => $entry->name
                        ];
                    })
                ]
            ]
        ]);
    }

    public function create(CreateRequest $request) : JsonResponse
    {

        /** @var Node $parent */
        $parent = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('inode', $request->parent)
            ->first();

        Node::query()->create([
            'user_token_id' => $request->user_token_id,
            'inode' => random_int(0, PHP_INT_MAX),
            'name' => $request->name,
            'type' => $request->type,
            'parent_id' => $parent->id
        ]);

        return response()->json([
            'status' => self::RESPONSE_SUCCESS
        ]);

    }

    public function read(ReadRequest $request) : JsonResponse
    {

    }

    public function write(WriteRequest $request) : JsonResponse
    {

    }

    public function link(LinkRequest $request) : JsonResponse
    {

    }

    public function unlink(UnlinkRequest $request) : JsonResponse
    {

    }

    public function rmdir(RmdirRequest $request) : JsonResponse
    {

    }

    public function lookup(LookupRequest $request) : JsonResponse
    {

    }

}
