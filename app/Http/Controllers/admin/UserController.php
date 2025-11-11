<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // =================== INDEX ===================
    public function index(Request $request)
    {
        return $this->userService->index($request);
    }

    // =================== CREATE & STORE ===================
    public function create()
    {
        return $this->userService->create();
    }

    public function store(UserRequest $request)
    {
        return $this->userService->store($request);
    }

    // =================== EDIT & UPDATE ===================
    public function edit($id)
    {
        return $this->userService->edit($id);
    }

    public function update(UserRequest $request, $id)
    {
        return $this->userService->update($request, $id);
    }

    // =================== SHOW ===================
    public function show($id)
    {
        return $this->userService->show($id);
    }

    // =================== DELETE & TRASH ===================
    public function destroy($id)
    {
        return $this->userService->destroy($id);
    }

    public function trashed()
    {
        return $this->userService->trashed();
    }

    public function restore($id)
    {
        return $this->userService->restore($id);
    }

    public function restoreAll()
    {
        return $this->userService->restoreAll();
    }

    public function forceDelete($id)
    {
        return $this->userService->forceDelete($id);
    }

    public function forceDeleteSelected(Request $request)
    {
        return $this->userService->forceDeleteSelected($request);
    }

    // =================== TOGGLE STATUS ===================
    public function toggleStatus($id, Request $request)
    {
        return $this->userService->toggleStatus($id, $request);
    }

    // =================== RESEND WELCOME EMAIL ===================
    public function resendWelcomeEmail($id)
    {
        return $this->userService->resendWelcomeEmail($id);
    }

    // =================== SEND EMAIL VERIFICATION ===================
    public function sendEmailVerification($id)
    {
        return $this->userService->sendEmailVerification($id);
    }
}