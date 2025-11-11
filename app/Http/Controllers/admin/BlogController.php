<?php

// ==========================================
// 📝 BLOG CONTROLLER
// ==========================================

// namespace App\Http\Controllers\Admin;

// use App\Http\Controllers\Controller;
// use App\Repositories\Contracts\BlogRepositoryInterface;
// use App\Repositories\Contracts\CategoryRepositoryInterface;
// use App\Enums\BlogStatus;
// use Illuminate\Http\Request;
// use Illuminate\Support\Str;

// class BlogController extends Controller
// {
//     protected $blogRepository;
//     protected $categoryRepository;

//     public function __construct(
//         BlogRepositoryInterface $blogRepository,
//         CategoryRepositoryInterface $categoryRepository
//     ) {
//         $this->blogRepository = $blogRepository;
//         $this->categoryRepository = $categoryRepository;
//     }

//     public function index(Request $request)
//     {
//         $query = $this->blogRepository->getModel();

//         // Filters
//         if ($request->filled('status')) {
//             $query = $query->where('status', $request->status);
//         }

//         if ($request->filled('category_id')) {
//             $query = $query->whereHas('categories', function ($q) use ($request) {
//                 $q->where('categories.id', $request->category_id);
//             });
//         }

//         if ($request->filled('author_id')) {
//             $query = $query->where('author_id', $request->author_id);
//         }

//         if ($request->filled('keyword')) {
//             $query = $query->search($request->keyword);
//         }

//         // Sorting
//         switch ($request->get('sort_by', 'latest')) {
//             case 'oldest':
//                 $query = $query->oldest();
//                 break;
//             case 'title':
//                 $query = $query->orderBy('title', 'asc');
//                 break;
//             default:
//                 $query = $query->latest();
//         }

//         $blogs = $query->with(['author', 'categories', 'primaryImage'])->paginate(15);

//         // Statistics
//         $totalBlogs = $this->blogRepository->getModel()->count();
//         $publishedBlogs = $this->blogRepository->getModel()->published()->count();
//         $draftBlogs = $this->blogRepository->getModel()->draft()->count();
//         $totalViews = $this->blogRepository->getModel()->sum('views_count');

//         $statuses = BlogStatus::cases();
//         $categories = $this->categoryRepository->all();
//         $authors = \App\Models\User::whereHas('blogs')->get();

//         return view('admin.blogs.index', compact(
//             'blogs',
//             'totalBlogs',
//             'publishedBlogs',
//             'draftBlogs',
//             'totalViews',
//             'statuses',
//             'categories',
//             'authors'
//         ));
//     }

//     public function create()
//     {
//         $categories = $this->categoryRepository->all();
//         $statuses = BlogStatus::cases();

//         return view('admin.blogs.create', compact('categories', 'statuses'));
//     }

//     public function store(Request $request)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:255',
//             'slug' => 'nullable|string|unique:blogs,slug',
//             'content' => 'required|string',
//             'status' => 'required|in:' . implode(',', BlogStatus::values()),
//             'categories' => 'nullable|array',
//             'categories.*' => 'exists:categories,id',
//             'primary_image' => 'nullable|image|max:2048',
//             'meta_title' => 'nullable|string|max:255',
//             'meta_description' => 'nullable|string|max:500',
//         ]);

//         $validated['author_id'] = auth()->id();
//         $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

//         $blog = $this->blogRepository->create($validated);

//         // Attach categories
//         if ($request->has('categories')) {
//             $blog->categories()->sync($request->categories);
//         }

//         // Handle image upload
//         if ($request->hasFile('primary_image')) {
//             $path = $request->file('primary_image')->store('blogs', 'public');
//             $image = \App\Models\Image::create(['path' => $path]);
//             $blog->images()->attach($image->id, ['is_main' => true]);
//         }

//         return redirect()
//             ->route('admin.blogs.index')
//             ->with('success', 'Blog créé avec succès!');
//     }

//     public function show($id)
//     {
//         $blog = $this->blogRepository->findOrFail($id);
//         $blog->load(['author', 'categories', 'images']);

//         return view('admin.blogs.show', compact('blog'));
//     }

//     public function edit($id)
//     {
//         $blog = $this->blogRepository->findOrFail($id);
//         $blog->load(['categories', 'images']);
        
//         $categories = $this->categoryRepository->all();
//         $statuses = BlogStatus::cases();

//         return view('admin.blogs.edit', compact('blog', 'categories', 'statuses'));
//     }

//     public function update(Request $request, $id)
//     {
//         $validated = $request->validate([
//             'title' => 'required|string|max:255',
//             'slug' => 'nullable|string|unique:blogs,slug,' . $id,
//             'content' => 'required|string',
//             'status' => 'required|in:' . implode(',', BlogStatus::values()),
//             'categories' => 'nullable|array',
//             'categories.*' => 'exists:categories,id',
//             'primary_image' => 'nullable|image|max:2048',
//         ]);

//         $validated['slug'] = $validated['slug'] ?? Str::slug($validated['title']);

//         $blog = $this->blogRepository->update($id, $validated);

//         // Update categories
//         if ($request->has('categories')) {
//             $blog->categories()->sync($request->categories);
//         }

//         // Handle new image
//         if ($request->hasFile('primary_image')) {
//             $path = $request->file('primary_image')->store('blogs', 'public');
//             $image = \App\Models\Image::create(['path' => $path]);
            
//             // Remove old primary image
//             $blog->images()->updateExistingPivot(
//                 $blog->images()->wherePivot('is_main', true)->pluck('id')->toArray(),
//                 ['is_main' => false]
//             );
            
//             $blog->images()->attach($image->id, ['is_main' => true]);
//         }

//         return redirect()
//             ->route('admin.blogs.index')
//             ->with('success', 'Blog mis à jour avec succès!');
//     }

//     public function destroy($id)
//     {
//         $this->blogRepository->delete($id);

//         return redirect()
//             ->route('admin.blogs.index')
//             ->with('success', 'Blog supprimé avec succès!');
//     }

//     public function bulkDelete(Request $request)
//     {
//         $request->validate(['ids' => 'required|array']);

//         foreach ($request->ids as $id) {
//             $this->blogRepository->delete($id);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => count($request->ids) . ' blogs supprimés avec succès!'
//         ]);
//     }

//     public function bulkUpdateStatus(Request $request)
//     {
//         $request->validate([
//             'ids' => 'required|array',
//             'status' => 'required|in:' . implode(',', BlogStatus::values())
//         ]);

//         foreach ($request->ids as $id) {
//             $this->blogRepository->update($id, ['status' => $request->status]);
//         }

//         return response()->json([
//             'success' => true,
//             'message' => count($request->ids) . ' blogs mis à jour!'
//         ]);
//     }
// }





namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Contracts\BlogRepositoryInterface;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use App\Services\BlogService;
use App\Http\Requests\BlogRequest;
use App\Enums\BlogStatus;
use Illuminate\Support\Facades\Log;

class BlogController extends Controller
{
    public function __construct(
        protected BlogRepositoryInterface $blogRepository,
        protected CategoryRepositoryInterface $categoryRepository,
        protected BlogService $blogService
    ) {}

    public function index(Request $request)
    {
        try {
            $query = $this->blogRepository->getModel();

            if ($request->filled('status')) $query = $query->where('status', $request->status);
            if ($request->filled('category_id')) $query = $query->whereHas('categories', fn($q) => $q->where('categories.id', $request->category_id));
            if ($request->filled('author_id')) $query = $query->where('author_id', $request->author_id);
            if ($request->filled('keyword')) $query = $query->search($request->keyword);

            switch ($request->get('sort_by', 'latest')) {
                case 'oldest': $query = $query->oldest(); break;
                case 'title': $query = $query->orderBy('title', 'asc'); break;
                default: $query = $query->latest();
            }

            $blogs = $query->with(['author','categories','primaryImage'])->paginate(15);
            $totalBlogs = $this->blogRepository->getModel()->count();
            $publishedBlogs = $this->blogRepository->getModel()->published()->count();
            $draftBlogs = $this->blogRepository->getModel()->draft()->count();
            $totalViews = $this->blogRepository->getModel()->sum('views_count');

            $statuses = BlogStatus::cases();
            $categories = $this->categoryRepository->all();
            $authors = \App\Models\User::whereHas('blogs')->get();

            return view('admin.blogs.index', compact(
                'blogs','totalBlogs','publishedBlogs','draftBlogs','totalViews','statuses','categories','authors'
            ));
        } catch (\Throwable $e) {
            Log::error("BlogController@index error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error', 'Erreur lors du chargement des blogs.');
        }
    }

    public function create()
    {
        try {
            $categories = $this->categoryRepository->all();
            $statuses = BlogStatus::cases();
            return view('admin.blogs.create', compact('categories','statuses'));
        } catch (\Throwable $e) {
            Log::error("BlogController@create error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error', 'Impossible de charger la page de création.');
        }
    }

    public function store(BlogRequest $request)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('primary_image')) $data['primary_image'] = $request->file('primary_image');

            $this->blogService->createBlog($data);
            return redirect()->route('admin.blogs.index')->with('success','Blog créé avec succès!');
        } catch (\Throwable $e) {
            Log::error("BlogController@store error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->withInput()->with('error','Erreur lors de la création du blog.');
        }
    }

    public function edit($id)
    {
        try {
            $blog = $this->blogRepository->findOrFail($id);
            $blog->load(['categories','images']);
            $categories = $this->categoryRepository->all();
            $statuses = BlogStatus::cases();
            return view('admin.blogs.edit', compact('blog','categories','statuses'));
        } catch (\Throwable $e) {
            Log::error("BlogController@edit error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Impossible de charger la page d\'édition.');
        }
    }

    public function update(BlogRequest $request, $id)
    {
        try {
            $data = $request->validated();
            if ($request->hasFile('primary_image')) $data['primary_image'] = $request->file('primary_image');

            $this->blogService->updateBlog($id, $data);
            return redirect()->route('admin.blogs.index')->with('success','Blog mis à jour avec succès!');
        } catch (\Throwable $e) {
            Log::error("BlogController@update error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->withInput()->with('error','Erreur lors de la mise à jour du blog.');
        }
    }

    public function show($id)
    {
        try {
            $blog = $this->blogRepository->findOrFail($id);
            $blog->load(['author','categories','images']);
            return view('admin.blogs.show', compact('blog'));
        } catch (\Throwable $e) {
            Log::error("BlogController@show error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Impossible de charger le blog.');
        }
    }

    public function destroy($id)
    {
        try {
            $this->blogService->deleteBlog($id);
            return redirect()->route('admin.blogs.index')->with('success','Blog supprimé avec succès!');
        } catch (\Throwable $e) {
            Log::error("BlogController@destroy error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return back()->with('error','Erreur lors de la suppression du blog.');
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            $request->validate(['ids'=>'required|array']);
            $result = $this->blogService->bulkDelete($request->ids);
            $message = $result['deleted'] > 0
                ? "Supprimé {$result['deleted']} blogs." . (!empty($result['errors']) ? ' Erreurs: '.implode(', ',$result['errors']):'')
                : implode(', ',$result['errors']);

            return response()->json(['success'=>$result['deleted']>0,'message'=>$message]);
        } catch (\Throwable $e) {
            Log::error("BlogController@bulkDelete error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors de la suppression en masse.']);
        }
    }

    public function bulkUpdateStatus(Request $request)
    {
        try {
            $request->validate([
                'ids'=>'required|array',
                'status'=>'required|in:'.implode(',',BlogStatus::values())
            ]);

            $result = $this->blogService->bulkUpdateStatus($request->ids,$request->status);
            $message = $result['updated'] > 0
                ? "Mis à jour {$result['updated']} blogs." . (!empty($result['errors']) ? ' Erreurs: '.implode(', ',$result['errors']):'')
                : implode(', ',$result['errors']);

            return response()->json(['success'=>$result['updated']>0,'message'=>$message]);
        } catch (\Throwable $e) {
            Log::error("BlogController@bulkUpdateStatus error: ".$e->getMessage(), ['trace'=>$e->getTraceAsString()]);
            return response()->json(['success'=>false,'message'=>'Erreur lors de la mise à jour du statut en masse.']);
        }
    }
}