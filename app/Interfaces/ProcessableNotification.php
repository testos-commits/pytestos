<?php
namespace App\Interfaces;

interface ProcessableNotification
{
    public function setQueryParameters(mixed $parameter);
    public function setQuery();
    public function process();
}
