<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileMediaUpdateRequest;
use App\Services\Settings\ProfileMediaService;

class ProfileMediaController extends Controller
{
    public function __construct(protected ProfileMediaService $service) {}

    public function update(ProfileMediaUpdateRequest $request)
    {
        $person = $request->user()->person;

        // 'deleted' llega como string en el body (no como archivo), por eso se
        // lee con input() y no con file() para poder detectar el sentinel.
        $this->service->updateMedia(
            $person,
            $request->input('photo') === 'deleted' ? 'deleted' : $request->file('photo'),
            $request->input('banner') === 'deleted' ? 'deleted' : $request->file('banner')
        );

        return back()->with([
            'message' => 'Medios actualizados exitosamente',
        ], 200);
    }
}
