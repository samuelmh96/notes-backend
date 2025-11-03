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
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10); // 10 notas por página por defecto

        $query = Note::with('tags')
            ->where('user_id', $request->user()->id);

        // Búsqueda por título o contenido
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Filtro por tag
        if ($request->has('tag')) {
            $tagId = $request->input('tag');
            $query->whereHas('tags', function ($q) use ($tagId) {
                $q->where('tags.id', $tagId);
            });
        }

        // Filtro por favoritas
        if ($request->has('favorites') && $request->input('favorites') == 'true') {
            $query->where('is_favorite', true);
        }

        // Ordenamiento
        $sortBy = $request->input('sort', 'date_desc');

        switch ($sortBy) {
            case 'date_asc':
                $query->oldest();
                break;
            case 'date_desc':
                $query->latest();
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            case 'favorites':
                $query->orderBy('is_favorite', 'desc')->latest();
                break;
            default:
                $query->latest();
        }

        // Paginación
        $notes = $query->paginate($perPage);

        return response()->json($notes);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'tags' => 'array'
            ]);

            $note = Note::create([
                'user_id' => $request->user()->id,  // Asignar usuario
                'title' => $validated['title'],
                'content' => $validated['content']
            ]);

            if (isset($validated['tags'])) {
                $note->tags()->sync($validated['tags']);
            }

            return response()->json([
                'message' => 'Nota creada exitosamente',
                'note' => $note->load('tags')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al crear la nota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Note $note)
    {
        // Verificar que la nota pertenece al usuario
        if ($note->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para ver esta nota'
            ], 403);
        }

        return response()->json($note->load('tags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        // Verificar que la nota pertenece al usuario
        if ($note->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para editar esta nota'
            ], 403);
        }

        try {
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

            return response()->json([
                'message' => 'Nota actualizada exitosamente',
                'note' => $note->load('tags')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar la nota',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Note $note)
    {
        // Verificar que la nota pertenece al usuario
        if ($note->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para eliminar esta nota'
            ], 403);
        }

        $note->delete();
        return response()->json(['message' => 'Note deleted successfully'], 200);
    }

    public function toggleFavorite(Request $request, Note $note)
    {
        // Verificar que la nota pertenece al usuario
        if ($note->user_id !== $request->user()->id) {
            return response()->json([
                'message' => 'No tienes permiso para modificar esta nota'
            ], 403);
        }

        $note->update([
            'is_favorite' => !$note->is_favorite
        ]);

        return response()->json([
            'message' => $note->is_favorite ? 'Nota marcada como favorita' : 'Nota desmarcada como favorita',
            'note' => $note->load('tags')
        ]);
    }
}
