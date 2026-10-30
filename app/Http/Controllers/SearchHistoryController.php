<?php

namespace App\Http\Controllers;

use App\Models\SearchHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

/**
 * JSON API for the client search history (logged-in clients only).
 * Guests keep their history in the browser (localStorage).
 */
class SearchHistoryController extends Controller
{
    /**
     * List the client's recent searches.
     */
    public function index(Request $request)
    {
        return response()->json([
            'authenticated' => true,
            'items' => $this->items($request->user()->id),
        ]);
    }

    /**
     * Record a search term.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'q' => 'required|string|max:150',
        ]);

        SearchHistory::record($request->user()->id, $data['q']);

        return response()->json([
            'success' => true,
            'items' => $this->items($request->user()->id),
        ]);
    }

    /**
     * Merge searches stored in the browser (made as a guest) into the account.
     */
    public function sync(Request $request)
    {
        $data = $request->validate([
            'items' => 'nullable|array|max:30',
            'items.*.q' => 'required|string|max:150',
            'items.*.t' => 'nullable|integer', // timestamp in ms
        ]);

        $userId = $request->user()->id;

        foreach ($data['items'] ?? [] as $item) {
            $at = !empty($item['t'])
                ? Carbon::createFromTimestampMs($item['t'])->min(now())
                : now();
            SearchHistory::record($userId, $item['q'], $at);
        }

        return response()->json([
            'success' => true,
            'items' => $this->items($userId),
        ]);
    }

    /**
     * Remove one search term.
     */
    public function destroy(Request $request)
    {
        $data = $request->validate([
            'q' => 'required|string|max:150',
        ]);

        SearchHistory::where('user_id', $request->user()->id)
            ->where('query_key', SearchHistory::keyFor(SearchHistory::cleanTerm($data['q'])))
            ->delete();

        return response()->json([
            'success' => true,
            'items' => $this->items($request->user()->id),
        ]);
    }

    /**
     * Clear the whole history.
     */
    public function clear(Request $request)
    {
        SearchHistory::where('user_id', $request->user()->id)->delete();

        return response()->json(['success' => true, 'items' => []]);
    }

    private function items(int $userId): array
    {
        return SearchHistory::where('user_id', $userId)
            ->orderByDesc('last_searched_at')
            ->limit(SearchHistory::MAX_PER_USER)
            ->get(['query', 'last_searched_at'])
            ->map(fn ($h) => [
                'q' => $h->query,
                't' => optional($h->last_searched_at)->getTimestampMs(),
            ])
            ->values()
            ->all();
    }
}

