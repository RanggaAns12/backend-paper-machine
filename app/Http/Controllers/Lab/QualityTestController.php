<?php

namespace App\Http\Controllers\Lab;

use App\Http\Controllers\Controller;
use App\Http\Requests\QualityTest\StoreQualityTestRequest;
use App\Http\Requests\QualityTest\UpdateQualityTestRequest;
use App\Http\Resources\QualityTestResource;
use App\Services\QualityTestService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QualityTestController extends Controller
{
    public function __construct(protected QualityTestService $qualityTestService) {}

    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->query('per_page', 15);
        $filters = $request->only(['result', 'report_id', 'tested_by']);

        $tests = $this->qualityTestService->getAllQualityTests($perPage, $filters);

        unset($filters);

        return response()->json([
            'success' => true,
            'message' => 'Data quality test berhasil diambil.',
            'data'    => QualityTestResource::collection($tests)->response()->getData(true),
            'errors'  => null,
        ]);
    }

    public function store(StoreQualityTestRequest $request): JsonResponse
    {
        $test = $this->qualityTestService->createQualityTest(
            $request->validated(),
            $request->user()->id
        );

        return response()->json([
            'success' => true,
            'message' => 'Quality test berhasil dibuat.',
            'data'    => new QualityTestResource($test),
            'errors'  => null,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Quality test tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Data quality test ditemukan.',
            'data'    => new QualityTestResource($test),
            'errors'  => null,
        ]);
    }

    public function update(UpdateQualityTestRequest $request, int $id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Quality test tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $this->authorize('update', $test);

        $updated = $this->qualityTestService->updateQualityTest($test, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Quality test berhasil diupdate.',
            'data'    => new QualityTestResource($updated),
            'errors'  => null,
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $test = $this->qualityTestService->findQualityTestById($id);

        if (!$test) {
            return response()->json([
                'success' => false,
                'message' => 'Quality test tidak ditemukan.',
                'data'    => null,
                'errors'  => null,
            ], 404);
        }

        $this->authorize('delete', $test);
        $this->qualityTestService->deleteQualityTest($test);

        return response()->json([
            'success' => true,
            'message' => 'Quality test berhasil dihapus.',
            'data'    => null,
            'errors'  => null,
        ]);
    }
}
