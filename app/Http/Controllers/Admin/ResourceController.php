<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Resource;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ResourceController extends Controller
{
    public function index(): View
    {
        return view('admin.resources.index', [
            'resources' => Resource::with('edition')->latest()->paginate(50),
            'editions' => Edition::orderByDesc('year')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.resources.create', [
            'editions' => Edition::orderByDesc('year')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'edition_id' => ['required', 'exists:editions,id'],
            'day' => ['required', 'integer', 'between:1,5'],
            'file' => ['required', 'file', 'mimes:pdf,mp3,wav,ogg,m4a', 'max:512000'],
        ]);

        $file = $request->file('file');
        $storedName = Str::uuid().'.'.$file->getClientOriginalExtension();
        $path = $file->storeAs("ressources/{$data['edition_id']}/{$data['day']}", $storedName, 'public');

        Resource::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'file_path' => $path,
            'original_filename' => $file->getClientOriginalName(),
            'edition_id' => $data['edition_id'],
            'day' => $data['day'],
        ]);

        return redirect()->route('admin.resources.index')->with('status', 'Ressource publiée.');
    }
}
