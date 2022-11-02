<?php

namespace App\Services;

use App\Models\Note;
use App\Services\Abstracts\CrudClass;

class NoteService extends CrudClass
{
    public function all(
        ?int $limit,
        ?int $offset,
        ?int $authorId,
        ?string $query
    ) {
        return Note::with('author')
            ->latest('id')
            ->filterByAuthorId($authorId)
            ->search($query)
            ->pagination($limit, $offset);
    }
}
