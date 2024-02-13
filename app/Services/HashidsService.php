<?php

namespace App\Services;

use Vinkla\Hashids\Facades\Hashids;

class HashidsService
{
    public function encode($id)
    {
        return Hashids::encode($id);
    }

    public function decode($hash)
    {
        $decoded = Hashids::decode($hash);
        return count($decoded) ? $decoded[0] : null;
    }
}
