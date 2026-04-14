<?php

namespace App\Repositories\Interfaces;

interface WinderLogRepositoryInterface
{
    public function getAll();
    public function findById($id);
    public function getByPaperMachineRollId($pmRollId);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
}