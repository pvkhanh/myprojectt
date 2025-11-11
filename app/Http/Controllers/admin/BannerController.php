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




namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\BannerRepositoryInterface;
use App\Services\BannerService;
use App\Http\Requests\BannerRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BannerController extends Controller
{
    public function __construct(
        protected BannerRepositoryInterface $bannerRepository,
        protected BannerService $bannerService
    ) {}

    public function index(Request $request)
    {
        try {
            $query = $this->bannerRepository->getModel();

            if ($request->filled('is_active')) $query = $query->where('is_active', $request->is_active);
            if ($request->filled('type')) $query = $query->ofType($request->type);
            if ($request->filled('keyword')) $query = $query->where('title', 'LIKE', "%{$request->keyword}%");

            $banners = $query->orderBy('position', 'asc')->paginate(15);

            $totalBanners = $this->bannerRepository->getModel()->count();
            $activeBanners = $this->bannerRepository->getModel()->where('is_active', 1)->count();
            $scheduledBanners = method_exists($this->bannerRepository->getModel(), 'scheduled')
                ? $this->bannerRepository->getModel()->scheduled()->count()
                : 0;
            $visibleBanners = method_exists($this->bannerRepository->getModel(), 'visible')
                ? $this->bannerRepository->getModel()->visible()->count()
                : 0;

            return view('admin.banners.index', compact('banners','totalBanners','activeBanners','scheduledBanners','visibleBanners'));
        } catch (\Throwable $e) {
            Log::error("BannerController@index error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Erreur lors du chargement des bannières.');
        }
    }

    public function create()
    {
        try {
            return view('admin.banners.create');
        } catch (\Throwable $e) {
            Log::error("BannerController@create error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Impossible de charger la page de création.');
        }
    }

    public function store(BannerRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) $data['image'] = $request->file('image');

            $this->bannerService->createBanner($data);
            return redirect()->route('admin.banners.index')->with('success','Bannière créée avec succès!');
        } catch (\Throwable $e) {
            Log::error("BannerController@store error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->withInput()->with('error','Erreur lors de la création de la bannière.');
        }
    }

    public function edit($id)
    {
        try {
            $banner = $this->bannerRepository->findOrFail($id);
            return view('admin.banners.edit', compact('banner'));
        } catch (\Throwable $e) {
            Log::error("BannerController@edit error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Impossible de charger la page d\'édition.');
        }
    }

    public function update(BannerRequest $request, $id)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('image')) $data['image'] = $request->file('image');

            $this->bannerService->updateBanner($id, $data);
            return redirect()->route('admin.banners.index')->with('success','Bannière mise à jour avec succès!');
        } catch (\Throwable $e) {
            Log::error("BannerController@update error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->withInput()->with('error','Erreur lors de la mise à jour de la bannière.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->bannerService->deleteBanner($id);
            return redirect()->route('admin.banners.index')->with('success','Bannière supprimée avec succès!');
        } catch (\Throwable $e) {
            Log::error("BannerController@destroy error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Erreur lors de la suppression de la bannière.');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate(['ids'=>'required|array']);
            $result = $this->bannerService->bulkDelete($request->ids);
            $message = $result['deleted']>0
                ? "Supprimé {$result['deleted']} bannières." . (!empty($result['errors']) ? ' Erreurs: '.implode(', ',$result['errors']):'')
                : implode(', ',$result['errors']);

            return response()->json(['success'=>$result['deleted']>0,'message'=>$message]);
        } catch (\Throwable $e) {
            Log::error("BannerController@bulkDelete error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors de la suppression en masse.']);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $newStatus = $this->bannerService->toggleStatus($id);
            return response()->json(['success'=>true,'is_active'=>$newStatus]);
        } catch (\Throwable $e) {
            Log::error("BannerController@toggleStatus error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors du changement de statut.']);
        }
    }

    public function updatePositions(Request $request)
    {
        try {
            $request->validate(['positions'=>'required|array']);
            $this->bannerService->updatePositions($request->positions);
            return response()->json(['success'=>true,'message'=>'Positions mises à jour!']);
        } catch (\Throwable $e) {
            Log::error("BannerController@updatePositions error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors de la mise à jour des positions.']);
        }
    }
}