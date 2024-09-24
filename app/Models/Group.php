<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    use HasFactory;


    public $fillable =[
        'name',
        'description',
        'created_by'
    ];

    public function members(){
        return $this->hasMany(Member::class);
    }

    public function admin(){
        return $this->belongsTo(User::class, 'created_by');
    }
    
}
