<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

       //la fonction de génération aléatoire du token

       public function respondWithToken($token)
       {
   
           return response()->json(
               [
                   "title" => "CONNEXION",
                   "message" => "Connexion réussie.Vous vous êtes connecté(e) avec succès.",
                   'token' => $token,
                   'token_type' => 'bearer',
                   'token_validity' => Auth::guard('api')
               ]
           );

       }


    public function guard()
    {
        return Auth::guard();
    }


    public function register(Request $request)
    {
        try {

            $messages = [

                "nom.required" => "Votre nom est requis",
                "prenoms.required" => "Votre prénoms est requis",
                "email.required" => "Votre adresse mail est requise",
                "email.email" => "Votre adresse mail est invalide",
                "password.required" => "Le mot de passe est requis",
                "password.min" => "Le mot de passe est trop court",
                "password.same" => "Les mots de passes ne sont pas identiques",
                
            ];

            $validator = Validator::make($request->all(), [

                "nom" => "bail|required|max:255|",
                "prenoms" => "bail|required|max:255",
                "email" => "bail|required|email|max:255|unique:users,email",
                "password" => "bail|required|min:4|same:confirmation_password",
    
            ], $messages);  

                
            if ($validator->fails()) return response()->json([
                "status" => "error",
                "title" => "INSCRIPTION",
                "message" => $validator->errors()->first()
            ]);


            $user = new User();
            $user->nom = $request->nom;
            $user->prenoms = $request->prenoms;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            
            $token = Auth::login($user);
            //Générer un token
            $user->save();

            $token = $user->createToken('user_token')->plainTextToken;
            return response()->json([
               
                "title" => "INSCRIPTION",
                "status" => true,
                "message" => "Mr/Mlle " . $user->nom . " " . $user->prenoms . ". Votre compte a été crée avec succes.",
                'user' => $user,
                'token' => $token

            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Quelque chose s'est passé dans le controller de l'autentification d'inscription",
            ]);
        }
    }

    public function login(Request $request)
    {

        $messages = [

            "email.required" => "Votre adresse mail est requise",
            "email.email" => "Votre adresse mail est invalide",
            "password.required" => "Le mot de passe est requis",
            "password.min" => "Le mot de passe est trop court",
        ];

        $validator = Validator::make($request->all(), [

            "email" => "bail|required|email|max:255|",
            "password" => "bail|required|min:8|max:50",

        ], $messages);

        if ($validator->fails()) {

            return response()->json([
                "status" => false,
                "title" => "CONNEXION",
                "message" => $validator->errors()->first()
            ]);
        }

        
        //validité du token(1j = 24h et 1h =60min donc token_validity = 24*60)

        $token_validity = 60;
        // Définition de la durée de validité du token pour le guard "api"
         config(['auth.guards.api.expire' => $token_validity]);
    
            // Générer et retourner le token
            //condition de fonctionnement

          if (!$token = Auth::guard('api')->attempt($validator->validated())) {
            return response()->json([
                "status" => false,
                "title" => "CONNEXION",
                'erreur' => "Adresse Email ou mot de passe incorrect"

            ], 401);
        }



        
        //Vérifier si le client existe

        $user = User::where('email', "=", $request->input('email'))->firstOrFail();

        if ($user)
        
        {
            if (Hash::check($request->input('password'), $user->password)) {

                //créer un jeton ou un token
             
                $token = $user->createToken('user_token')->plainTextToken;

                return response()->json([

                    "title" => "CONNEXION",
                    "status" => true,
                    "message" => "Mr/Mlle " . $user->nom . " " . $user->prenoms . ", vous vous êtes connectés avec succes",
                    'user' => $user,
                    'acces_token' => $token,
                ], 200);
                
            } 
            
            else
            
            {
                //réponse
                return response()->json([
                    "status" => false,
                    "message" => "Mot de Passe incorrect"
                ], 404);

            }
        } else {
            //réponse
            return response()->json([
                "status" => false,
                "message" => "Utilisateur n'existe pas ou est introuvable"
            ], 404);
        }
    }


    public function logout(Request $request)
    {
        try {

            $user = User::findOrFail($request->input(('user_id')));
            $user->tokens()->delete();
            return response()->json([
                "Utilisateur déconnecté avec succès"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => "Quelque chose s'est passé dans le controller de l'autentification de déconnexion",
            ]);
        }
    }
}
