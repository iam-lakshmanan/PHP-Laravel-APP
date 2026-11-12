<?php

namespace App\Http\Controllers\Dkp;

use App\Http\Controllers\Controller;
use App\Models\DigitalKeyDocument;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * @var AuthService
     */
    protected $service;

    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $user = session('dkp_user');
        $secretKey = session('dkp_secret_key');

        if (!$user || !$secretKey) {
            return redirect()->route('materials.alreadylogin')
                ->with('error', 'Please log in with your secret key.');
        }

        $clientId = is_array($user)
            ? ($user['client_id'] ?? null)
            : ($user->client_id ?? null);

        if (!$clientId) {
            $keyRecord = $this->service->findKey($secretKey);
            $clientId = $keyRecord ? $keyRecord->client_id : null;
        }

        if (!$clientId) {
            $documents = collect();
        } else {
            try {
                $result = $this->service->getDashboardDocuments(
                    $clientId,
                    $secretKey
                );

                $documents = $result['success'] ? $result['documents'] : collect();
            } catch (\Throwable $e) {
                Log::error('Digital Key dashboard error', [
                    'error' => $e->getMessage(),
                ]);

                $documents = collect();
            }
        }

        $pdfEditService = app(\App\Services\PdfEditService::class);
        $userDetails = $pdfEditService->getUserDetailsFromDb();
        $userFullName = $userDetails['full_name'];
        $userEmail = $userDetails['email'];
        $logoUrl = asset('img/procept-logo.png');

        $userEditedFiles = $this->service->getUserEditedFiles();
        $editedFileNames = [];
        foreach ($userEditedFiles as $f) {
            if (isset($f['name'])) {
                $editedFileNames[] = strtolower($f['name']);
            }
        }

        return view(
            'dkp-adminpanel.dashboard.dashboard',
            compact('user', 'documents', 'userFullName', 'userEmail', 'logoUrl', 'editedFileNames')
        );
    }

    public function download(int $documentId)
    {
        $user = session('dkp_user');
        $secretKey = session('dkp_secret_key');

        if (!$user || !$secretKey) {
            return redirect()->route('materials.alreadylogin')
                ->with('error', 'Please log in first.');
        }

        $clientId = is_array($user)
            ? ($user['client_id'] ?? null)
            : ($user->client_id ?? null);

        if (!$clientId) {
            $keyRecord = $this->service->findKey($secretKey);
            $clientId = $keyRecord ? $keyRecord->client_id : null;
        }

        try {
            $document = DigitalKeyDocument::where('document_id', $documentId)
                ->where('status', 1)
                ->first();

            if (!$document) {
                abort(404, 'Document not found.');
            }

            if (empty($document->printable)) {
                abort(403, 'This document is not authorized for printing or downloading.');
            }

            if (empty($document->box_file_id)) {
                abort(404, 'Box file ID is missing.');
            }

            $pdf = $this->service->downloadPdf(
                (string) $document->box_file_id
            );

            try {
                $pdfEditService = app(\App\Services\PdfEditService::class);
                $pdf = $pdfEditService->stampPdfWithCoverAndFooter($pdf);
            } catch (\Throwable $e) {
                Log::error('Cover page stamping error on dashboard download: ' . $e->getMessage());
            }

            $filename = preg_replace(
                '/[^A-Za-z0-9._-]/',
                '_',
                $document->title ?: 'document'
            );

            $filename = preg_replace('/\.pdf$/i', '', $filename)
                . '.pdf';

            return response($pdf, 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' =>
                'inline; filename="' . $filename . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store',
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::error('Box PDF download failed', [
                'document_id' => $documentId,
                'error' => $e->getMessage(),
            ]);

            abort(502, 'Unable to retrieve the PDF from Box.');
        }
    }
}
