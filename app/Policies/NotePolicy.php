<?php

namespace App\Policies;

use App\Models\Note;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class NotePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Note $note
     * @return Response|bool
     */
    public function update(User $user, Note $note): Response|bool
    {
        return $this->checkOwnership($user, $note);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Note $note
     * @return Response|bool
     */
    public function delete(User $user, Note $note): Response|bool
    {
        return $this->checkOwnership($user, $note);
    }

    private function checkOwnership(User $user, Note $note): Response
    {
        return $user->id === $note->author_id
            ? Response::allow()
            : Response::deny(trans('messages.not_yours'));
    }
}
