<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MachineResource;
use App\Services\MachineService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function __construct(protected MachineService $machineService) {}

    public function index(): JsonResponse
    {
        $machines = $this->machineService->getAllMachines();
        return response()->json(['success' => true, 'message' => 'Data mesin berhasil diambil.', 'data' => MachineResource::collection($machines)->response()->getData(true), 'errors' => null]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'type'        => ['required', 'in:lab,paper_machine,winder'],
            'status'      => ['sometimes', 'in:active,inactive,maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        $machine = $this->machineService->createMachine($validated);
        return response()->json(['success' => true, 'message' => 'Mesin berhasil dibuat.', 'data' => new MachineResource($machine), 'errors' => null], 201);
    }

    public function show(int $id): JsonResponse
    {
        $machine = $this->machineService->findMachineById($id);
        if (!$machine) {
            return response()->json(['success' => false, 'message' => 'Mesin tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }
        return response()->json(['success' => true, 'message' => 'Data mesin ditemukan.', 'data' => new MachineResource($machine), 'errors' => null]);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $machine = $this->machineService->findMachineById($id);
        if (!$machine) {
            return response()->json(['success' => false, 'message' => 'Mesin tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }

        $validated = $request->validate([
            'name'        => ['sometimes', 'string', 'max:255'],
            'type'        => ['sometimes', 'in:lab,paper_machine,winder'],
            'status'      => ['sometimes', 'in:active,inactive,maintenance'],
            'description' => ['nullable', 'string'],
        ]);

        $updated = $this->machineService->updateMachine($machine, $validated);
        return response()->json(['success' => true, 'message' => 'Mesin berhasil diupdate.', 'data' => new MachineResource($updated), 'errors' => null]);
    }

    public function destroy(int $id): JsonResponse
    {
        $machine = $this->machineService->findMachineById($id);
        if (!$machine) {
            return response()->json(['success' => false, 'message' => 'Mesin tidak ditemukan.', 'data' => null, 'errors' => null], 404);
        }
        $this->machineService->deleteMachine($machine);
        return response()->json(['success' => true, 'message' => 'Mesin berhasil dihapus.', 'data' => null, 'errors' => null]);
    }
}
