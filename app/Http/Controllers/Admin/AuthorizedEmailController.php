<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuthorizedEmail;
use App\Models\Edition;
use App\Services\AuthorizedEmailCsvImporter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthorizedEmailController extends Controller
{
    public function index(): View
    {
        return view('admin.authorized-emails.index', [
            'authorizedEmails' => AuthorizedEmail::with('edition')->latest()->paginate(50),
            'editions' => Edition::orderByDesc('year')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'edition_id' => ['required', 'exists:editions,id'],
        ]);

        AuthorizedEmail::updateOrCreate(
            ['email' => strtolower($data['email'])],
            [
                'name' => $data['name'] ?? null,
                'edition_id' => $data['edition_id'],
                'source' => AuthorizedEmail::SOURCE_ADMIN_MANUEL,
            ]
        );

        return back()->with('status', "Email ajoute : {$data['email']}");
    }

    public function import(Request $request, AuthorizedEmailCsvImporter $importer): RedirectResponse
    {
        $data = $request->validate([
            'edition_id' => ['required', 'exists:editions,id'],
            'csv' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $edition = Edition::findOrFail($data['edition_id']);
        $result = $importer->import($request->file('csv')->getRealPath(), $edition);

        return back()->with('status', "Import termine : {$result['imported']} importes, {$result['skipped']} ignores.");
    }
}
