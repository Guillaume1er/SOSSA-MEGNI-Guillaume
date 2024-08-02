<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class TacheController extends Controller
{
    public function index()
    {
        // Récupérer toutes les tâches
        $taches = Tache::all();
        return response()->json($taches);
    }

    public function store(Request $request)
    {
        // Valider les données entrantes
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_echeance' => 'required|date',
            'statut' => 'required|string',
            'lien_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Gérer l'upload de fichier
        $filePath = null;
        if ($request->hasFile('lien_image')) {
            $filePath = $request->file('lien_image')->store('Taches', 'public');
        }

        // Créer la tâche
        $tache = Tache::create([
            'title' => $request->title,
            'description' => $request->description,
            'date_echeance' => $request->date_echeance,
            'lien_image' => $filePath,
        ]);

        return response()->json($tache, 201);
    }

    public function show($id)
    {
        // Récupérer une tâche par ID
        $tache = Tache::find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }

        return response()->json($tache);
    }

    public function update(Request $request, $id)
    {
        // Récupérer la tâche à mettre à jour
        $tache = Tache::find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }

        // Valider les données entrantes
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'date_echeance' => 'required|date',
            'statut' => 'required|string',
            'lien_image' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Gérer l'upload de fichier
        if ($request->hasFile('file')) {
            // Supprimer le fichier existant
            if ($tache->lien_image) {
                Storage::disk('public')->delete($tache->lien_image);
            }
            // Enregistrer le nouveau fichier
            $tache->lien_image = $request->file('file')->store('Taches', 'public');
        }

        // Mettre à jour la tâche
        $tache->update([
            'title' => $request->title,
            'description' => $request->description,
            'date_echeance' => $request->date_echeance,
            'statut' => $request->statut,
        ]);

        return response()->json($tache);
    }

    public function destroy($id)
    {
        // Récupérer la tâche à supprimer
        $tache = Tache::find($id);
        if (!$tache) {
            return response()->json(['message' => 'Tâche non trouvée'], 404);
        }

        // Supprimer le fichier associé
        if ($tache->lien_image) {
            Storage::disk('public')->delete($tache->lien_image);
        }

        // Supprimer la tâche
        $tache->delete();

        return response()->json(['message' => 'Tâche supprimée avec succès']);
    }

    public function downloadFile($id)
    {
        // Télécharger le fichier associé à une tâche
        $tache = Tache::find($id);
        if (!$tache || !$tache->lien_image) {
            return response()->json(['message' => 'Fichier non trouvé'], 404);
        }

        return response()->download(storage_path("app/public/{$tache->lien_image}"));
    }
}
