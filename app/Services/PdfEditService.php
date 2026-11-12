<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\Models\DigitalKeyDocument;
use App\Models\DigitalKeyUserLogin;
use App\Models\DigitalKeyKey;
use App\Services\AuthService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use setasign\Fpdi\Tcpdf\Fpdi;


class PdfEditService
{
    /**
     * @var AuthService
     */
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Retrieve user first name, last name, and email from database.
     */
    public function getUserDetailsFromDb(): array
    {
        $sessionUser = session('dkp_user');
        $email = is_array($sessionUser) ? ($sessionUser['email'] ?? null) : null;
        $keyId = session('dkp_key_id');

        $userRecord = null;
        if ($email) {
            $userRecord = DigitalKeyUserLogin::where('email', $email)->latest()->first();
        }
        if (!$userRecord && $keyId) {
            $userRecord = DigitalKeyUserLogin::where('key_id', $keyId)->latest()->first();
        }
        if (!$userRecord) {
            $secretKey = session('dkp_secret_key');
            if ($secretKey) {
                $key = DigitalKeyKey::where('key_hash', $secretKey)
                    ->orWhere('key_hash', hash('sha256', $secretKey))
                    ->first();
                if ($key) {
                    $userRecord = DigitalKeyUserLogin::where('key_id', $key->key_id)->latest()->first();
                }
            }
        }

        $firstName = $userRecord ? $userRecord->first_name : (is_array($sessionUser) ? ($sessionUser['first_name'] ?? '') : '');
        $lastName = $userRecord ? $userRecord->last_name : (is_array($sessionUser) ? ($sessionUser['last_name'] ?? '') : '');
        $userEmail = $userRecord ? $userRecord->email : ($email ?: '');

        if (!$firstName && !$lastName && !$userEmail) {
            $firstName = '';
            $lastName = '';
            $userEmail = '';
        }

        $fullName = trim("{$firstName} {$lastName}");
        if (!$fullName) {
            $fullName = $userEmail;
        }

        return [
            'first_name' => $firstName,
            'last_name'  => $lastName,
            'full_name'  => $fullName,
            'email'      => $userEmail,
        ];
    }

    /**
     * Check if a PDF file or binary string has already been stamped with the cover page.
     */
    public function isAlreadyStamped($source): bool
    {
        if (is_string($source) && file_exists($source)) {
            $filesize = filesize($source);
            $handle = @fopen($source, 'rb');
            if (!$handle) return false;
            $head = fread($handle, 65536);
            if ($filesize > 65536) {
                fseek($handle, -65536, SEEK_END);
                $tail = fread($handle, 65536);
            } else {
                $tail = '';
            }
            fclose($handle);
            return (strpos($head, AppConstants::PERSONALIZED_COVER_MARKER) !== false || strpos($tail, AppConstants::PERSONALIZED_COVER_MARKER) !== false);
        }

        $content = is_string($source) ? $source : '';
        if (empty($content)) {
            return false;
        }

        return (strpos($content, AppConstants::PERSONALIZED_COVER_MARKER) !== false);
    }

    /**
     * Stamp PDF with Page 1 Personalized Cover Page and Page 2..N Personalized Footers.
     *
     * @param string $source File path or raw binary string of PDF
     * @return string Stamped PDF binary content
     */
    public function stampPdfWithCoverAndFooter($source): string
    {
        @ini_set('memory_limit', AppConstants::PDF_MEMORY_LIMIT);
        @set_time_limit(AppConstants::PDF_TIME_LIMIT);

        if ($this->isAlreadyStamped($source)) {
            return is_string($source) && file_exists($source) ? file_get_contents($source) : (string)$source;
        }

        $tempFile = null;
        if (is_string($source) && file_exists($source)) {
            $sourceFile = $source;
        } else {
            $tempFile = tempnam(sys_get_temp_dir(), AppConstants::PDF_TEMP_PREFIX);
            file_put_contents($tempFile, (string)$source);
            $sourceFile = $tempFile;
        }

        try {
            $userDetails = $this->getUserDetailsFromDb();
            $fullName = $userDetails['full_name'];
            $userEmail = $userDetails['e    mail'];

            $pdf = new Fpdi('P', 'mm', 'A4', true, 'UTF-8', false);
            $pdf->setPrintHeader(false);
            $pdf->setPrintFooter(false);
            $pdf->SetAutoPageBreak(false);
            $pdf->SetMargins(0, 0, 0);
            $pdf->SetKeywords(AppConstants::PERSONALIZED_COVER_MARKER);
            $pdf->SetTitle(AppConstants::PDF_TITLE);
            $pdf->SetAuthor(AppConstants::PDF_AUTHOR);

            // ----------------------------------------------------
            // PAGE 1: Personalized Cover Page (Matching Image 1)
            // ----------------------------------------------------
            $pdf->AddPage('P', 'A4');
            $pageWidth = AppConstants::PDF_PAGE_WIDTH;
            $pageHeight = AppConstants::PDF_PAGE_HEIGHT;

            // Logo or Header
            // Logo or Header
            $isBarnesConti = str_contains(request()->getHost(), 'barnesconti.com');

            $logoPath = public_path(
                $isBarnesConti
                    ? AppConstants::BARNES_CONTI_LOGO_PATH
                    : AppConstants::PROCEPT_LOGO_PATH
            );

            if (file_exists($logoPath)) {
                $logoWidth = 65;
                $logoX = ($pageWidth - $logoWidth) / 2;

                $pdf->Image(
                    $logoPath,
                    $logoX,
                    35,
                    $logoWidth,
                    0,
                    'PNG',
                    '',
                    'T',
                    false,
                    300,
                    '',
                    false,
                    false,
                    0,
                    false,
                    false,
                    false
                );
            } else {
                $pdf->SetTextColor(0, 56, 118);
                $pdf->SetFont('helvetica', 'B', 30);
                $pdf->SetXY(0, 36);

                $headerText = $isBarnesConti ? AppConstants::BARNES_CONTI_HEADER_TEXT : AppConstants::PROCEPT_HEADER_TEXT;

                $pdf->Cell($pageWidth, 12, $headerText, 0, 1, 'C');
            }

            // Blue horizontal accent bar
            $barWidth = 42;
            $barHeight = 1.6;
            $barX = ($pageWidth - $barWidth) / 2;
            $barY = 56;
            $pdf->SetFillColor(0, 102, 204);
            $pdf->Rect($barX, $barY, $barWidth, $barHeight, 'F');

            // Subtitle: COURSE MATERIAL
            $pdf->SetTextColor(13, 35, 68);
            $pdf->SetFont('helvetica', 'B', 14);
            $pdf->SetXY(0, 64);
            $pdf->Cell($pageWidth, 8, AppConstants::PDF_COVER_SUBTITLE, 0, 1, 'C');

            // Personalized Notice Card (Rounded light blue box)
            $cardWidth = 150;
            $cardHeight = 72;
            $cardX = ($pageWidth - $cardWidth) / 2;
            $cardY = 92;

            $pdf->SetFillColor(234, 244, 252); // #EAF4FC
            $pdf->SetDrawColor(203, 227, 248); // #CBE3F8
            $pdf->SetLineWidth(0.5);
            $pdf->RoundedRect($cardX, $cardY, $cardWidth, $cardHeight, 4, '1111', 'DF');

            // Notice text inside card
            $cardHtml = '
            <div style="text-align: center; font-family: helvetica;">
                <p style="font-size: 11pt; color: #2C3E50; margin: 0; line-height: 1.4;">This PDF file is issued<br>exclusively to</p>
                <p style="font-size: 14pt; font-weight: bold; color: #003876; margin: 6px 0 0 0; line-height: 1.3;">' . htmlspecialchars($fullName) . '<br><span style="font-size: 12pt;">(' . htmlspecialchars($userEmail) . ')</span></p>
                <p style="font-size: 9.5pt; color: #4A5568; margin-top: 8px; line-height: 1.35;">Duplication and further<br>distribution are prohibited.</p>
            </div>';

            $pdf->writeHTMLCell($cardWidth - 10, $cardHeight - 12, $cardX + 5, $cardY + 8, $cardHtml, 0, 0, false, true, 'C', true);

            // Bottom Section: Lock Circle & Confidential Material
            $iconCenterX = $pageWidth / 2;
            $iconCenterY = 195;
            $circleRadius = 8;

            $pdf->SetFillColor(0, 56, 118);
            $pdf->Circle($iconCenterX, $iconCenterY, $circleRadius, 0, 360, 'F', [], [0, 56, 118]);

            // White Lock Vector inside the circle (using native RoundedRect, Circle, and Rect)
            $pdf->SetDrawColor(255, 255, 255);
            $pdf->SetLineWidth(0.85);
            $pdf->RoundedRect($iconCenterX - 2.2, $iconCenterY - 4.2, 4.4, 4.5, 2.2, '1100', 'D');

            $pdf->SetFillColor(255, 255, 255);
            $pdf->RoundedRect($iconCenterX - 3.4, $iconCenterY - 1.2, 6.8, 5.0, 0.6, '1111', 'F');

            $pdf->SetFillColor(0, 56, 118);
            $pdf->Circle($iconCenterX, $iconCenterY + 0.7, 0.75, 0, 360, 'F', [], [0, 56, 118]);
            $pdf->Rect($iconCenterX - 0.4, $iconCenterY + 0.7, 0.8, 1.4, 'F', [], [0, 56, 118]);

            // Divider Line
            $dividerWidth = 110;
            $dividerX = ($pageWidth - $dividerWidth) / 2;
            $dividerY = 212;
            $pdf->SetDrawColor(210, 218, 226);
            $pdf->SetLineWidth(0.4);
            $pdf->Line($dividerX, $dividerY, $dividerX + $dividerWidth, $dividerY);

            // Text: PROCEPT / CONFIDENTIAL MATERIAL
            $pdf->SetTextColor(70, 80, 90);
            $pdf->SetFont('helvetica', 'B', 8.5);
            $pdf->SetXY(0, 216);
            $pdf->Cell($pageWidth, 5, AppConstants::PROCEPT_HEADER_TEXT, 0, 1, 'C');

            $pdf->SetTextColor(110, 120, 130);
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetXY(0, 221);
            $pdf->Cell($pageWidth, 5, AppConstants::PDF_CONFIDENTIAL_TEXT, 0, 1, 'C');

            // ----------------------------------------------------
            // PAGE 2+: Original Pages with Personalized Footer (Image 2)
            // ----------------------------------------------------
            $pageCount = $pdf->setSourceFile($sourceFile);
            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $tplIdx = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($tplIdx);
                $orientation = ($size['width'] > $size['height']) ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height'], true);

                $displayPageNum = $pageNo + 1; // Cover page is page 1

                // Personalized Footer at bottom
                $footerMarginLeft = 14;
                $footerMarginRight = 14;
                $footerWidth = $size['width'] - $footerMarginLeft - $footerMarginRight;
                $footerY = $size['height'] - 15;
                $footerHeight = 11;

                $footerHtml = '
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="85%" align="left" style="font-family:helvetica; font-size:7.5pt; color:#444444; line-height:1.25;">
                            This PDF file is issued exclusively to<br>
                            <b style="color:#111111;">' . htmlspecialchars($fullName) . ' (' . htmlspecialchars($userEmail) . ').</b><br>
                            Duplication and further distribution are prohibited.
                        </td>
                        <td width="15%" align="right" valign="bottom" style="font-family:helvetica; font-size:9pt; font-weight:bold; color:#333333;">
                            ' . $displayPageNum . '
                        </td>
                    </tr>
                </table>';

                $pdf->writeHTMLCell($footerWidth, $footerHeight, $footerMarginLeft, $footerY, $footerHtml, 0, 0, false, true, 'L', true);
            }

            $output = $pdf->Output('', 'S');
            return $output;
        } catch (\Throwable $e) {
            Log::error('Failed to stamp PDF with cover page: ' . $e->getMessage());
            return is_string($source) && file_exists($source) ? file_get_contents($source) : (string)$source;
        } finally {
            if ($tempFile && file_exists($tempFile)) {
                @unlink($tempFile);
            }
        }
    }

    /**
     * Get PDF content for document directly from Box (User edited copy or stamped master original).
     */
    public function getPdfContent($document): ?string
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $boxFilename = $this->getBoxEditedFilename($filename);

        // 1. Check if user already has an edited version on Box
        $userEditedPdf = $this->authService->downloadUserEditedPdf($boxFilename);
        if ($userEditedPdf) {
            if (!$this->isAlreadyStamped($userEditedPdf)) {
                $userEditedPdf = $this->stampPdfWithCoverAndFooter($userEditedPdf);
            }
            return $userEditedPdf;
        }

        // 2. Otherwise download original master copy directly from Box
        if ($docRecord && !empty($docRecord->box_file_id)) {
            try {
                $rawPdf = $this->authService->downloadPdf((string)$docRecord->box_file_id);
                if (!$this->isAlreadyStamped($rawPdf)) {
                    $rawPdf = $this->stampPdfWithCoverAndFooter($rawPdf);
                }
                return $rawPdf;
            } catch (\Throwable $e) {
                Log::error('Box download failed for document: ' . $e->getMessage());
            }
        }

        return null;
    }

    /**
     * Resolve document filename and DigitalKeyDocument record if numeric ID or base64.
     */
    public function resolveDocumentInfo($document): array
    {
        $docRecord = null;
        $filename = 'document.pdf';

        if (is_numeric($document)) {
            $docRecord = DigitalKeyDocument::where('document_id', $document)->first();
            if ($docRecord) {
                $filename = $docRecord->title ?: "Document-{$docRecord->document_id}.pdf";
                if (substr(strtolower($filename), -4) !== '.pdf') {
                    $filename .= '.pdf';
                }
            }
        } elseif (!empty($document)) {
            $decoded = base64_decode($document, true);
            $filename = ($decoded !== false && preg_match('/^[a-zA-Z0-9_\-\. ]+$/', $decoded)) ? $decoded : $document;
            $docRecord = DigitalKeyDocument::where('title', $filename)
                ->orWhere('title', preg_replace('/\.pdf$/i', '', $filename))
                ->first();
        }

        return [$filename, $docRecord];
    }

    /**
     * Get user identifier for saving edits (uses session dkp_user email or Auth id).
     */
    public function getUserIdentifier(): string
    {
        $email = session('dkp_user.email');
        if ($email) {
            return preg_replace('/[^A-Za-z0-9._-]/', '_', $email);
        }
        return (string)(Auth::id() ?? 'guest');
    }

    /**
     * Get user prefix for edited filename: e.g. harivishnuait__ST-CR-WB-0-6-0-Fillable.pdf
     */
    public function getBoxEditedFilename(string $filename): string
    {
        $email = session('dkp_user.email') ?: 'user';
        $prefix = preg_replace('/@.*$/', '', $email);
        $cleanPrefix = preg_replace('/[^A-Za-z0-9_-]/', '', $prefix);
        return $cleanPrefix . '__' . $filename;
    }

    /**
     * Get list of documents for dashboard with modified dates and edit statuses.
     */
    public function getDashboardDocuments(): array
    {
        return [];
    }

    /**
     * Get data required for the editor Blade view.
     */
    public function getEditorData($document): array
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $boxFilename = $this->getBoxEditedFilename($filename);

        $hasSavedVersion = false;
        try {
            $files = $this->authService->getUserEditedFiles();
            foreach ($files as $file) {
                if ($file['type'] === 'file' && strtolower($file['name']) === strtolower($boxFilename)) {
                    $hasSavedVersion = true;
                    break;
                }
            }
        } catch (\Throwable $e) {
            Log::error('Box getEditorData check error: ' . $e->getMessage());
        }

        $userDetails = $this->getUserDetailsFromDb();
        $isBarnesConti = str_contains(request()->getHost(), 'barnesconti.com');


        return [
            'document' => $document,
            'filename' => $filename,
            'hasSavedVersion' => $hasSavedVersion,
            'loadUrl' => route('materials.load', ['document' => $document]),
            'saveUrl' => route('materials.save', ['document' => $document]),
            'downloadUrl' => route('materials.download', ['document' => $document]),
            'userFullName' => $userDetails['full_name'],
            'userEmail' => $userDetails['email'],
            'logoUrl' => asset(
                $isBarnesConti ? 'img/barnesconti-logo.png' : 'img/procept-logo.png'
            ),
        ];
    }

    /**
     * Get PDF response for inline streaming (editor load) with Cover Page and Footers.
     */
    public function getStreamResponse($document)
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $pdfContent = $this->getPdfContent($document);

        if (!$pdfContent) {
            return null;
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }

    /**
     * Save edited PDF to Box only (no local storage).
     */
    public function saveEditedPdf($document, $pdfContent): array
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $boxFilename = $this->getBoxEditedFilename($filename);

        // Upload directly to Box "User Editable" folder
        try {
            $this->authService->uploadUserEditedPdf($boxFilename, $pdfContent);
        } catch (\Throwable $e) {
            Log::error('Box upload user edited pdf error: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'PDF saved successfully to Box.',
            'box_filename' => $boxFilename,
        ];
    }

    /**
     * Revert / Restore original PDF: deletes from Box (no local storage).
     */
    public function revertPdf($document): array
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $boxFilename = $this->getBoxEditedFilename($filename);
        try {
            $this->authService->deleteUserEditedPdf($boxFilename);
        } catch (\Throwable $e) {
            Log::error('Box delete user edited pdf error: ' . $e->getMessage());
        }

        return [
            'success' => true,
            'message' => 'PDF restored to original master copy.',
        ];
    }

    /**
     * Get response for downloading user's edited PDF or personalized original PDF.
     */
    public function getDownloadResponse($document)
    {
        list($filename, $docRecord) = $this->resolveDocumentInfo($document);
        $pdfContent = $this->getPdfContent($document);

        if (!$pdfContent) {
            return null;
        }

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
        ]);
    }
}
