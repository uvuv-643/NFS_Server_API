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
use App\Services\FSService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class FSController extends Controller
{

    const RESPONSE_SUCCESS = 0;
    const RESPONSE_FAILURE = 5501;

    public function list(FSService $service, ListRequest $request): JsonResponse|Response
    {
        $entries = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->where('parent_id', $service->getNodeIdByInode($request->inode, $request->user_token_id))
            ->get();

        if ($request->input('json')) {
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
        } else {
            $response = pack('PP', self::RESPONSE_SUCCESS, $entries->count());
            for ($entryIndex = 0; $entryIndex < 16; $entryIndex++) {
                if ($entryIndex < $entries->count()) {
                    $entry = $entries[$entryIndex];
                    $response .= pack('a8', $entry->type == 'file' ? 'f' : 'd');
                    $response .= pack('P', $entry->inode);
                    $response .= pack('a256', $entry->name);
                } else {
                    $response .= str_repeat(pack('C', 0), 272);
                }

            }
            return response($response)->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($response)
            ]);
        }

    }

    public function create(CreateRequest $request): JsonResponse|Response
    {

        /** @var Node $parent */
        /** @var Node $node */
        $parent = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('inode', $request->parent)
            ->first();

        try {
            $node = Node::query()->create([
                'user_token_id' => $request->user_token_id,
                'inode' => random_int(0, PHP_INT_MAX),
                'name' => $request->name,
                'type' => $request->type,
                'parent_id' => $parent->id
            ]);
        } catch (Exception $exception) {
            if ($request->input('json')) {
                return response()->json([
                    'status' => self::RESPONSE_FAILURE,
                    'message' => $exception->getMessage()
                ]);
            } else {
                return response(pack('P', self::RESPONSE_FAILURE))->withHeaders([
                    'Content-Type' => 'application/octet-stream',
                    'Content-Length' => 8
                ]);
            }
        }

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
                'response' => [
                    'ino' => $node->inode
                ]
            ]);
        } else {
            return response(pack('PP', self::RESPONSE_FAILURE, $node->inode))->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => 16
            ]);
        }

    }

    public function read(ReadRequest $request): JsonResponse|Response
    {
        /** @var Node $node */
        $node = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('inode', $request->inode)
            ->first();

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
                'response' => [
                    'content_length' => strlen($node->content),
                    'content' => $node->content
                ]
            ]);
        } else {
            $response = pack('PPc*', self::RESPONSE_SUCCESS, strlen($node->content), $node->content);
            return response($response)->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($response)
            ]);
        }

    }

    public function write(WriteRequest $request): JsonResponse|Response
    {
        Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->where('inode', $request->inode)
            ->update([
                'content' => $request->content
            ]);

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
            ]);
        } else {
            return response(pack('P', self::RESPONSE_SUCCESS))->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => 8
            ]);
        }
    }

    public function link(LinkRequest $request): JsonResponse|Response
    {

        /** @var Node $parent */
        /** @var Node $source */
        /** @var Node $node */
        $parent = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('inode', $request->parent)
            ->first();

        $source = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->where('inode', $request->source)
            ->first();

        try {
            Node::query()->create([
                'user_token_id' => $request->user_token_id,
                'inode' => $source->inode,
                'name' => $request->name,
                'type' => $source->type,
                'hard_link_id' => $source->id,
                'parent_id' => $parent->id,
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'status' => self::RESPONSE_FAILURE,
                'message' => $exception->getMessage()
            ]);
        }

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
            ]);
        } else {
            return response(pack('P', self::RESPONSE_SUCCESS))->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => 8
            ]);
        }
    }

    public function unlink(FSService $service, UnlinkRequest $request): JsonResponse|Response
    {
        Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->where('parent_id', $service->getNodeIdByInode($request->parent, $request->user_token_id))
            ->where('name', $request->name)->delete();

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
            ]);
        } else {
            return response(pack('P', self::RESPONSE_SUCCESS))->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => 8
            ]);
        }
    }

    public function rmdir(FSService $service, RmdirRequest $request): JsonResponse|Response
    {
        Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('parent_id', $service->getNodeIdByInode($request->parent, $request->user_token_id))
            ->where('name', $request->name)->delete();

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
            ]);
        } else {
            return response(pack('P', self::RESPONSE_SUCCESS))->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => 8
            ]);
        }
    }

    public function lookup(FSService $service, LookupRequest $request): JsonResponse|Response
    {
        /** @var Node $node */
        $node = Node::query()
            ->where('user_token_id', $request->user_token_id)
            ->whereNull('hard_link_id')
            ->where('parent_id', $service->getNodeIdByInode($request->parent, $request->user_token_id))
            ->where('name', $request->name)->first();

        if ($request->input('json')) {
            return response()->json([
                'status' => self::RESPONSE_SUCCESS,
                'response' => [
                    'entry_info' => [
                        'entry_type' => $node->type == 'file' ? 'f' : 'd',
                        'ino' => $node->inode
                    ]
                ]
            ]);
        } else {
            $response = pack('P', self::RESPONSE_SUCCESS);
            $response .= pack('a8', $node->type == 'file' ? 'f' : 'd');
            $response .= pack('P', $node->inode);
            return response()->withHeaders([
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => strlen($response)
            ]);
        }
    }

}
