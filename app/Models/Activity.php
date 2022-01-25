<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table="activities";
    protected $primaryKey="id";
    protected $fillable=['activity_name','priority'];

    public function items(){
        return $this->hasMany(Item::class,'activity_id');
    }
    public function tags(){
        return $this->belongsToMany(
            Tags::class,
            'tags_activities',
            'activity_id',
            'tag_id');
    }
    protected $guarded = [];
}
