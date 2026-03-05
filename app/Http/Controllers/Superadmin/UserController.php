<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(protected UserService $userService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['is_active', 'search']);

        $users = $this->userService->getAllUsers($perPage, $filters);

        return response()->json([
            'success' => true,
            'message' => 'Data user berhasil diambil.',
            'data'    => UserResource::collection($users)->response()->getData(true),
            'errors'  => null,
        ]);
    }


    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'      => ['required', 'string', 'max:255'],
            'username'  => ['required', 'string', 'max:100', 'unique:users,username'],
            'password'  => ['required', 'string', 'min:8'],
            'role'      => ['required', 'in:superadmin,admin_lab,admin_paper_machine,admin_winder'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $user = $this->userService->createUser($validated);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data'    => new UserResource($user),
            'errors'  => null,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        return response()->json(['success' => true, 'message' => 'Data user ditemukan.', 'data' => new UserResource($user), 'errors' => null]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $validated = $request->validate([
            'name'      => ['sometimes', 'string', 'max:255'],
            'username'  => ['sometimes', 'string', 'max:100', 'unique:users,username,' . $id],
            'password'  => ['nullable', 'string', 'min:8'],
            'role'      => ['sometimes', 'in:superadmin,admin_lab,admin_paper_machine,admin_winder'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $updated = $this->userService->updateUser($user, $validated);

        return response()->json(['success' => true, 'message' => 'User berhasil diupdate.', 'data' => new UserResource($updated), 'errors' => null]);
    }

    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->findUserById($id);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $this->userService->deleteUser($user);

        return response()->json(['success' => true, 'message' => 'User berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}
