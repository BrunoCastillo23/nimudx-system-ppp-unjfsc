<?php

namespace App\Http\Controllers;

use App\Http\Requests\Document\UpdateDocumentStatusRequest;
use App\Http\Requests\Document\UploadDocumentRequest;
use App\Models\Assignment;
use App\Models\Document;
use App\Models\Dossier;
use App\Services\DocumentService;
use Illuminate\Http\JsonResponse;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $documentService) {}

    public function store(UploadDocumentRequest $request): JsonResponse
    {
        $data = $request->validated();

        // La ruta requiere 'auth' (ver routes/api.php), por lo que siempre hay
        // un usuario autenticado; la asignación activa se identifica por sesión,
        // igual que en el resto de controllers (UserRegistrationController, etc.).
        $assignment = Assignment::findOrFail(session('assignment_id'));

        $model = $this->documentService->validateOwnership(
            $data['context'], $data['target_id'], $assignment
        );

        $document = $this->documentService->registerDocument($data, $assignment, $model);

        return response()->json([
            'message' => 'Document uploaded successfully',
            'data' => $document,
        ], 201);
    }

    public function updateStatus(UpdateDocumentStatusRequest $request, Document $document): JsonResponse
    {
        $updated = $this->documentService->updateStatus($document, $request->validated());

        $message = $updated ? 'Estado actualizado correctamente' : 'No se pudo actualizar el estado';

        if ($updated->documentable_type == Dossier::class) {
            if ($updated->documentable->approval_status == 1) {
                $message = '¡Felicidades! Todos los documentos han sido aprobados. El Dossier y la Asignación han sido habilitados.';
            }
        }

        return response()->json([
            'message' => $message,
            'data' => $updated->load('documentable'),
        ], 200);
    }
}
