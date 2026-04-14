<?php

namespace App\Repositories;

use App\Models\QualityTest;
use App\Repositories\Interfaces\QualityTestRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class QualityTestRepository implements QualityTestRepositoryInterface
{
    public function __construct(protected QualityTest $model) {}

    public function getAllPaginated(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model
            // ✅ PERBAIKAN 1: Menyesuaikan kolom untuk tabel Riwayat (agar angkanya tidak strip '-')
            ->select([
                'id', 'paper_machine_roll_id', 'tested_by', 'status', 
                'thickness', 'bw', 'rct', 'moisture', // Parameter utama untuk tabel
                'created_at'
            ])
            ->with([
                'paperMachineRoll:id,roll_number,grade', 
                'tester:id,name,username',               
            ])
            ->when(!empty($filters['status']), fn($q) =>
                $q->where('status', $filters['status'])
            )
            ->when(!empty($filters['paper_machine_roll_id']), fn($q) =>
                $q->where('paper_machine_roll_id', $filters['paper_machine_roll_id'])
            )
            ->when(!empty($filters['tested_by']), fn($q) =>
                $q->where('tested_by', $filters['tested_by'])
            )
            ->latest()
            ->paginate(min($perPage, 50));
    }

    public function findById(int $id): ?QualityTest
    {
        return $this->model
            // ✅ PERBAIKAN 2: Mengambil SEMUA parameter baru untuk halaman Detail / Edit
            ->select([
                'id', 'paper_machine_roll_id', 'tested_by', 'shift', 
                'thickness', 'bw', 'rct', 'bursting', 'moisture',
                'cobb_top', 'cobb_bottom', 'plybonding', 'warna', 
                'status', 'notes', 'created_at', 'updated_at'
            ])
            ->with([
                'paperMachineRoll', 
                'tester:id,name,username',
            ])
            ->find($id);
    }

    public function create(array $data): QualityTest
    {
        return $this->model->create($data);
    }

    public function update(QualityTest $qualityTest, array $data): QualityTest
    {
        $qualityTest->update($data);
        unset($data); // Bersihkan memori agar fresh() berjalan optimal
        return $qualityTest->fresh(['paperMachineRoll', 'tester']);
    }

    public function delete(QualityTest $qualityTest): bool
    {
        return $qualityTest->delete();
    }
}