<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        try{
            $user = User::findOrFail($id);
            return response()->json(['user'=>$user],200);
        }

        catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Une erreur trouvée dans UserController.show",
            ], 400);
        }
       
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        try{

            $messages = [

                "nom.required" => "Votre nom est requis",
                "nom.min" => "Le champ nom doit comporter au moins 3 caractères",
                "prenoms.required" => "Votre prénoms est requis",
                "prenoms.min" => "Le champ prénoms doit comporter au moins 3 caractères",
                
            ];


            $validator = Validator::make($request->all(), [

                "nom" => "bail|required|min:3|max:255|",
                "prenoms" => "bail|required|min:3|max:255",
    
            ], $messages);  

            if ($validator->fails()) return response()->json([
                "status" => "error",
                "title" => "INSCRIPTION",
                "message" => $validator->errors()->first()
            ]);


            $user = User::findOrFail($id);
            $user->nom =$request->nom;
            $user->prenoms =$request->prenoms;
            $user->update();

            return response()->json(["Détails de l'utilisateur mise à jour"],200);
        }

        catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Une erreur trouvée dans UserController.update",
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
