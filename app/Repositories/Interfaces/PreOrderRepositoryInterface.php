<?php

namespace App\Repositories\Interfaces;

interface PreOrderRepositoryInterface
{
    public function getActiveOrders();
    public function create(array $data);
}