<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LegacyLiveChatReader;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class ChatController extends Controller
{
    /** @return Application|Factory|View */
    public function getChat()
    {
        return view('admin.chat.index');
    }

    public function getSettings()
    {
        return view('admin.chat.settings');
    }

    public function postChatConfig(Request $request): RedirectResponse
    {
        $input = $request->validate([
            'log_path' => 'required|string'
        ]);
        Config::write('pw-config.chat_log_path', $input['log_path']);

        return redirect()->route('admin.chat.watch')->with('success', __('admin.configSaved'));
    }

    public function postChatLogs(LegacyLiveChatReader $reader)
    {
        return $reader->read(config('pw-config.chat_log_path'), 750);
    }
}
