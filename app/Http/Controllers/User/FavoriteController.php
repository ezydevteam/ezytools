<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FavoriteController extends Controller
{
    /**
     * Display a listing of the user's favorite tools.
     */
    public function index()
    {
        $favorites = auth()->user()->favoriteTools()->with('category')->get();
        
        return Inertia::render('User/Favorites', [
            'favorites' => $favorites
        ]);
    }

    /**
     * Toggle the favorite status of a tool.
     */
    public function toggle(Tool $tool)
    {
        $user = auth()->user();
        
        $user->favoriteTools()->toggle($tool->id);
        
        return redirect()->back();
    }
}
