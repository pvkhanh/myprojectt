<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Banner::with('image')->latest();

        // Search filter
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('url', 'LIKE', "%{$request->keyword}%");
            });
        }

        // Status filter
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Pagination
        $perPage = $request->get('per_page', 15);
        $banners = $query->paginate($perPage)->withQueryString();

        // Statistics
        $totalBanners = Banner::count();
        $activeBanners = Banner::where('is_active', true)->count();
        $scheduledBanners = Banner::whereNotNull('start_at')->orWhereNotNull('end_at')->count();
        $visibleBanners = Banner::active()->scheduled()->count();

        return view('admin.banners.index', compact(
            'banners',
            'totalBanners',
            'activeBanners',
            'scheduledBanners',
            'visibleBanners'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'title' => 'required|string|max:255',
    //         'url' => 'nullable|url|max:500',
    //         'type' => 'nullable|string|in:hero,sidebar,popup,footer',
    //         'position' => 'nullable|integer|min:0',
    //         'is_active' => 'nullable|boolean',
    //         'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    //         'start_at' => 'nullable|date',
    //         'end_at' => 'nullable|date|after_or_equal:start_at',
    //     ]);

    //     $validated['is_active'] = $request->has('is_active');

    //     // Handle image upload
    //     if ($request->hasFile('image_file')) {
    //         $image = $this->uploadImage($request->file('image_file'));
    //         $validated['image_id'] = $image->id;
    //     }

    //     Banner::create($validated);

    //     return redirect()->route('admin.banners.index')
    //         ->with('success', 'Banner đã được tạo thành công!');
    // }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
            'type' => 'nullable|string|in:hero,sidebar,popup,footer',
            'position' => 'nullable|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        // Chuyển checkbox thành boolean
        $validated['is_active'] = $request->has('is_active');

        // Upload ảnh nếu có
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('banners', 'public');

            $image = Image::create([
                'path' => $path,
                'type' => 'banner',
                'alt_text' => null,
                'is_active' => true,
            ]);

            $validated['image_id'] = $image->id;
        }

        Banner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được tạo thành công!');
    }



    /**
     * Display the specified resource.
     */
    public function show(Banner $banner)
    {
        $banner->load('image');
        return view('admin.banners.show', compact('banner'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        $banner->load('image');
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'nullable|url|max:500',
            'type' => 'nullable|string|in:hero,sidebar,popup,footer',
            'position' => 'nullable|integer|min:0',
            'image_file' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'start_at' => 'nullable|date',
            'end_at' => 'nullable|date|after_or_equal:start_at',
        ]);

        // Chuyển checkbox thành boolean
        $validated['is_active'] = $request->has('is_active');

        // Upload ảnh mới nếu có
        if ($request->hasFile('image_file')) {
            $file = $request->file('image_file');
            $path = $file->store('banners', 'public');

            // Nếu banner đã có ảnh, xóa ảnh cũ
            if ($banner->image) {
                Storage::disk('public')->delete($banner->image->path);
                $banner->image->delete();
            }

            // Lưu ảnh mới vào bảng images
            $image = Image::create([
                'path' => $path,
                'type' => 'banner',
                'alt_text' => null,
                'is_active' => true,
            ]);

            $validated['image_id'] = $image->id;
        }

        // Cập nhật banner
        $banner->update($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được cập nhật thành công!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        // Delete associated image
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image->path);
            $banner->image->delete();
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner đã được xóa thành công!');
    }

    /**
     * Toggle banner status
     */
    public function toggleStatus(Banner $banner)
    {
        $banner->update(['is_active' => !$banner->is_active]);

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đã được cập nhật!',
            'is_active' => $banner->is_active
        ]);
    }

    /**
     * Update positions for drag & drop
     */
    public function updatePositions(Request $request)
    {
        $positions = $request->input('positions', []);

        foreach ($positions as $id => $position) {
            Banner::where('id', $id)->update(['position' => $position]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Vị trí đã được cập nhật!'
        ]);
    }

    /**
     * Bulk delete banners
     */
    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        $action = $request->input('action');

        if (empty($ids)) {
            return response()->json([
                'success' => false,
                'message' => 'Không có banner nào được chọn!'
            ]);
        }

        $banners = Banner::whereIn('id', $ids)->get();

        switch ($action) {
            case 'activate':
                Banner::whereIn('id', $ids)->update(['is_active' => true]);
                $message = 'Đã kích hoạt ' . count($ids) . ' banner!';
                break;

            case 'deactivate':
                Banner::whereIn('id', $ids)->update(['is_active' => false]);
                $message = 'Đã tạm dừng ' . count($ids) . ' banner!';
                break;

            case 'delete':
                foreach ($banners as $banner) {
                    if ($banner->image) {
                        Storage::disk('public')->delete($banner->image->path);
                        $banner->image->delete();
                    }
                    $banner->delete();
                }
                $message = 'Đã xóa ' . count($ids) . ' banner!';
                break;

            default:
                return response()->json([
                    'success' => false,
                    'message' => 'Hành động không hợp lệ!'
                ]);
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Upload image helper
     */
    private function uploadImage($file)
    {
        $path = $file->store('banners', 'public');

        return Image::create([
            'path' => $path,
            'type' => 'banner',
            'alt_text' => null,
            'is_active' => true,
        ]);
    }
}
