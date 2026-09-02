<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkshopCode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkshopCodeController extends Controller
{
    public function index(): View
    {
        return view('admin.workshop.codes', [
            'codes' => WorkshopCode::withCount('participants')->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:32', 'regex:/^[A-Za-z0-9 -]+$/'],
            'label' => ['required', 'string', 'max:120'],
        ], [
            'code.regex' => 'Lettres, chiffres, espaces et tirets seulement.',
        ]);

        $code = WorkshopCode::normalize($data['code']);

        if (WorkshopCode::where('code', $code)->exists()) {
            return back()->withInput()->withErrors(['code' => 'Ce code existe déjà.']);
        }

        WorkshopCode::create([
            'code' => $code,
            'label' => $data['label'],
            'workshop_type' => WorkshopCode::TYPE_3X30,
            'is_active' => true,
        ]);

        return back()->with('status', "Code créé : {$code}");
    }

    public function toggle(WorkshopCode $workshopCode): RedirectResponse
    {
        $workshopCode->update(['is_active' => ! $workshopCode->is_active]);

        return back()->with('status', $workshopCode->is_active ? "Code {$workshopCode->code} réactivé." : "Code {$workshopCode->code} désactivé.");
    }
}
