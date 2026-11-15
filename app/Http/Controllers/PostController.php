<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Support\PortfolioData;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(): View
    {
        return view('pages.blogs', [
            'appData' => PortfolioData::make([
                'posts' => PostResource::collection(Post::published()->get()),
            ]),
        ]);
    }

    public function show(Post $post): View
    {
        abort_unless($post->isPublished(), 404);

        // The current post plus up to three others for "Keep reading".
        $related = Post::published()->whereKeyNot($post->getKey())->take(3)->get();

        return view('pages.blog-details', [
            'post' => $post,
            'appData' => PortfolioData::make([
                'posts' => PostResource::collection($related->prepend($post)),
            ]),
        ]);
    }
}
