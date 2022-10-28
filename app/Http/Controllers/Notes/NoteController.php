<?php

namespace App\Http\Controllers\Notes;

use App\Http\Controllers\Controller;
use App\Http\Requests\Notes\NoteCreateRequest;
use App\Http\Requests\Notes\NoteUpdateRequest;
use App\Http\Resources\Notes\NoteResource;
use App\Jobs\SendNoteCreatedMail;
use App\Models\Note;
use App\Services\NoteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class NoteController extends Controller
{
    private NoteService $service;

    public function __construct(NoteService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return NoteResource::collection($this->service->all(
            $request->query('limit'),
            $request->query('offset'),
            $request->query('authorId'),
            $request->query('searchQuery')
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param NoteCreateRequest $request
     * @return JsonResponse
     */
    public function store(NoteCreateRequest $request): JsonResponse
    {
        $note = $this->service->create(Note::class, $request->validated());

        SendNoteCreatedMail::dispatch();

        return (new NoteResource($note))
            ->additional([
                'message' => trans('messages.created')
            ])->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param NoteUpdateRequest $request
     * @param Note $note
     * @return JsonResponse
     */
    public function update(NoteUpdateRequest $request, Note $note): JsonResponse
    {
        return (new NoteResource($this->service->update($note, $request->validated())))
            ->additional([
                'message' => trans('messages.updated')
            ])->response()
            ->setStatusCode(Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Note $note
     * @return JsonResponse
     */
    public function destroy(Note $note): JsonResponse
    {
        $this->service->delete($note);

        return \response()
            ->json([
                'message' => trans('messages.deleted')
            ], Response::HTTP_NO_CONTENT);
    }
}
