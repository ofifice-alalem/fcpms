<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSiteRequest;
use App\Models\Site;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SiteController extends Controller
{
    public function index(): Response
    {
        $sites = Site::orderBy('created_at', 'desc')->get();

        return Inertia::render('HR/Sites/Index', [
            'sites' => $sites,
        ]);
    }

    public function show(int $id): Response
    {
        $site = Site::with('visits')->findOrFail($id);

        return Inertia::render('HR/Sites/Show', [
            'site' => $site,
        ]);
    }

    public function store(StoreSiteRequest $request)
    {
        Site::create($request->validated());

        return redirect()->back()->with('success', 'تم إضافة الموقع الميداني بنجاح.');
    }

    public function update(Request $request, int $id)
    {
        $site = Site::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:50|unique:sites,code,' . $id,
            'location' => 'nullable|string|max:255',
            'status' => 'required|in:active,inactive',
        ]);

        $site->update($request->all());

        return redirect()->back()->with('success', 'تم تحديث بيانات الموقع بنجاح.');
    }
}
