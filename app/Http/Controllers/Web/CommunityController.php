<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CommunityPost;
use App\Models\CommunityReaction;
use App\Models\Zone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CommunityController extends Controller
{
    public function index(Request $request): View
    {
        $posts = CommunityPost::query()
            ->where('status', 'PUBLISHED')
            ->with(['user', 'zone', 'comments.user'])
            ->withCount(['comments', 'reactions'])
            ->latest()
            ->paginate(10);

        return view('community.index', [
            'posts' => $posts,
            'zones' => Zone::query()->orderBy('city')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'zone_id' => ['nullable', 'exists:zones,id'],
            'category' => ['required', 'in:UPDATE,QUESTION,TIP'],
            'title' => ['required', 'string', 'max:120'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $request->user()->communityPosts()->create($data + ['status' => 'PUBLISHED']);

        return back()->with('success', 'Your community update has been shared.');
    }

    public function comment(Request $request, CommunityPost $post): RedirectResponse
    {
        abort_unless($post->status === 'PUBLISHED', 404);

        $data = $request->validate(['body' => ['required', 'string', 'max:1000']]);
        $post->comments()->create($data + ['user_id' => $request->user()->id]);

        return back()->with('success', 'Your comment has been added.');
    }

    public function react(Request $request, CommunityPost $post): RedirectResponse
    {
        abort_unless($post->status === 'PUBLISHED', 404);

        $reaction = CommunityReaction::query()
            ->where('community_post_id', $post->id)
            ->where('user_id', $request->user()->id)
            ->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            $post->reactions()->create(['user_id' => $request->user()->id]);
        }

        return back();
    }

    public function hide(CommunityPost $post): RedirectResponse
    {
        $post->update(['status' => 'HIDDEN']);

        return back()->with('success', 'Community post hidden.');
    }
}
