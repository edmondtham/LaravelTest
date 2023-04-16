<?php

namespace App\Services;

use App\Models\Author;
use Illuminate\Http\Request;

class AuthorService
{
    
    public function create(Request $request){
        $validatedData = $request->validate([
            'name' => 'required|max:255',
            'email' => 'required|email',
        ]);
        $author = Author::create($validatedData);
        return $author;
    }
}