<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAdRequest;
use App\Http\Requests\UpdateAdRequest;
use App\Models\Ad;
use App\Services\AdImageService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class AdController extends Controller
{
    public function __construct(private readonly AdImageService $adImages) {}

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
            $data['title'] = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $data['image'] = $this->adImages->storeUploadedFile($request->file('image'));
        }

        Ad::create($data);

        Cache::forget('frontend_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil ditambahkan.');
    }

    public function edit(Ad $ad)
    {
        return view('admin.ads.edit', compact('ad'));
    }

    public function update(UpdateAdRequest $request, Ad $ad)
    {
        $data = $request->validated();
        $oldImage = $ad->image;

        if ($request->hasFile('image')) {
            $data['title'] = pathinfo($request->file('image')->getClientOriginalName(), PATHINFO_FILENAME);
            $data['image'] = $this->adImages->storeUploadedFile($request->file('image'));
        }

        $ad->update($data);

        if ($oldImage && isset($data['image']) && $oldImage !== $data['image']) {
            Storage::disk('public')->delete($oldImage);
        }

        Cache::forget('frontend_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil diperbarui.');
    }

    public function destroy(Ad $ad)
    {
        if ($ad->image) {
            Storage::disk('public')->delete($ad->image);
        }

        $ad->delete();

        Cache::forget('frontend_ads');

        return redirect()->route('admin.ads.index')->with('success', 'Iklan berhasil dihapus.');
    }
}
