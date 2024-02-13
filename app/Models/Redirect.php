<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Redirect extends Model
{
    use SoftDeletes;

    protected $fillable = ['destination_url', 'active'];
    protected $dates = ['deleted_at'];

    public function logs()
    {
        return $this->hasMany(RedirectLog::class);
    }

    public function addLog($logData)
    {
        return $this->logs()->create($logData);
    }
}
