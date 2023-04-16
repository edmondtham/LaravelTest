<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Enums\PostStatusEnum;
use App\Mail\PublishEmail;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Post::query();
    
        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }
        
        if ($request->has('published_since')) {
            $query->where('published_at', '>=', $request->input('published_since'));
        }
        
        $posts = $query->get();
        
        return response()->json($posts);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $post = new Post($request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'author_id' => 'required|exists:authors,id'
        ]));

        if ($request->has('status')) {
            $post->status = $request->input('status');
            if ($request->input('status') == 'published'){
                $post->published_at = Carbon::now();
                $author = $post->author;
                Mail::to($author->email)->send(new PublishEmail($post));
            }
        }

        $post->save();
        return response()->json($post, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $post = Post::find($id);
        if ($post) {            
            return response()->json($post);
        }
        return response()->json(null, 404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);
        
        if ($post) {
            if ($request->has('status') && $request->input('status') == 'published' && !$post->published_at) {
                $post->status = 'published';
                $post->published_at = Carbon::now();
                $author = $post->author;
                Mail::to($author->email)->send(new PublishEmail($post));
            }
    
            $post->save();
            return response()->json($post);
        }
            
        return response()->json(null, 404);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $post = Post::find($id);
        if ($post){
            $post->delete();
            return response()->json(null, 204);
        }
        return response()->json(null, 404);
    }
}
