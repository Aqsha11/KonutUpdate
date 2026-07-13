<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Models\Ad;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function index()
    {
        $ads = Ad::latest()->paginate(15);

        return view('admin.ads.index', compact('ads'));
    }

    public function create()
    {
        return view('admin.ads.create');
    }

    public function store(StoreAdRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        Ad::create($data);

        Cache::forget('sidebar_ads_top');
        Cache::forget('sidebar_ads_bottom');
        Cache::forget('in_article_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil ditambahkan.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(UpdateAdRequest $request, Ad $ad)
    {
        $data = $request->validated();

        if ($request->hasFile('image')) {
            if ($ad->image) {
                Storage::disk('public')->delete($ad->image);
            }
            $data['image'] = $request->file('image')->store('ads', 'public');
        }

        $data['is_active'] = $request->boolean('is_active');

        $ad->update($data);

        Cache::forget('sidebar_ads_top');
        Cache::forget('sidebar_ads_bottom');
        Cache::forget('in_article_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil diperbarui.');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        Cache::forget('sidebar_ads_top');
        Cache::forget('sidebar_ads_bottom');
        Cache::forget('in_article_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil dihapus.');
    }
}
