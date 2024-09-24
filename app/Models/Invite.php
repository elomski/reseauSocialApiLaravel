<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    use HasFactory;

    protected $fillable = [
        'email',
        'group_id',
        'status'
    ];

    public function group(){
        return $this->belongsTo(Group::class);
    }
}
