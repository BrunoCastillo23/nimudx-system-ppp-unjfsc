<?php

namespace App\Exceptions\Internship;

use App\Exceptions\BusinessException;

class InternshipNotActiveException extends BusinessException
{
    protected $message = 'No existe una práctica activa para realizar esta acción.';

    public function status(): int
    {
        return 409;
    }

    public function code(): string
    {
        return 'INTERNSHIP_NOT_ACTIVE';
    }
}
