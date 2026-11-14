<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;

class FAQController extends Controller
{
    /**
     * Show FAQ page.
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $faqFile = resource_path('markdown/faq.md');

        return view('faq', [
            'faq' => Str::markdown(file_get_contents($faqFile)),
        ]);
    }
}