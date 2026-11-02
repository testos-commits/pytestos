<?php

namespace App\Interfaces;



interface ForcesReloadOnStatusChangeRequest
{
    public function reload(): void;
}
