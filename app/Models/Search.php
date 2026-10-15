<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A recorded search, used for the "recent searches" chips and for admins to see
 * what travellers cannot find.
 */
class Search extends Model
{
    protected $fillable = ['user_id', 'query', 'type', 'result_count', 'ip_address'];

    protected function casts(): array
    {
        return ['result_count' => 'integer'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Popular queries for the admin search-insights panel. */
    public static function topQueries(int $limit = 10)
    {
        return static::query()
            ->whereNotNull('query')
            ->where('query', '!=', '')
            ->selectRaw('query, COUNT(*) as hits')
            ->groupBy('query')
            ->orderByDesc('hits')
            ->limit($limit)
            ->get();
    }
}
