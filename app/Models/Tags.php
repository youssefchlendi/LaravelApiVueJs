<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $table="activities";
    protected $primaryKey="id";
    protected $fillable=['tag_name'];

    public function activities(){
        return $this->belongsToMany(
            Activity::class,
            'tags_activities',
            'tag_id',
            'activity_id');
    }
}
