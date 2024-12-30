<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Display a listing of all posts.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Fetch all posts with eager loading
        $posts = Post::with('user')->latest()->get();

        return view('posts', compact('posts'));
    }

    /**
     * Store a newly created post in storage.
     *
     * @param PostRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostRequest $request)
    {
        // Create the post
        Post::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'body' => $request->body,
        ]);

        return redirect()->back()->with('success', 'Post created successfully.');
    }
}
