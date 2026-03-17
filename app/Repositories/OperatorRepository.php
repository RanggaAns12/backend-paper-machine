<?php

namespace App\Repositories;

use App\Models\Operator;
use App\Repositories\Interfaces\OperatorRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class OperatorRepository implements OperatorRepositoryInterface
{
    /**
     * Mengambil semua data operator, bisa difilter berdasarkan pencarian nama.
     */
    public function getAll(array $filters = []): Collection
    {
        $query = Operator::query();

        // Jika ada filter pencarian (search)
        if (isset($filters['search']) && $filters['search'] !== '') {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        // Mengambil semua data tanpa pagination, diurutkan berdasarkan nama (A-Z)
        return $query->orderBy('name', 'asc')->get();
    }

    /**
     * Mencari data operator berdasarkan ID.
     */
    public function findById(int $id): ?Operator
    {
        return Operator::find($id);
    }

    /**
     * Menyimpan data operator baru ke database.
     */
    public function create(array $data): Operator
    {
        return Operator::create($data);
    }

    /**
     * Memperbarui data operator yang sudah ada.
     */
    public function update(Operator $operator, array $data): Operator
    {
        $operator->update($data);
        
        return $operator;
    }

    /**
     * Menghapus data operator dari database.
     */
    public function delete(Operator $operator): bool
    {
        return $operator->delete();
    }
}