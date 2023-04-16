<?php

namespace App\Services;

use App\Models\Post;
use App\Mail\PublishEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class PostService
{
    
    public function create(Request $request){
        $post = new Post($request->validate([
            'title' => 'required|max:255',
            'content' => 'required',
            'author_id' => 'required|exists:authors,id',
            'status' => 'required'
        ]));
        $post->save();
        return $post;
    }

    public function publish(string $id){
        $post = Post::find($id);
        
        if ($post) {
            if (!$post->published_at) {
                $post->status = 'published';
                $post->published_at = Carbon::now();
                $author = $post->author;
                Mail::to($author->email)->send(new PublishEmail($post));
            }
    
            $post->save();
            return $post;
        }
        return null;
    }
}