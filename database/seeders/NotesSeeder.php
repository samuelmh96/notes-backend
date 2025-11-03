<?php

namespace Database\Seeders;

use App\Models\Note;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NotesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         // Crear usuario de prueba (o usar existente)
        $user = User::firstOrCreate(
            ['email' => 'demo@example.com'],
            [
                'name' => 'Usuario Demo',
                'password' => bcrypt('password123')
            ]
        );

        // Crear tags (o usar existentes)
        $trabajo = Tag::firstOrCreate(['name' => 'trabajo']);
        $personal = Tag::firstOrCreate(['name' => 'personal']);
        $urgente = Tag::firstOrCreate(['name' => 'urgente']);

        // Crear notas solo si no existen
        if ($user->notes()->count() === 0) {
            $nota1 = Note::create([
                'user_id' => $user->id,
                'title' => 'Reunión de proyecto',
                'content' => 'Discutir los avances del proyecto con el equipo'
            ]);
            $nota1->tags()->attach([$trabajo->id, $urgente->id]);

            $nota2 = Note::create([
                'user_id' => $user->id,
                'title' => 'Lista de compras',
                'content' => 'Leche, pan, huevos, frutas'
            ]);
            $nota2->tags()->attach([$personal->id]);

            $nota3 = Note::create([
                'user_id' => $user->id,
                'title' => 'Ideas para el blog',
                'content' => 'Escribir sobre Laravel y Vue.js'
            ]);
            $nota3->tags()->attach([$trabajo->id]);
        }

        $this->command->info('Usuario demo: demo@example.com / password123');
    }
}
