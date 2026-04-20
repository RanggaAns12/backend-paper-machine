<?php

use Illuminate\Support\Facades\Broadcast;

// 1. Ini bawaan Laravel (Biarkan saja untuk notifikasi private per user)
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 2. ✅ Ini Channel BARU kita untuk Gudang (Tambahkan di bawahnya)
Broadcast::channel('warehouse.inbound', function ($user) {
    // Return true jika user sudah login (tidak null). 
    // Bisa juga ditambah cek role: return $user->hasRole('admin_warehouse');
    return $user !== null; 
});