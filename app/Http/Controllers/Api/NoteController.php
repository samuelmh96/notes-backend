<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Mockery\Matcher\Not;

class NoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $notes = Note::with('tags')->latest()->get();
        return response()->json($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array'
        ]);

        $note = Note::create([
            'title' => $validated['title'],
            'content' => $validated['content']
        ]);

        if (isset($validated['tags'])) {
            $note->tags()->sync($validated['tags']);
        }

        return response()->json($note->load('tags'), 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
         return response()->json($note->load('tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
         $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'tags' => 'array'
        ]);

        $note->update([
            'title' => $validated['title'],
            'content' => $validated['content']
        ]);

        if (isset($validated['tags'])) {
            $note->tags()->sync($validated['tags']);
        }

        return response()->json($note->load('tags'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
         $note->delete();
        return response()->json(['message' => 'Note deleted successfully'], 200);
    }
}
