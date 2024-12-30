<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Requests\PostRequest;
use App\Notifications\PostApproved;

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

    /**
     * Approve a post and notify the user.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, $id)
    {
        // Check if the authenticated user is an admin
        if (!auth()->user()->is_admin) {
            return back()->with('error', 'You are not authorized to approve posts.');
        }

        // Find the post by ID
        $post = Post::find($id);

        if (!$post) {
            return back()->with('error', 'Post not found.');
        }

        if (!$post->is_approved) {
            $post->is_approved = true;
            $post->save();

            // Notify the post's author
            if ($post->user) {
                $post->user->notify(new PostApproved($post));
            }

            return back()->with('success', 'Post approved and user notified.');
        }

        return back()->with('info', 'Post is already approved.');
    }

    /**
     * Mark a notification as read.
     *
     * @param Request $request
     * @param string $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function markAsRead(Request $request, $id)
    {
        $notification = auth()->user()->unreadNotifications->find($id);

        if (!$notification) {
            return back()->with('error', 'Notification not found.');
        }

        $notification->markAsRead();

        return back()->with('success', 'Notification marked as read.');
    }
}
