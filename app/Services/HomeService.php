<?php

namespace App\Services;

use App\Constants\AppConstants;
use App\DTO\ShowEventDTO;
use App\Repositories\HomeRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;
use App\Interfaces\HomeServiceInterface;
use App\DTO\ContactDTO;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class HomeService implements HomeServiceInterface
{
    protected $homeRespository;

    public function __construct()
    {
        $this->homeRespository = new HomeRepository();
    }

    public function contact($request)
    {
        $contactformdetails = new ContactDTO(
            $request->input('name'),
            $request->input('email'),
            $request->input('subject'),
            $request->input('message')
        );
        return $this->homeRespository->contact($contactformdetails,$request);
    }

    public function index($request)
    {

        return $this->homeRespository->index($request);
    }

    public function getallevents($request)
    {

        return $this->homeRespository->getallevents($request);
    }

    public function showWhoWeAre($request)
    {

        return $this->homeRespository->showWhoWeAre($request);
    }
    public function showevents($request,$nodeId)
    {
        return $this->homeRespository->showevents($request,$nodeId);
    }
    public function showManagementTeam($request)
    {
        return $this->homeRespository->showManagementTeam($request);
    }
    public function showPartners($request)
    {
        return $this->homeRespository->showPartners($request);
    }
    public function showEarncredits($request)
    {
        return $this->homeRespository->showEarncredits($request);
    }
    public function showExamPassGuarantess($request)
    {
        return $this->homeRespository->showExamPassGuarantess($request);
    }
    public function showprivacypolicy($request)
    {
        return $this->homeRespository->showprivacypolicy($request);
    }
    public function showCaseStudy($nodeId)
    {
        return $this->homeRespository->showCaseStudy($nodeId);
    }
    public function showCaseStudies()
    {
        return $this->homeRespository->showCaseStudies();
    }
    public function showUpcomingCourse($request)
    {
        return $this->homeRespository->showUpcomingCourse($request);
    }
    public function showUpcomingPublicCourse($request)
    {
        return $this->homeRespository->showUpcomingPublicCourse($request);
    }
    public function showfiltercourse($request)
    {
        return $this->homeRespository->showfiltercourse($request);
    }
    public function getContentByAlias($alias)
    {
        return $this->homeRespository->getContentByAlias($alias);
    }
    public function getAllCompanyNews($request)
    {
        return $this->homeRespository->getAllCompanyNews($request);
    }
    public function showPromotionalMessageBar($request)
    {
        return $this->homeRespository->showPromotionalMessageBar($request);
    }
    public function showTestimonials($request)
    {
        return $this->homeRespository->showTestimonials($request);
    }
    public function showMenubar($request)
    {
        return $this->homeRespository->showMenubar($request);
    }
    public function industryRecoginationLogoslider($request)
    {
        return $this->homeRespository->industryRecoginationLogoslider($request);
    }
    public function contactInformationBlock($request)
    {
        return $this->homeRespository->contactInformationBlock($request);
    }
    public function getRssFeed($request)
    {
        return $this->homeRespository->getRssFeed($request);
    }
    public function getAllUpcomingCourses($request,$nodeId)
    {
        return $this->homeRespository->getAllUpcomingCourses($request,$nodeId);
    }
    public function getAllUpcomingCoursesSessions($request)
    {
        return $this->homeRespository->getAllUpcomingCoursesSessions($request);
    }

    public function getAllCourseType($request, $course_type_slug)
    {
        return $this->homeRespository->getAllCourseType($request, $course_type_slug);
    }

    public function showfilterPageCourse($request)
    {
        return $this->homeRespository->showfilterPageCourse($request);
    }

    public function getAllArticlePage($request)
    {
        return $this->homeRespository->getAllArticlePage($request);
    }
    public function getAllBasicPage($request,$nodeId)
    {
        return $this->homeRespository->getAllBasicPage($request,$nodeId);
    }
    public function getAllTeam($request,$nodeId)
    {
        return $this->homeRespository->getAllTeam($request,$nodeId);
    }
    public function getTrainingPage($request)
    {
        return $this->homeRespository->getTrainingPage($request);
    }
    public function getCoursesDetails($request, $course_alias, $course_type_alias)
    {
        return $this->homeRespository->getCoursesDetails($request, $course_alias, $course_type_alias);
    }

    public function getPMCoursesPage($request)
    {
        return $this->homeRespository->getPMCoursesPage($request);
    }
    public function getCMCoursesPage($request)
    {
        return $this->homeRespository->getCMCoursesPage($request);
    }
    public function getBACoursesPage($request)
    {
        return $this->homeRespository->getBACoursesPage($request);
    }

    public function getleadershipCoursesPage($request)
    {
        return $this->homeRespository->getleadershipCoursesPage($request);
    }

    public function getCourseTypeImageDetails($course_type_url)
    {
        return $this->homeRespository->getCourseTypeImageDetails($course_type_url);
    }
    public function getCanadaJobGrantPage($request)
    {
        return $this->homeRespository->getCanadaJobGrantPage($request);
    }
    public function getPayingUsPage($request)
    {
        return $this->homeRespository->getPayingUsPage($request);
    }
    public function getHistoryPage($request)
    {
        return $this->homeRespository->getHistoryPage($request);
    }
    public function getAwardsPage($request)
    {
        return $this->homeRespository->getAwardsPage($request);
    }

    public function getAccessibilityPolicyPage($request)
    {
        return $this->homeRespository->getAccessibilityPolicyPage($request);
    }
    public function getDiversityPolicyPage($request)
    {
        return $this->homeRespository->getDiversityPolicyPage($request);
    }
    public function getDrugAndAlcoholPolicyPage($request)
    {
        return $this->homeRespository->getDrugAndAlcoholPolicyPage($request);
    }
    public function getEnvironmentalPolicyPage($request)
    {
        return $this->homeRespository->getEnvironmentalPolicyPage($request);
    }
    public function getHealthAndSafetyPolicyPage($request)
    {
        return $this->homeRespository->getHealthAndSafetyPolicyPage($request);
    }
    public function getPolicyRecordingTrainingSessionsPage($request)
    {
        return $this->homeRespository->getPolicyRecordingTrainingSessionsPage($request);
    }
    public function getConsultantsDetails($request, $consultant_tab)
    {
        return $this->homeRespository->getConsultantsDetails($request, $consultant_tab);
    }
    public function partnersLogoslider($request)
    {
        return $this->homeRespository->partnersLogoslider($request);
    }
    public function getConsultantsQueryData($consultantTypes)
    {
        return $this->homeRespository->getConsultantsQueryData($consultantTypes);
    }
    public function getTeamTestimonialsPage($nodeId)
    {
        return $this->homeRespository->getTeamTestimonialsPage($nodeId);
    }
    public function getTeamVideoTestimonialsPage($nodeId)
    {
        return $this->homeRespository->getTeamVideoTestimonialsPage($nodeId);
    }
    public function getTeamLinksPage($nodeId)
    {
        return $this->homeRespository->getTeamLinksPage($nodeId);
    }
    public function getconsultantstestimonials($request, $nodeId)
    {
        return $this->homeRespository->getconsultantstestimonials($request, $nodeId);
    }
    public function getCourseTestimonials($request, $nodeId)
    {
        return $this->homeRespository->getCourseTestimonials($request, $nodeId);
    }
    public function processEmailAddresses($contactInfoDetails)
    {

        foreach ($contactInfoDetails as $content) {
            $content->body = preg_replace('/<a\s+href="mailto:([^"]+)">([^<]+)<\/a>/', '$2', $content->body);
            $content->body = preg_replace_callback(
                '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,7}\b/',
                function ($matches) {
                    $email = $matches[0];
                    if (filter_var($email, FILTER_VALIDATE_EMAIL) && !filter_var($email, FILTER_VALIDATE_URL)) {
                        return '<a href="mailto:' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</a>';
                    }

                    return $email;
                },
                $content->body
            );

            $content->body = preg_replace('/\[[^\]]*\]/', '', $content->body);
        }


        return $contactInfoDetails;
    }
    public function getTeamVideoTestimonialsPageData($nodeId)
    {
        return $this->homeRespository->getTeamVideoTestimonialsPageData($nodeId);
    }
    public function getTeamPageContent($nodeId)
    {
        return $this->homeRespository->getTeamPageContent($nodeId);
    }

    public function getRequestingQuote($request)
    {
        return $this->homeRespository->getRequestingQuote($request);
    }
    public function getWorkingForUs($request)
    {
        return $this->homeRespository->getWorkingForUs($request);
    }
    public function getUrlDetails($request, $nodeId)
    {
        return $this->homeRespository->getUrlDetails($request, $nodeId);
    }
    public function getAllBlogContent($request,$nodeId)
    {
        return $this->homeRespository->getAllBlogContent($request,$nodeId);
    }
    public function contactMetaTagDetails($request)
    {
        return $this->homeRespository->contactMetaTagDetails($request);
    }
    public function indexMetaTagDetails($request)
    {
        return $this->homeRespository->indexMetaTagDetails($request);
    }
    public function getSiteMapDetails($request)
    {
        return $this->homeRespository->getSiteMapDetails($request);
    }

    public function getManagementCoursesPage($request)
    {
        return $this->homeRespository->getManagementCoursesPage($request);
    }
    public function getAgileCoursesPage($request)
    {
        return $this->homeRespository->getAgileCoursesPage($request);
    }
    public function getPMIPage($request)
    {
        return $this->homeRespository->getPMIPage($request);
    }
    public function getSponsorships($request)
    {
        return $this->homeRespository->getSponsorships($request);
    }
    public function getAllSlug($requestUrl)
    {
        return $this->homeRespository->getAllSlug($requestUrl);
    }
    public function getOtherCoursesPage($request)
    {
        return $this->homeRespository->getOtherCoursesPage($request);
    }
    public function getConsultingPage($request)
    {
        return $this->homeRespository->getConsultingPage($request);
    }
    public function updateContent($contentDetails)
    {

        $baseUrl = env('TRAINING_URL');
        $liveUrl = env('PROCEPT_COM');
        $trainingUrl = env('TRAINING_URL');

        $contentData=$contentDetails->body_value;

        $content = preg_replace(
            '/https?:\/\/(' .
                preg_quote($liveUrl, '/') .
                '|' .
                preg_quote($trainingUrl, '/') .
                ')/',
            '',
            $contentData
        );
$updatedContent = preg_replace_callback(
    '/<img\s+([^>]*?)src="([^"]*)"(.*?)>/i',
    function ($matches) use ($baseUrl) {
        $beforeSrc = $matches[1];
        $src = $matches[2];
        $afterSrc = $matches[3];

        if (!preg_match('/^https?:\/\//i', $src)) {
            $src = $baseUrl . ltrim($src, '/');
        }

        $imgTag = '<img ' . $beforeSrc . 'src="' . $src . '"' . $afterSrc . '>';

        // Add alt attribute if missing
        if (!preg_match('/\salt\s*=\s*/i', $imgTag)) {
            $imgTag = str_replace('<img', '<img alt=""', $imgTag);
        }

        return $imgTag;
    },
            $content
        );
        return $updatedContent;
    }


      public function serveFromSites($path)
    {
        Log::info("Services log Serving file from sites: {$path}");
        return $this->fetchAndServe(AppConstants::SITES_BASE_URL . $path, "sites_default_files_{$path}");
    }

    public function serveFromImages($path)
    {
        Log::info("Services logServing image from images: {$path}");
        return $this->fetchAndServe(AppConstants::IMAGES_BASE_URL . $path, "images_{$path}");
    }

    private function fetchAndServe($remoteUrl, $cacheKey)
    {
        $cached = Cache::remember($cacheKey, 60 * 60, function () use ($remoteUrl) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->get($remoteUrl);

                if ($response->successful()) {
                    Log::info("Successfully fetched image", [
                        'url' => $remoteUrl,
                        'size' => strlen($response->body()),
                        'content_type' => $response->header('Content-Type'),
                    ]);

                    return [
                        'content' => $response->body(),
                        'content_type' => $response->header('Content-Type'),
                    ];
                } else {
                    Log::warning("Failed to fetch image - non-success status", [
                        'url' => $remoteUrl,
                        'status' => $response->status(),
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Exception while fetching image", [
                    'url' => $remoteUrl,
                    'message' => $e->getMessage(),
                ]);
            }

            return null;
        });

        if (!$cached) {
            Log::error("Image not found or failed to fetch", ['url' => $remoteUrl]);
            abort(404, 'Image not found on intranet');
        }

        return response($cached['content'], Response::HTTP_OK)
            ->header('Content-Type', $cached['content_type']);
    }

    public function getFooterCopyrightDetails($request)
    {
        return $this->homeRespository->getFooterCopyrightDetails($request);
    }
}
