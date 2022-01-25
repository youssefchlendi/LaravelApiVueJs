<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $table="items";
    protected $primaryKey="id";
    protected $fillable=['item_name','activity_id','status','dead_line'];

}
