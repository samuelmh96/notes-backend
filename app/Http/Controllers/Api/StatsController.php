<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;

        // Total de notas
        $totalNotes = Note::where('user_id', $userId)->count();

        // Notas favoritas
        $favoriteNotes = Note::where('user_id', $userId)
            ->where('is_favorite', true)
            ->count();

        // Notas por tag
        $notesByTag = DB::table('notes')
            ->join('note_tag', 'notes.id', '=', 'note_tag.note_id')
            ->join('tags', 'note_tag.tag_id', '=', 'tags.id')
            ->where('notes.user_id', $userId)
            ->select('tags.name', DB::raw('count(*) as total'))
            ->groupBy('tags.id', 'tags.name')
            ->orderBy('total', 'desc')
            ->get();

        // Notas recientes (últimas 5)
        $recentNotes = Note::where('user_id', $userId)
            ->with('tags')
            ->latest()
            ->limit(5)
            ->get();

        // Tags más usados
        $topTags = DB::table('tags')
            ->join('note_tag', 'tags.id', '=', 'note_tag.tag_id')
            ->join('notes', 'note_tag.note_id', '=', 'notes.id')
            ->where('notes.user_id', $userId)
            ->select('tags.id', 'tags.name', DB::raw('count(*) as usage_count'))
            ->groupBy('tags.id', 'tags.name')
            ->orderBy('usage_count', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'total_notes' => $totalNotes,
            'favorite_notes' => $favoriteNotes,
            'notes_by_tag' => $notesByTag,
            'recent_notes' => $recentNotes,
            'top_tags' => $topTags
        ]);
    }
}
