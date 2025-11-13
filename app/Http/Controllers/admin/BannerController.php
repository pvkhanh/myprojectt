<?php


// ==========================================
// 🎨 BANNER CONTROLLER  bản dùng tạm
// ==========================================

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\BannerRepositoryInterface;
// use Illuminate\Http\Request;

// class BannerController extends Controller
// {
//     protected $bannerRepository;

//     public function __construct(BannerRepositoryInterface $bannerRepository)
//     {
//         $this->bannerRepository = $bannerRepository;
//     }

//     public function index(Request $request)
//     {
//         $query = $this->bannerRepository->getModel();

//         // ===== Filters =====
//         if ($request->filled('is_active')) {
//             $query = $query->where('is_active', $request->is_active);
//         }

//         if ($request->filled('type')) {
//             $query = $query->ofType($request->type);
//         }

//         if ($request->filled('keyword')) {
//             $query = $query->where('title', 'LIKE', "%{$request->keyword}%");
//         }

//         // ===== Paginate banners =====
//         $banners = $query->orderBy('position', 'asc')->paginate(15);

//         // ===== Statistics =====
//         $totalBanners = $this->bannerRepository->getModel()->count();
//         $activeBanners = $this->bannerRepository->getModel()->where('is_active', 1)->count();

//         // Scheduled: banners with start_at <= now <= end_at
//         $scheduledBanners = 0;
//         if (method_exists($this->bannerRepository->getModel(), 'scheduled')) {
//             $scheduledBanners = $this->bannerRepository->getModel()->scheduled()->count();
//         }

//         // Visible: active + scheduled
//         $visibleBanners = 0;
//         if (method_exists($this->bannerRepository->getModel(), 'visible')) {
//             $visibleBanners = $this->bannerRepository->getModel()->visible()->count();
//         }

//         return view('admin.banners.index', compact(
//             'banners',
//             'totalBanners',
//             'activeBanners',
//             'scheduledBanners',
//             'visibleBanners'
//         ));
//     }

//     public function create()
//     {
//         return view('admin.banners.create');
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:255',
//             'url' => 'nullable|url|max:500',
//             'is_active' => 'boolean',
//             'position' => 'nullable|integer',
//             'start_at' => 'nullable|date',
//             'end_at' => 'nullable|date|after:start_at',
//             'type' => 'nullable|string|max:50',
//             'image' => 'required|image|max:2048',
//         ]);

//         $validated['is_active'] = $request->has('is_active');

//         // Upload image
//         if ($request->hasFile('image')) {
//             $path = $request->file('image')->store('banners', 'public');
//             $validated['image_path'] = $path;
//         }

//         $this->bannerRepository->create($validated);

//         return redirect()
//             ->route('admin.banners.index')
//             ->with('success', 'Bannière créée avec succès!');
//     }

//     public function edit($id)
//     {
//         $banner = $this->bannerRepository->findOrFail($id);

//         return view('admin.banners.edit', compact('banner'));
//     }

//     public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:255',
//             'url' => 'nullable|url|max:500',
//             'is_active' => 'boolean',
//             'position' => 'nullable|integer',
//             'start_at' => 'nullable|date',
//             'end_at' => 'nullable|date|after:start_at',
//             'type' => 'nullable|string|max:50',
//             'image' => 'nullable|image|max:2048',
//         ]);

//         $validated['is_active'] = $request->has('is_active');

//         // Upload new image if provided
//         if ($request->hasFile('image')) {
//             $path = $request->file('image')->store('banners', 'public');
//             $validated['image_path'] = $path;
//         }

//         $this->bannerRepository->update($id, $validated);

//         return redirect()
//             ->route('admin.banners.index')
//             ->with('success', 'Bannière mise à jour avec succès!');
//     }

//     public function destroy($id)
//     {
//         $this->bannerRepository->delete($id);

//         return redirect()
//             ->route('admin.banners.index')
//             ->with('success', 'Bannière supprimée avec succès!');
//     }

//     public function updatePositions(Request $request)
//     {
//         $request->validate(['positions' => 'required|array']);

//         $this->bannerRepository->updatePositions($request->positions);

//         return response()->json([
//             'success' => true,
//             'message' => 'Positions mises à jour!'
//         ]);
//     }

//     public function bulkDelete(Request $request)
//     {
//         $request->validate(['ids' => 'required|array']);

//         foreach ($request->ids as $id) {
//             $this->bannerRepository->delete($id);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => count($request->ids) . ' bannières supprimées!'
//         ]);
//     }

//     public function toggleStatus($id)
//     {
//         $banner = $this->bannerRepository->findOrFail($id);

//         $this->bannerRepository->update($id, [
//             'is_active' => !$banner->is_active
//         ]);

//         return response()->json([
//             'success' => true,
//             'is_active' => !$banner->is_active
//         ]);
//     }
// }



// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\BannerRepositoryInterface;
// use App\Services\BannerService;
// use App\Http\Requests\BannerRequest;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Log;
// use App\Models\Banner;

// class BannerController extends Controller
// {
//     protected BannerRepositoryInterface $bannerRepository;
//     protected BannerService $bannerService;

//     public function __construct(
//         BannerRepositoryInterface $bannerRepository,
//         BannerService $bannerService
//     ) {
//         $this->bannerRepository = $bannerRepository;
//         $this->bannerService = $bannerService;
//     }

//     // ================== DANH SÁCH BANNER ==================
//     // public function index(Request $request)
//     // {
//     //     try {
//     //         $query = $this->bannerRepository->getModel();

//     //         if ($request->filled('is_active')) $query->where('is_active', $request->is_active);
//     //         if ($request->filled('type')) $query->ofType($request->type);
//     //         if ($request->filled('keyword')) $query->where('title', 'LIKE', "%{$request->keyword}%");

//     //         $banners = $query->orderBy('position', 'asc')->paginate(15);

//     //         $totalBanners = $this->bannerRepository->getModel()->count();
//     //         $activeBanners = $this->bannerRepository->getModel()->where('is_active', 1)->count();

//     //         return view('admin.banners.index', compact(
//     //             'banners',
//     //             'totalBanners',
//     //             'activeBanners'
//     //         ));
//     //     } catch (\Throwable $e) {
//     //         Log::error("BannerController@index error: " . $e->getMessage());
//     //         return back()->with('error', 'Đã xảy ra lỗi khi tải danh sách banner.');
//     //     }
//     // }

//     public function index(Request $request)
// {
//     $bannersQuery = Banner::query();

//     // Filter
//     if ($request->filled('keyword')) {
//         $bannersQuery->where('title', 'like', '%' . $request->keyword . '%');
//     }
//     if ($request->filled('is_active')) {
//         $bannersQuery->where('is_active', $request->is_active);
//     }
//     if ($request->filled('type')) {
//         $bannersQuery->where('type', $request->type);
//     }

//     $banners = $bannersQuery->orderBy('position', 'asc')->paginate(12);

//     // Statistics
//     $totalBanners = Banner::count();
//     $activeBanners = Banner::where('is_active', 1)->count();
//     $scheduledBanners = Banner::whereNotNull('start_at')
//                               ->orWhereNotNull('end_at')
//                               ->count();
//     $visibleBanners = Banner::where('is_active', 1)
//                              ->where(function($q){
//                                  $q->whereNull('start_at')->orWhere('start_at', '<=', now());
//                              })
//                              ->where(function($q){
//                                  $q->whereNull('end_at')->orWhere('end_at', '>=', now());
//                              })
//                              ->count();

//     return view('admin.banners.index', compact(
//         'banners',
//         'totalBanners',
//         'activeBanners',
//         'scheduledBanners',
//         'visibleBanners'
//     ));
// }

//     // ================== TẠO MỚI BANNER ==================
//     public function create()
//     {
//         try {
//             return view('admin.banners.create');
//         } catch (\Throwable $e) {
//             Log::error("BannerController@create error: " . $e->getMessage());
//             return back()->with('error', 'Không thể tải trang tạo banner.');
//         }
//     }

//     public function store(BannerRequest $request)
//     {
//         try {
//             $data = $request->validated();

//             // Upload file mới
//             if ($request->hasFile('image')) {
//                 $data['image_file'] = $request->file('image');
//             }

//             // Chọn ảnh từ thư viện
//             if ($request->filled('image_id')) {
//                 $data['image_id'] = $request->input('image_id');
//             }

//             $this->bannerService->store($data);

//             return redirect()->route('admin.banners.index')
//                 ->with('success', 'Banner đã được tạo thành công!');
//         } catch (\Throwable $e) {
//             Log::error("BannerController@store error: " . $e->getMessage());
//             return back()->withInput()->with('error', 'Đã xảy ra lỗi khi tạo banner.');
//         }
//     }

//     // ================== SỬA BANNER ==================
//     public function edit($id)
//     {
//         try {
//             $banner = $this->bannerRepository->findOrFail($id);
//             return view('admin.banners.edit', compact('banner'));
//         } catch (\Throwable $e) {
//             Log::error("BannerController@edit error: " . $e->getMessage());
//             return back()->with('error', 'Không thể tải trang chỉnh sửa banner.');
//         }
//     }

//     public function update(BannerRequest $request, $id)
//     {
//         try {
//             $data = $request->validated();

//             if ($request->hasFile('image')) {
//                 $data['image_file'] = $request->file('image');
//             }

//             if ($request->filled('image_id')) {
//                 $data['image_id'] = $request->input('image_id');
//             }

//             $this->bannerService->update($id, $data);

//             return redirect()->route('admin.banners.index')
//                 ->with('success', 'Banner đã được cập nhật thành công!');
//         } catch (\Throwable $e) {
//             Log::error("BannerController@update error: " . $e->getMessage());
//             return back()->withInput()->with('error', 'Đã xảy ra lỗi khi cập nhật banner.');
//         }
//     }

//     // ================== XÓA BANNER ==================
//     public function destroy($id)
//     {
//         try {
//             $this->bannerService->delete($id);
//             return redirect()->route('admin.banners.index')
//                 ->with('success', 'Banner đã được xóa thành công!');
//         } catch (\Throwable $e) {
//             Log::error("BannerController@destroy error: " . $e->getMessage());
//             return back()->with('error', 'Đã xảy ra lỗi khi xóa banner.');
//         }
//     }

//     // ================== BULK DELETE ==================
//     public function bulkDelete(Request $request)
//     {
//         try {
//             $request->validate(['ids' => 'required|array']);
//             $result = $this->bannerService->bulkDelete($request->ids);
//             $message = $result['deleted'] > 0
//                 ? "Đã xóa {$result['deleted']} banner." . (!empty($result['errors']) ? ' Lỗi: ' . implode(', ', $result['errors']) : '')
//                 : implode(', ', $result['errors']);

//             return response()->json(['success' => $result['deleted'] > 0, 'message' => $message]);
//         } catch (\Throwable $e) {
//             Log::error("BannerController@bulkDelete error: " . $e->getMessage());
//             return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi xóa nhiều banner.']);
//         }
//     }

//     // ================== TOGGLE STATUS ==================
//     public function toggleStatus($id)
//     {
//         try {
//             $newStatus = $this->bannerService->toggleStatus($id);
//             return response()->json(['success' => true, 'is_active' => $newStatus]);
//         } catch (\Throwable $e) {
//             Log::error("BannerController@toggleStatus error: " . $e->getMessage());
//             return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi thay đổi trạng thái banner.']);
//         }
//     }

//     // ================== CẬP NHẬT VỊ TRÍ ==================
//     public function updatePositions(Request $request)
//     {
//         try {
//             $request->validate(['positions' => 'required|array']);
//             $this->bannerService->updatePositions($request->positions);
//             return response()->json(['success' => true, 'message' => 'Cập nhật vị trí banner thành công!']);
//         } catch (\Throwable $e) {
//             Log::error("BannerController@updatePositions error: " . $e->getMessage());
//             return response()->json(['success' => false, 'message' => 'Đã xảy ra lỗi khi cập nhật vị trí banner.']);
//         }
//     }
// }




namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BannerService;
use Exception;

class BannerController extends Controller
{
    protected BannerService $bannerService;

    public function __construct(BannerService $bannerService)
    {
        $this->bannerService = $bannerService;
    }

    // Hiển thị danh sách banner
    public function index(Request $request)
{
    // Lấy query Builder
    $query = $this->bannerService->getAll();

    // Lọc theo keyword
    if ($keyword = $request->keyword) {
        $query->where('title', 'like', "%{$keyword}%");
    }

    // Lọc theo trạng thái
    if ($request->has('is_active') && $request->is_active !== '') {
        $query->where('is_active', $request->is_active);
    }

    // Lọc theo type
    if ($type = $request->type) {
        $query->where('type', $type);
    }

    // Phân trang
    $banners = $query->orderBy('position')->paginate(12)->withQueryString();

    // Thống kê
    $totalBanners     = $this->bannerService->getAll()->count();
    $activeBanners    = $this->bannerService->getActive()->count();
    $scheduledBanners = $this->bannerService->scheduled()->count();
    $visibleBanners   = $this->bannerService->visible()->count();

    return view('admin.banners.index', compact(
        'banners',
        'totalBanners',
        'activeBanners',
        'scheduledBanners',
        'visibleBanners'
    ));
}




    // Form tạo banner
    public function create()
    {
        $data = $this->bannerService->create();
        return view('admin.banners.create', $data);
    }

    // Lưu banner mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'url'       => 'nullable|url|max:255',
            'type'      => 'nullable|string|max:50',
            'position'  => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image_id'  => 'nullable|exists:images,id',
            'start_at'  => 'nullable|date',
            'end_at'    => 'nullable|date|after_or_equal:start_at',
            'image_file' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        // Thêm file upload vào validated
        $validated['image_file'] = $request->file('image_file');

        try {
            $this->bannerService->store($validated);

            return redirect()->route('admin.banners.index')
                ->with('success', 'Tạo banner mới thành công!');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    // Form chỉnh sửa banner
    public function edit($id)
    {
        $banner = $this->bannerService->findOrFail($id);
        $data = $this->bannerService->create(); // để lấy image library
        return view('admin.banners.edit', array_merge($data, compact('banner')));
    }

    // Cập nhật banner
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title'     => 'required|string|max:255',
            'url'       => 'nullable|url|max:255',
            'type'      => 'nullable|string|max:50',
            'position'  => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
            'image_id'  => 'nullable|exists:images,id',
            'start_at'  => 'nullable|date',
            'end_at'    => 'nullable|date|after_or_equal:start_at',
            'image_file' => 'nullable|file|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['image_file'] = $request->file('image_file');

        try {
            $this->bannerService->update($id, $validated);

            return redirect()->route('admin.banners.index')
                ->with('success', 'Cập nhật banner thành công!');
        } catch (Exception $e) {
            return back()->withInput()
                ->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }

    // Xóa banner
    public function destroy($id)
    {
        try {
            $this->bannerService->delete($id);
            return redirect()->route('admin.banners.index')
                ->with('success', 'Xóa banner thành công!');
        } catch (Exception $e) {
            return back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
        }
    }
}
