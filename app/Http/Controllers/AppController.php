<?php

namespace App\Http\Controllers;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\Request;

class AppController extends Controller
{
    
    public function index(): Response
    {
        return Inertia::render('Home', [
            'message' => 'Bienvenue sur la page d’accueil (via contrôleur) !'
        ]);
    }
}
