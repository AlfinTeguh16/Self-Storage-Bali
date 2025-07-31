<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Storage extends Model
{
    protected $table = 'tb_storages';

    protected $fillable = [
        'size', 'price', 'description', 'is_deleted',
    ];

    public function storageManagement()
    {
        return $this->hasMany(StorageManagement::class, 'storage_id');
    }

    public $timestamps = true;
}
