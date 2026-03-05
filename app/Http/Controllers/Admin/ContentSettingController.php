<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentSettings;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContentSettingController extends Controller
{
    public function index()
    {
        return Inertia::render('Settings/Content/Index', [
            'settings' => ContentSettings::orderBy('key')->get()
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'key'   => 'required|string|unique:content_settings,key',
            'value' => 'nullable',
            'type'  => 'required|string',
        ]);

        ContentSettings::create($data);

        return back()->with('success', 'Setting created');
    }

    public function update(Request $request, ContentSettings $contentSetting)
    {
        $data = $request->validate([
            'value' => 'nullable',
            'type'  => 'required|string',
        ]);

        $contentSetting->update($data);

        return back()->with('success', 'Setting updated');
    }

    public function destroy(ContentSettings $contentSetting)
    {
        $contentSetting->delete();

        return back()->with('success', 'Setting deleted');
    }
}
