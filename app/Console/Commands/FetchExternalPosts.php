<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Post;
use App\Models\Author;

class FetchExternalPosts extends Command
{
    protected $signature = 'fetch:external-posts';
    // protected $description = 'Fetch external posts from DummyJSON API';

    public function handle()
    {
        // Send GET request to DummyJSON post API
        $response = Http::get('https://dummyjson.com/posts');

        if ($response->ok()) {
            // Decode response JSON
            $response = $response->json();
            if (!empty($response['posts'])){
                $posts = $response['posts'];
                // Loop through posts and create new draft blog post in userId found
                foreach ($posts as $post) {
                    $author = Author::find($post['userId']);
                    if ($author){
                        $newPost = new Post([
                            'title' => $post['title'],
                            'content' => $post['body'],
                            'status' => 'draft',
                            'published_at' => null,
                            'author_id' => $post['userId'],
                        ]);
                        $newPost->save();
                    }
                }
            }

            $this->info('Successfully fetched and saved external posts.');
        } else {
            $this->error('Failed to fetch external posts.');
        }
    }
}