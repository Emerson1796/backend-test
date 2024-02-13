<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Vinkla\Hashids\Facades\Hashids;

class Redirect extends Model
{
    use SoftDeletes, HasFactory;

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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($redirect) {
            if (empty($redirect->code)) {
                $redirect->code = Hashids::encode(time() . rand());
            }
        });
    }
}
