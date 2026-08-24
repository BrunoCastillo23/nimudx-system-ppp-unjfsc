<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
        ])->validate();

        // NOTA: Features::registration() está deshabilitado en config/fortify.php.
        // Esta acción no forma parte del flujo real de alta de usuarios (ver
        // App\Services\Registration\UserRegistrationService), que es quien crea
        // la Person/Company y vincula el registro polimórfico 'authenticable'
        // correctamente. No asignamos authenticable_id/type aquí para evitar
        // vincular por error el usuario a una Person incorrecta si esta ruta
        // llegara a activarse sin un rediseño explícito de este flujo.
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
        ]);
    }
}
