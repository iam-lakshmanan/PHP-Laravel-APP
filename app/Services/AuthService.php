<?php

namespace App\Services;

use App\DTO\VerifySecretKeyDTO;
use App\DTO\RegisterDetailsDTO;
use App\Models\DigitalKeyClient;
use App\Models\Variable;
use App\Repositories\AuthRepository;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OtpMail;
use App\Models\DigitalKeyDocument;
use App\Models\DigitalKeyKey;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;
use App\Constants\AppConstants;


class AuthService
{
    /**
     * @var AuthRepository
     */
    protected $repository;

    public function __construct(AuthRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Process secret key verification
     */
    public function verifySecretKey(VerifySecretKeyDTO $dto): array
    {
        $key = $dto->secret_key;

        // Verify key against repository
        $keyRecord = $this->repository->findKey($key);

        if (!$keyRecord) {
            return [
                'success' => false,
                'message' => 'Invalid secret key.',
            ];
        }

        // Store in session
        session([
            'dkp_secret_key' => $key,
            'dkp_key_id'     => $keyRecord->key_id,
        ]);

        return [
            'success' => true,
            'step'    => 2,
        ];
    }

    /**
     * Check whether a digital key has been revoked.
     *
     * @param string|null $secretKey
     * @return bool
     */
    public function isKeyRevoked(?string $secretKey): bool
    {
        if (empty($secretKey)) {
            return false;
        }

        $keyRecord = $this->repository->findKey($secretKey);

        // Key not found or no record returned.
        if (!$keyRecord) {
            return false;
        }

        // Return true if the key is revoked, otherwise false.
        return $keyRecord->status === 'revoked';
    }

    /**
     * Check whether a digital key has expired.
     *
     * @param string|null $secretKey
     * @return string|null Returns 'expired' if expired, 'active' if active, or null if not found.
     */
    public function isKeyExpired(?string $secretKey): ?string
    {
        if (empty($secretKey)) {
            return null;
        }

        // Find the key record using its secret key.
        $keyRecord = $this->repository->findKey($secretKey);

        if (!$keyRecord) {
            return null;
        }   

        // Get the associated client.
        $client = DigitalKeyClient::find($keyRecord->client_id);

        if (!$client || !$client->request_date) {
            return null;
        }

        // Check whether the expiration date has passed.
        if (today()->gt($client->request_date)) {
            return 'expired';
        }

        return 'active';
    }

    public function getKeyDetails(?string $secretkey): array
    {
        $getkey = $this->repository->findKey($secretkey);

        if (!$getkey) {
            return [
                'success'      => false,
                'key_id'       => null,
                'alreadylogin' => null,
                'step'         => 1,
            ];
        }

        $keyId = $getkey->key_id ?? $getkey->id;

        // A key is only already logged in if it has been used (use_count > 0) AND has a registered user
        $alreadylogin = null;
        if (($getkey->use_count ?? 0) > 0 && $keyId) {
            $alreadylogin = $this->repository->getUserLoginByKeyId($keyId);
        }

        return [
            'success'      => true,
            'key_id'       => $keyId,
            'alreadylogin' => $alreadylogin,
            'step'         => $alreadylogin ? 3 : 2,
        ];
    }

    public function findKey(?string $key)
    {
        return $this->repository->findKey($key);
    }

    public function getUserLoginByKeyId(?int $keyId)
    {
        return $this->repository->getUserLoginByKeyId($keyId);
    }

    public function incrementKeyUse(int $keyId): void
    {
        $this->repository->incrementKeyUse($keyId);
    }

    /**
     * Check if an email already exists in the user login records.
     *
     * @param string $email
     * @return bool
     */
    public function checkEmailExists(string $email): bool
    {

        return $this->repository->checkEmailExists($email);
    }

    /**
     * Process registration of user details
     */
    public function registerDetails(RegisterDetailsDTO $dto): array
    {
        $keyId = session('dkp_key_id', 1);

        // Save via repository
        $userRecord = $this->repository->saveUserLogin([
            'first_name'  => $dto->first_name,
            'last_name'   => $dto->last_name,
            'email'       => $dto->email,
            'key_id'      => $keyId,
            'first_login' => true,
        ]);

        // Keep user info in session
        session([
            'dkp_user' => [
                'first_name' => $dto->first_name,
                'last_name'  => $dto->last_name,
                'email'      => $dto->email,
            ],
            'dkp_authenticated' => true,
        ]);

        // Increment key usage
        if ($keyId) {
            $this->repository->incrementKeyUse($keyId);
        }

        return [
            'success' => true,
            'user'    => $userRecord,
        ];
    }


    public function sendOtpToUserEmail(string $email): bool
    {
        Log::info('OTP process started', [
            'email' => $email,
        ]);

        try {
            // Step 1: Generate OTP
            $otp = (string) random_int(100000, 999999);

            Log::info('OTP generated successfully', [
                'email' => $email,
            ]);

            // Step 2: Store OTP in session
            session(['dkp_otp' => $otp]);

            Log::info('OTP stored in session', [
                'email' => $email,
                'session_id' => session()->getId(),
            ]);

            // Step 3: Save OTP in database
            $this->repository->saveOtp([
                'email' => $email,
                'otp' => $otp,
            ]);

            Log::info('OTP saved to database', [
                'email' => $email,
            ]);

            $smtpHost = Variable::where('name', AppConstants::SMTP_HOST)->first();
            $smtpPort = Variable::where('name', AppConstants::SMTP_PORT)->first();
            $smtpProtocol = Variable::where('name', AppConstants::SMTP_PROTOCOL)->first();
            $smtpUserName = Variable::where('name', AppConstants::SMTP_USERNAME)->first();
            $smtpPassword = Variable::where('name', AppConstants::SMTP_PASSWORD)->first();

            $smtpHostValue = is_array($hostVal = unserialize($smtpHost->value ?? '')) ? reset($hostVal) : $hostVal;
            $smtpPortValue = is_array($portVal = unserialize($smtpPort->value ?? '')) ? reset($portVal) : $portVal;
            $smtpProtocolValue = is_array($protocolVal = unserialize($smtpProtocol->value ?? '')) ? reset($protocolVal) : $protocolVal;
            $smtpUserNameValue = is_array($usernameVal = unserialize($smtpUserName->value ?? '')) ? reset($usernameVal) : $usernameVal;
            $smtpPasswordValue = is_array($passwordVal = unserialize($smtpPassword->value ?? '')) ? reset($passwordVal) : $passwordVal;

            config([
                'mail.mailers.smtp.host' => $smtpHostValue,
                'mail.mailers.smtp.port' => $smtpPortValue,
                'mail.mailers.smtp.username' => $smtpUserNameValue,
                'mail.mailers.smtp.password' => $smtpPasswordValue,
                'mail.mailers.smtp.encryption' => $smtpProtocolValue,
            ]);
            Mail::purge('smtp');

            // Step 4: Send OTP email
            Log::info('Attempting to send OTP email', [
                'email' => $email,
                'mailer' => 'smtp',
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
            ]);

            Mail::mailer('smtp')->to($email)->send(new OtpMail($email, $otp));

            Log::info('OTP email sent successfully', [
                'email' => $email,
            ]);

            return true;
        } catch (\Throwable $e) {
            Log::error('OTP process failed', [
                'email' => $email,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Verify the OTP provided by the user
     */
    public function verifyOtp(string $email, ?string $otp): bool
    {
        if (empty($otp)) {
            return false;
        }

        $email = strtolower(trim($email));
        $otp = trim((string) $otp);
        $storedOtp = session('dkp_otp');

        Log::info('AuthService verifying OTP', [
            'email' => $email,
            'has_session_otp' => !empty($storedOtp),
            'session_matches' => ($storedOtp && (string) $storedOtp === $otp),
        ]);

        // Check session OTP first
        if ($storedOtp && (string) $storedOtp === $otp) {
            session()->forget('dkp_otp');
            $this->repository->verifyOtp($email, $otp);
            return true;
        }

        // Fallback: verify against database record
        if ($this->repository->verifyOtp($email, $otp)) {
            session()->forget('dkp_otp');
            return true;
        }

        return false;
    }

    /**
     * Generate a Box access token.
     */
    private function getAccessToken(): string
    {
        $response = Http::asForm()
            ->timeout(30)
            ->post('https://api.box.com/oauth2/token', [
                'grant_type' => 'client_credentials',
                'client_id' => config('services.box.client_id'),
                'client_secret' => config('services.box.client_secret'),
                'box_subject_type' => config('services.box.subject_type'),
                'box_subject_id' => config('services.box.enterprise_id'),
            ]);

        if (!$response->successful()) {
            throw new RuntimeException(
                'Box authentication failed: HTTP ' . $response->status()
            );
        }

        $token = $response->json('access_token');

        if (!$token) {
            throw new RuntimeException('Box did not return an access token.');
        }

        return $token;
    }

    /**
     * Download a file from Box.
     */
    public function downloadPdf(string $fileId): string
    {
        Log::info('Box PDF download process started', [
            'file_id' => $fileId,
        ]);

        try {
            // Step 1: Get Box access token
            Log::info('Retrieving Box access token', [
                'file_id' => $fileId,
            ]);

            $token = $this->getAccessToken();

            if (empty($token)) {
                Log::error('Box access token is empty', [
                    'file_id' => $fileId,
                ]);

                throw new RuntimeException(
                    'Box access token could not be retrieved.'
                );
            }

            Log::info('Box access token retrieved successfully', [
                'file_id' => $fileId,
            ]);

            // Step 2: Send download request to Box
            $url = 'https://api.box.com/2.0/files/'
                . rawurlencode($fileId)
                . '/content';

            Log::info('Sending Box PDF download request', [
                'file_id' => $fileId,
                'endpoint' => $url,
                'timeout_seconds' => 120,
            ]);

            $response = Http::withToken($token)
                ->withOptions(['allow_redirects' => true])
                ->timeout(120)
                ->get($url);

            // Step 3: Log HTTP response
            Log::info('Box PDF download response received', [
                'file_id' => $fileId,
                'http_status' => $response->status(),
                'successful' => $response->successful(),
                'content_type' => $response->header('Content-Type'),
                'content_length' => strlen($response->body()),
            ]);

            if (!$response->successful()) {
                Log::error('Box PDF download request failed', [
                    'file_id' => $fileId,
                    'http_status' => $response->status(),
                    'response_body' => substr($response->body(), 0, 2000),
                ]);

                throw new RuntimeException(
                    'Box file download failed: HTTP '
                        . $response->status()
                );
            }

            // Step 4: Retrieve downloaded content
            $content = $response->body();

            Log::info('Box file content retrieved', [
                'file_id' => $fileId,
                'content_size_bytes' => strlen($content),
            ]);

            // Step 5: Validate PDF signature
            if (strncmp($content, '%PDF-', 5) !== 0) {
                Log::error('Downloaded Box file is not a valid PDF', [
                    'file_id' => $fileId,
                    'content_size_bytes' => strlen($content),
                    'content_type' => $response->header('Content-Type'),
                    'content_signature' => substr($content, 0, 20),
                ]);

                throw new RuntimeException(
                    'The requested Box file is not a valid PDF.'
                );
            }

            // Step 6: Download completed
            Log::info('Box PDF downloaded and validated successfully', [
                'file_id' => $fileId,
                'content_size_bytes' => strlen($content),
            ]);

            return $content;
        } catch (Throwable $e) {
            Log::error('Box PDF download process failed', [
                'file_id' => $fileId,
                'exception' => get_class($e),
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }

    /**
     * Get or create the "User Editable" folder inside Digital-Key-PDFs root folder in Box.
     */
    public function getUserEditableFolderId(): string
    {
        $folderId = AppConstants::PDF_USER_EDITABLE; // Box Folder ID for "User Editable" directly from URL
        return $folderId;
    }

    /**
     * Upload or update user-edited PDF into the "User Editable" subfolder on Box.
     */
    public function uploadUserEditedPdf(string $filename, string $pdfContent): ?array
    {
        $token = $this->getAccessToken();
        $folderId = $this->getUserEditableFolderId();

        // Check if file already exists in "User Editable" folder
        $searchResponse = Http::withToken($token)
            ->get("https://api.box.com/2.0/folders/{$folderId}/items?fields=id,name");

        $existingFileId = null;
        if ($searchResponse->successful()) {
            $items = $searchResponse->json('entries') ?? [];
            foreach ($items as $item) {
                if ($item['type'] === 'file' && strtolower($item['name']) === strtolower($filename)) {
                    $existingFileId = $item['id'];
                    break;
                }
            }
        }

        if ($existingFileId) {
            // Upload new version of file
            $uploadResponse = Http::withToken($token)
                ->attach('file', $pdfContent, $filename)
                ->post("https://upload.box.com/api/2.0/files/{$existingFileId}/content");
        } else {
            // Upload new file
            $uploadResponse = Http::withToken($token)
                ->attach('attributes', json_encode([
                    'name' => $filename,
                    'parent' => ['id' => $folderId],
                ]))
                ->attach('file', $pdfContent, $filename)
                ->post("https://upload.box.com/api/2.0/files/content");
        }

        if (!$uploadResponse->successful()) {
            Log::error('Box upload user edited pdf error: ' . $uploadResponse->body());
            return null;
        }

        $entries = $uploadResponse->json('entries') ?? [];
        return !empty($entries[0]) ? $entries[0] : null;
    }

    /**
     * Delete user edited PDF from "User Editable" folder on Box (Revert action).
     */
    public function deleteUserEditedPdf(string $filename): bool
    {
        $token = $this->getAccessToken();
        $folderId = $this->getUserEditableFolderId();

        $searchResponse = Http::withToken($token)
            ->get("https://api.box.com/2.0/folders/{$folderId}/items?fields=id,name");

        if ($searchResponse->successful()) {
            $items = $searchResponse->json('entries') ?? [];
            foreach ($items as $item) {
                if ($item['type'] === 'file' && strtolower($item['name']) === strtolower($filename)) {
                    $delResponse = Http::withToken($token)
                        ->delete("https://api.box.com/2.0/files/{$item['id']}");
                    return $delResponse->successful();
                }
            }
        }

        return false;
    }

    /**
     * Get all file names in the Box "User Editable" folder.
     */
    public function getUserEditedFiles(): array
    {
        try {
            $token = $this->getAccessToken();
            $folderId = $this->getUserEditableFolderId();

            $response = Http::withToken($token)
                ->get("https://api.box.com/2.0/folders/{$folderId}/items?fields=id,name,size,modified_at&limit=1000");

            if ($response->successful()) {
                return $response->json('entries') ?? [];
            }
        } catch (\Throwable $e) {
            Log::error('Box getUserEditedFiles error: ' . $e->getMessage());
        }

        return [];
    }

    /**
     * Download user-edited PDF directly from Box if it exists.
     */
    public function downloadUserEditedPdf(string $filename): ?string
    {
        try {
            $files = $this->getUserEditedFiles();
            foreach ($files as $file) {
                if ($file['type'] === 'file' && strtolower($file['name']) === strtolower($filename)) {
                    return $this->downloadPdf((string)$file['id']);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Box downloadUserEditedPdf error: ' . $e->getMessage());
        }

        return null;
    }

    /**
     * Get the list of documents for a client based on their secret key.
     * */
    public function getDashboardDocuments($clientId, $secretKey)
    {
        Log::info('Dashboard documents retrieval started', ['client_id' => $clientId]);

        Log::info('Validating digital key', ['client_id' => $clientId]);

        // Validate the secret key for the client.
        $key = DigitalKeyKey::where('client_id', $clientId)
            ->where('key_hash', $secretKey)
            ->where('status', 'active')
            ->first();

        if (!$key) {
            return [
                'success' => false,
                'message' => 'Invalid or inactive secret key.',
                'documents' => collect(),
            ];
        }

        Log::info('Digital key validated successfully', ['client_id' => $clientId, 'key_id' => $key->key_id]);

        Log::info('Fetching active printable dashboard documents', [
            'client_id' => $clientId,
            'status' => 1,
            'printable' => 1,
            'per_page' => 10,
        ]);

        // Get active documents assigned to this client (directly or via digital_key_client_documents)
        $documents = DigitalKeyDocument::where(function ($query) use ($key) {
            $query->where('client_id', $key->client_id)
                ->orWhereIn('document_id', function ($subQuery) use ($key) {
                    $subQuery->select('document_id')
                        ->from('digital_key_client_documents')
                        ->where('client_id', $key->client_id);
                });
        })
            ->where('status', 1)
            ->where('printable', 1)
            ->orderByDesc('document_id')
            ->paginate(10);

        Log::info('Dashboard documents query completed', [
            'client_id' => $clientId,
            'documents_on_page' => $documents->count(),
            'total_documents' => $documents->total(),
            'current_page' => $documents->currentPage(),
            'last_page' => $documents->lastPage(),
        ]);

        Log::info('Dashboard documents retrieved successfully', ['client_id' => $clientId]);

        return [
            'success' => true,
            'message' => 'Documents retrieved successfully.',
            'documents' => $documents,
        ];
    }
}
