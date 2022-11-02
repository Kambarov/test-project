<?php

namespace Tests\Feature;

use App\Jobs\SendNoteCreatedMail;
use App\Models\Note;
use App\Models\User;
use Illuminate\Support\Facades\Queue;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class NotesCrudTest extends TestCase
{
    public function test_get_notes()
    {
        $user = User::factory()
            ->create();

        Note::factory()
            ->count(10)
            ->state([
                'author_id' => User::query()->inRandomOrder()->first()->id
            ])
            ->create();

        $response = $this->actingAs($user)
            ->getJson('api/notes');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'content',
                        'author',
                        'created_at'
                    ]
                ]
            ]);
    }

    public function test_create_note()
    {
        $user = User::factory()
            ->create();

        $note = Note::factory()
            ->make();

        Queue::fake();

        $response = $this->actingAs($user)
            ->postJson('api/notes', [
                'name' => $note->name,
                'content' => $note->content
            ]);

        Queue::assertPushed(SendNoteCreatedMail::class);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'content',
                    'created_at'
                ],
                'message'
            ]);
    }

    public function test_note_cannot_be_updated()
    {
        $user = User::factory()
            ->create();

        $note = Note::factory()
            ->state([
                'author_id' => $user->id
            ])
            ->create();

        $newNote = Note::factory()
            ->make();

        $otherUser = User::factory()
            ->create();

        $response = $this->actingAs($otherUser)
            ->putJson('api/notes/' . $note->id, [
                'name' => $note->name,
                'content' => $newNote->content
            ]);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_note_update()
    {
        $user = User::factory()
            ->create();

        $note = Note::factory()
            ->state([
                'author_id' => $user->id
            ])
            ->create();

        $newNote = Note::factory()
            ->make();

        $response = $this->actingAs($user)
            ->putJson('api/notes/' . $note->id, [
                'name' => $note->name,
                'content' => $newNote->content
            ]);

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'content',
                    'created_at'
                ],
                'message'
            ]);
    }

    public function test_note_cannot_be_deleted()
    {
        $user = User::factory()
            ->create();

        $note = Note::factory()
            ->state([
                'author_id' => $user->id
            ])
            ->create();

        $otherUser = User::factory()
            ->create();

        $response = $this->actingAs($otherUser)
            ->deleteJson('api/notes/' . $note->id);

        $response->assertStatus(Response::HTTP_FORBIDDEN);
    }

    public function test_note_delete()
    {
        $user = User::factory()
            ->create();

        $note = Note::factory()
            ->state([
                'author_id' => $user->id
            ])
            ->create();

        $response = $this->actingAs($user)
            ->deleteJson('api/notes/' . $note->id);

        $response->assertStatus(Response::HTTP_NO_CONTENT);
    }
}
