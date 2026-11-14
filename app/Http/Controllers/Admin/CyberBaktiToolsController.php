<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Services\LegacyPanelInspector;
class CyberBaktiToolsController extends Controller
{
    public function index(LegacyPanelInspector $inspector)
    {
        return view('admin.cyberbakti.tools', ['data' => $inspector->snapshot()]);
    }
}
