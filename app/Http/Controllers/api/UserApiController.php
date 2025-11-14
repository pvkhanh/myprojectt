<?php

// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Services\Api\UserApiService;
// use Illuminate\Http\Request;

// class UserController extends Controller
// {
//     protected UserApiService $service;

//     public function __construct(UserApiService $service)
//     {
//         $this->service = $service;
//     }

//     public function index()
//     {
//         return response()->json(
//             $this->service->index()
//         );
//     }

//     public function show($id)
//     {
//         return response()->json(
//             $this->service->show($id)
//         );
//     }

//     public function store(Request $request)
//     {
//         return response()->json(
//             $this->service->store($request->all())
//         );
//     }

//     public function update(Request $request, $id)
//     {
//         return response()->json(
//             $this->service->update($id, $request->all())
//         );
//     }

//     public function destroy($id)
//     {
//         return response()->json([
//             'success' => $this->service->destroy($id)
//         ]);
//     }
// }




// namespace App\Http\Controllers\Api;

// use App\Http\Controllers\Controller;
// use App\Services\Api\UserApiService;
// use Illuminate\Http\Request;

// class UserApiController extends Controller
// {
//     protected $service;

//     public function __construct(UserApiService $service)
//     {
//         $this->service = $service;
//     }

//     // GET /api/users
//     public function index()
//     {
//         return response()->json($this->service->getAll());
//     }

//     // GET /api/users/{id}
//     public function show($id)
//     {
//         return response()->json($this->service->getById($id));
//     }

//     // POST /api/users
//     public function store(Request $request)
//     {
//         $data = $request->all();

//         if ($request->hasFile('avatar')) {
//             $data['avatar'] = $request->file('avatar');
//         }

//         $user = $this->service->create($data);

//         return response()->json($user, 201);
//     }

//     // PUT /api/users/{id}
//     public function update(Request $request, $id)
//     {
//         $data = $request->all();

//         if ($request->hasFile('avatar')) {
//             $data['avatar'] = $request->file('avatar');
//         }

//         $user = $this->service->update($id, $data);

//         return response()->json($user);
//     }

//     // DELETE /api/users/{id}
//     public function destroy($id)
//     {
//         $this->service->delete($id);

//         return response()->json(['message' => 'User deleted successfully']);
//     }
// }



namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Services\Api\UserApiService;
use Illuminate\Http\Request;

class UserApiController extends Controller
{
    protected UserApiService $service;

    public function __construct(UserApiService $service)
    {
        $this->service = $service;
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }

    // ------------------- Lấy danh sách người dùng -------------------
    public function index()
    {
        $users = $this->service->getAll();
        return response()->json([
            'success' => true,
            'data' => $users
        ]);
    }

    // ------------------- Lấy chi tiết 1 người dùng -------------------
    public function show($id)
    {
        $user = $this->service->getById($id);
        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    // ------------------- Tạo người dùng mới (dành cho client đăng ký) -------------------
    public function store(UserStoreRequest $request)
    {
        $user = $this->service->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // ------------------- Cập nhật thông tin user -------------------
    public function update(UserUpdateRequest $request, $id)
    {
        $user = $this->service->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // ------------------- Xóa user -------------------
    public function destroy($id)
    {
        $this->service->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully'
        ]);
    }
}
