<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Streamer;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StreamerController extends Controller
{
    public function index()
    {
        return view('admin.donate.streamer.index', [
            'streamers' => Streamer::with('user:ID,name')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        Streamer::create($this->validated($request));

        return redirect()->route('admin.donate.streamer.index')
            ->with('success', __('donate.streamer.added'));
    }

    public function edit(Streamer $streamer)
    {
        return view('admin.donate.streamer.edit', ['streamer' => $streamer]);
    }

    public function update(Request $request, Streamer $streamer)
    {
        $streamer->update($this->validated($request, $streamer));

        return redirect()->route('admin.donate.streamer.index')
            ->with('success', __('donate.streamer.updated'));
    }

    public function destroy(Streamer $streamer)
    {
        $streamer->delete();

        return redirect()->route('admin.donate.streamer.index')
            ->with('success', __('donate.streamer.deleted'));
    }

    private function validated(Request $request, ?Streamer $streamer = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:50',
            'user_id' => 'required|integer|exists:users,ID',
            'code' => ['required', 'alpha_dash', 'max:30', Rule::unique('pwp_streamers', 'code')->ignore($streamer)],
            'bonus_percent' => 'required|numeric|min:0|max:100',
        ], [], [
            'name' => __('donate.streamer.name'),
            'user_id' => __('donate.streamer.user_id'),
            'code' => __('donate.streamer.code'),
            'bonus_percent' => __('donate.streamer.bonus_percent'),
        ]);
    }
}
