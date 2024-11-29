<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progress extends Model
{
    use HasFactory;

    protected $table = 'progresses';
    protected $primaryKey = 'id';
    public $timestamps = true;
    protected $fillable = ['title', 'description', 'current_value', 'max_value', 'due_date', 'user_id'];
}
