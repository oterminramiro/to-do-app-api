<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
	public $table = "Task";
	public $primaryKey = 'IdTask';
	protected $fillable = ['Name','Description','Priority','Color','Date','Pinned'];
}
