<?php

namespace App\Http\Controllers;

use App\Services\PdfEditService;
use Illuminate\Http\Request;

class EditablePdfController extends Controller
{
    /**
     * @var PdfEditService
     */
    protected $pdfService;

    public function __construct(PdfEditService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    /**
     * Show table of available original PDFs and user's edited versions.
     */
    public function dashboard()
    {
        $documents = $this->pdfService->getDashboardDocuments();
        return view('dkp-adminpanel.editable-pdf.index', compact('documents'));
    }

    /**
     * Show the PDF Editor Blade view.
     */
    public function editor(Request $request, $document = null)
    {
        if (!session('dkp_authenticated') && !session('dkp_user')) {
            return redirect()->route('materials.alreadylogin')
                ->with('error', 'Please log in with your secret key first.');
        }

        $data = $this->pdfService->getEditorData($document);
        return view('dkp-adminpanel.editable-pdf.editor', $data);
    }

    /**
     * Stream/Load the PDF (returns existing user-edited PDF if present, otherwise local original or Box original).
     */
    public function loadPdf(Request $request, $document = null)
    {
        $response = $this->pdfService->getStreamResponse($document);
        if (!$response) {
            abort(404, 'PDF document not found.');
        }
        return $response;
    }

    /**
     * Save the user's edited PDF file.
     */
    public function save(Request $request, $document = null)
    {
        $pdfContent = null;
        if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
            $pdfContent = file_get_contents($request->file('pdf')->getRealPath());
        } else {
            $raw = $request->getContent();
            if (!empty($raw) && strlen($raw) > 1000) {
                $pdfContent = $raw;
            }
        }

        if (!$pdfContent) {
            return response()->json([
                'success' => false,
                'message' => 'No valid PDF data received.'
            ], 422);
        }

        $result = $this->pdfService->saveEditedPdf($document, $pdfContent);
        return response()->json($result);
    }

    /**
     * Revert / Restore original PDF: removes local edit and deletes from Box User Editable folder.
     */
    public function revert(Request $request, $document)
    {
        $result = $this->pdfService->revertPdf($document);
        return response()->json($result);
    }

    /**
     * Download the user's edited PDF (or the original if not edited yet).
     */
    public function download(Request $request, $document = null)
    {
        $response = $this->pdfService->getDownloadResponse($document);
        if (!$response) {
            abort(404, 'PDF document not found.');
        }
        return $response;
    }
}
