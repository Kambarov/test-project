<?php

namespace App\Mixins;

use Illuminate\Database\Eloquent\Builder;

class PaginationMixin
{
    public function pagination(): \Closure
    {
        return function (?int $limit, ?int $offset = 0) {
            if (!is_null($limit)) {
                if ($limit === 0) {
                    return Builder::get();
                }

                return Builder::offset($offset)->limit($limit)->get();
            }

            return Builder::paginate(config('app.per_page'));
        };
    }
}
