<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SupportTransaction
{
    public static function run(callable $callback): mixed
    {
        return DB::transaction(function () use ($callback) {
            // A write precedes all business reads: SQLite serializes writers here.
            DB::table('supporting_write_locks')->where('id', 1)->increment('revision');

            return $callback();
        }, 3);
    }
}
