<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HomeService;
use Faker\Provider\ar_EG\Company;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Constants\AppConstants;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    protected $homeService;

    public function __construct(HomeService $homeService)
    {
        $this->homeService = $homeService;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
    */

    public function contact(Request $request)
    {

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'g-recaptcha-response' => 'required',
        ]);

        $recipients = $this->homeService->contact($request);

        return redirect('/contact')->with('success', 'Your message has been sent successfully!');
    }
    private function getHeaderData(Request $request)
    {
        $menuBarDetails = $this->homeService->showMenubar($request);

        $promotionalMessageBar = $this->homeService->showPromotionalMessageBar($request);

        return compact('menuBarDetails', 'promotionalMessageBar');
    }
    private function genericPage(Request $request, $page_content, $metaDetails)
    {
        $commonheaderData = $this->getHeaderData($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.generic_page', array_merge(
            $commonheaderData,
            $metaDetails,
            compact('page_content', 'footercopyright')
        ));
    }


    public function show(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $contactInfoData = $this->homeService->contactInformationBlock($request);
        $contactInfoDetails = $contactInfoData->contactInformation;
        $recaptchaSiteKey = $contactInfoData->recaptchaSiteKey;
        $contactInfoDetails = $this->homeService->processEmailAddresses($contactInfoDetails);
        $metaDetails = $this->homeService->contactMetaTagDetails($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
         return view('pages.contactus', array_merge($commonheaderData, $metaDetails, compact('contactInfoDetails','recaptchaSiteKey','footercopyright')));
    }

    public function index(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $events = $this->homeService->index($request);
        $allCompanyNewsData = $this->homeService->getAllCompanyNews($request);
        $allCompanyNews = $allCompanyNewsData->allCompanyNews->take(3);
        $upcomingCourses = $this->homeService->showUpcomingCourse($request);
        $testimonials = $this->homeService->showTestimonials($request);
        $industryRecLogoslider = $this->homeService->industryRecoginationLogoslider($request);
        $metaDetails = $this->homeService->indexMetaTagDetails($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('welcome', array_merge($commonheaderData, $metaDetails, compact('events', 'allCompanyNews', 'upcomingCourses', 'testimonials', 'industryRecLogoslider','footercopyright')));
    }

    public function showevents(Request $request, $nodeId)
    {
        $getPageContent = $this->homeService->showevents($request, $nodeId);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function showWhoWeAre(Request $request)
    {
        $getPageContent = $this->homeService->showWhoWeAre($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return $this->genericPage($request, $page_content, $metaDetails , $footercopyright);
    }
    public function showManagementTeam(Request $request)
    {
        $getPageContent = $this->homeService->showManagementTeam($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function showPartners(Request $request)
    {
        $getPageContent = $this->homeService->showPartners($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function showEarncredits(Request $request)
    {
        $getPageContent = $this->homeService->showEarncredits($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function showExamPassGuarantess(Request $request)
    {
        $getPageContent = $this->homeService->showExamPassGuarantess($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function showprivacypolicy(Request $request)
    {
        $getPageContent = $this->homeService->showprivacypolicy($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getContentByAlias(Request $alias)
    {
        return $this->homeService->getContentByAlias($alias);
    }
    public function getallevents(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $listalleventsData = $this->homeService->getallevents($request);
        $listallevents = $listalleventsData->listallevents;
        $metaDetails = $listalleventsData->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.listallevents', array_merge($commonheaderData, $metaDetails, compact('listallevents', 'footercopyright')));
    }
    public function showCaseStudy(int $nodeId, Request $request)
    {
        $caseData = $this->homeService->showCaseStudy($nodeId);
        $case = $caseData->getCaseListDetails;
        $metaDetails = $caseData->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.casestudylist', array_merge($metaDetails, compact('case','footercopyright')));
    }
    public function showCaseStudies(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $casestudiesdata = $this->homeService->showCaseStudies();
        $casestudies = $casestudiesdata->casestudies;
        $metaDetails = $casestudiesdata->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.casestudy', array_merge($commonheaderData, $metaDetails, compact('casestudies','footercopyright')));
    }
    public function showUpcomingPublicCourse(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $upcomingPublicCourseData = $this->homeService->showUpcomingPublicCourse($request);
        $upcomingPublicCourse = $upcomingPublicCourseData->upcomingPublicCourse;
        $metaDetails = $upcomingPublicCourseData->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.upcomingcourse', array_merge($commonheaderData, $metaDetails, compact('upcomingPublicCourse','footercopyright')));
    }

    public function showfiltercourse(Request $request)
    {
        $filtercourse = $this->homeService->showfiltercourse($request);

        return response()->json(['filtercourse' => $filtercourse]);
    }

    public function getAllCompanyNews(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $allCompanyNewsData = $this->homeService->getAllCompanyNews($request);
        $allCompanyNews = $allCompanyNewsData->allCompanyNews;
        $metaDetails = $allCompanyNewsData->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.companynews', array_merge($commonheaderData, $metaDetails, compact('allCompanyNews','footercopyright')));
    }
    public function showRssFeed(Request $request)
    {
        $getRssFeed = $this->homeService->getRssFeed($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return response()
            ->view('pages.rssfeed', compact('getRssFeed', 'footercopyright'))
            ->header('Content-Type', 'text/xml');
    }


    public function getAllUpcomingCourses(Request $request, $nodeId)
    {


        $commonheaderData = $this->getHeaderData($request);
        $getAllUpcomingCourses = $this->homeService->getAllUpcomingCourses($request, $nodeId);

        $courseData = $getAllUpcomingCourses->course;
        $testimonialsData = $getAllUpcomingCourses->testimonials ?? [];
        $testimonialCount = $testimonialsData->count();
        $upcomingSessionData = $getAllUpcomingCourses->upcomingSession ?? [];
        $videoTestinomialsData = $getAllUpcomingCourses->getVideoTestinomials ?? [];
        $iframeSrcs = $getAllUpcomingCourses->videolinkiframe ?? [];
        $getcoursesTypeImage = $getAllUpcomingCourses->getCourseTypeImage;
        $trainingDetails = $getAllUpcomingCourses->trainingDetails;
        $metaDetails = $getAllUpcomingCourses->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.listupcomingcourse', array_merge($commonheaderData, $metaDetails, [

            'courseData' => $courseData,
            'testimonialsData' => $testimonialsData,
            'upcomingSessionData' => $upcomingSessionData,
            'videoTestinomialsData' => $videoTestinomialsData,
            'videolinkiframe' => $iframeSrcs,
            'getcoursesTypeImage' => $getcoursesTypeImage,
            'trainingDetails' => $trainingDetails,
            'testimonialcount' => $testimonialCount,
            'footercopyright'=> $footercopyright
        ]));
    }

    public function getAllUpcomingCoursesSessions(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $getAllUpcomingCoursesSessions = $this->homeService->getAllUpcomingCoursesSessions($request);
        $courseSessionsData = $getAllUpcomingCoursesSessions->getAllUpcomingCoursesSessions ?? null;
        $courseTitleData = $getAllUpcomingCoursesSessions->getCourseTitle ?? [];
        $metaDetails = $getAllUpcomingCoursesSessions->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.upcomingsessioncourses', array_merge($commonheaderData, $metaDetails, [
            'courseSessionsData' => $courseSessionsData,
            'courseTitleData' => $courseTitleData,
            'footercopyright' => $footercopyright
        ]));
    }



    public function getAllCourseType(Request $request, $course_type_slug)
    {

        $commonheaderData = $this->getHeaderData($request);
        $courseTypeData = $this->homeService->getAllCourseType($request, $course_type_slug);

        $trainingDetails = $courseTypeData->trainingDetails;
        $getcourseslevel = $courseTypeData->getcourseslevel;
        $getcoursesTypeImage = $courseTypeData->getcoursesTypeImage;
        $metaDetails = $courseTypeData->metaDetails;
         $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.training.trainingcoursepage', array_merge($commonheaderData, $metaDetails, compact('courseTypeData', 'trainingDetails', 'getcourseslevel', 'getcoursesTypeImage', 'footercopyright')));
    }

    public function showfilterPageCourse(Request $request)
    {
        $filterPageCourse = $this->homeService->showfilterPageCourse($request);

        return response()->json(['filterPageCourse' => $filterPageCourse]);

    }
    public function getAllArticlePage(Request $request)
    {

        $getPageContent = $this->homeService->getAllArticlePage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }

    public function getAllBasicPage(Request $request, $nodeId)
    {
        $getPageContent = $this->homeService->getAllBasicPage($request, $nodeId);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }

    public function getTrainingPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $upcomingCourses = $this->homeService->showUpcomingCourse($request);
        $metaDetails = $this->homeService->getTrainingPage($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.training.traininglandingpage', array_merge($commonheaderData, $metaDetails, compact('upcomingCourses','footercopyright')));
    }
    public function getCoursesDetails(Request $request, $course_alias, $course_type_alias)
    {
        return $this->homeService->getCoursesDetails($request, $course_alias, $course_type_alias);
    }

    public function getPMCoursesPage(Request $request)
    {

        $commonheaderData = $this->getHeaderData($request);
        $pmCoursesPageDetails = $this->homeService->getPMCoursesPage($request);
        $trainingDetails = $pmCoursesPageDetails->trainingDetails;
        $getPMCourses = $pmCoursesPageDetails->getCoursesDetails;
        $getcoursesTypeImage = $pmCoursesPageDetails->getCoursesTypeImage;
        $metaDetails = $pmCoursesPageDetails->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.pmcourses', array_merge($commonheaderData, $metaDetails, compact('getcoursesTypeImage', 'getPMCourses', 'trainingDetails', 'footercopyright')));
    }

    public function getCMCoursesPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $cmCoursesPageDetails = $this->homeService->getCMCoursesPage($request);
        $trainingDetails = $cmCoursesPageDetails->trainingDetails;
        $getCMCourses = $cmCoursesPageDetails->getCoursesDetails;
        $getcoursesTypeImage = $cmCoursesPageDetails->getCoursesTypeImage;
        $metaDetails = $cmCoursesPageDetails->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.cmcourses', array_merge($commonheaderData, $metaDetails, compact('getcoursesTypeImage', 'getCMCourses', 'trainingDetails','footercopyright')));
    }

    public function getBACoursesPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);

        $baCoursesPageDetails = $this->homeService->getBACoursesPage($request);
        $trainingDetails = $baCoursesPageDetails->trainingDetails;
        $getBACourses = $baCoursesPageDetails->getCoursesDetails;
        $getcoursesTypeImage = $baCoursesPageDetails->getCoursesTypeImage;
        $metaDetails = $baCoursesPageDetails->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

        return view('pages.upcoming_courses.bacourses', array_merge($commonheaderData, $metaDetails, compact('getcoursesTypeImage', 'getBACourses', 'trainingDetails', 'footercopyright')));
    }

    public function getCourseTypeImageDetails(Request $course_type_url)
    {
        $this->homeService->getCourseTypeImageDetails($course_type_url);
    }
    public function getCanadaJobGrantPage(Request $request)
    {
        $getPageContent = $this->homeService->getCanadaJobGrantPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails;
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getAboutUsPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $testimonials = $this->homeService->showTestimonials($request);
        $industryRecLogoslider = $this->homeService->industryRecoginationLogoslider($request);
        $partnersLogoslider = $this->homeService->partnersLogoslider($request);
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.about_us.about_us_landing_page', array_merge($commonheaderData, compact('industryRecLogoslider', 'testimonials', 'partnersLogoslider', 'footercopyright')));
    }

    public function getHistoryPage(Request $request)
    {

        $getPageContent = $this->homeService->getHistoryPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }

    public function getAccessibilityPolicyPage(Request $request)
    {
        $getPageContent = $this->homeService->getAccessibilityPolicyPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getDiversityPolicyPage(Request $request)
    {
        $getPageContent = $this->homeService->getDiversityPolicyPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getDrugAndAlcoholPolicyPage(Request $request)
    {
        $getPageContent = $this->homeService->getDrugAndAlcoholPolicyPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getEnvironmentalPolicyPage(Request $request)
    {
        $getPageContent = $this->homeService->getEnvironmentalPolicyPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getHealthAndSafetyPolicyPage(Request $request)
    {
        $getPageContent = $this->homeService->getHealthAndSafetyPolicyPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getPolicyRecordingTrainingSessionsPage(Request $request)
    {

        $getPageContent = $this->homeService->getPolicyRecordingTrainingSessionsPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }

    public function getConsultantsQueryData($consultantTypes)
    {
        return $this->homeService->getConsultantsQueryData($consultantTypes);
    }

    public function getOurConsultantsPage(Request $request, $consultant_tab)
    {
        $commonheaderData = $this->getHeaderData($request);
        $consultants_type = $this->homeService->getConsultantsDetails($request, $consultant_tab);
        $consultants_type_details = $consultants_type->consultants_type_details;
        $current_tab = $consultants_type->current_tab;
        $metaDetails = $consultants_type->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

        return view('pages.consultants.our_consultants', array_merge($commonheaderData, $metaDetails, compact('current_tab', 'consultants_type_details', 'footercopyright')));
    }

    public function getTeamTestimonialsPage($nodeId)
    {
        return $this->homeService->getTeamTestimonialsPage($nodeId);
    }
    public function getTeamVideoTestimonialsPage($nodeId)
    {
        return $this->homeService->getTeamVideoTestimonialsPage($nodeId);
    }
    public function getTeamLinksPage($nodeId)
    {
        return $this->homeService->getTeamLinksPage($nodeId);
    }

    public function getAllTeam(Request $request, $nodeId)
    {
        $commonheaderData = $this->getHeaderData($request);
        $getTeamPageDetails = $this->homeService->getAllTeam($request, $nodeId);
        $getTeamPage = $getTeamPageDetails->getTeamPage;
        $getTeamTestinomial = $getTeamPageDetails->getTeamTestinomial;
        $teamVideoTestimonials = $getTeamPageDetails->getTeamVideoTestimonials;
        $teamLinksPage = $getTeamPageDetails->getTeamLinks;
        $specialties = $getTeamPageDetails->getSpecialties;
        $specialtiesIndustry = $getTeamPageDetails->getSpecialtiesindustry;
        $metaDetails = $getTeamPageDetails->metaDetails;
        $teamtestimonialcount = $getTeamTestinomial->count();
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

        return view('pages.team.teampage', array_merge($commonheaderData,$metaDetails, compact(
            'getTeamPage',
            'getTeamTestinomial',
            'teamVideoTestimonials',
            'teamLinksPage',
            'specialties',
            'specialtiesIndustry',
            'teamtestimonialcount',
            'footercopyright'
        )));
    }


    public function getConsultantsTestimonials(Request $request)
    {
        try {

            $nodeId = $request->get('nodeId');
            $commonheaderData = $this->getHeaderData($request);
            $consultantTestimonialsData = $this->homeService->getConsultantsTestimonials($request, $nodeId);
            $consultantTestimonials=$consultantTestimonialsData->getallTestinomials;
            $metaDetails=$consultantTestimonialsData->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

            return view('pages.team.teamconsultantstestimonials', array_merge($commonheaderData, $metaDetails, compact(
                'consultantTestimonials', 'footercopyright'
            )));

        } catch (\Exception $e) {
            Log::error('Error in fetching team testimonials page', [
                'function' => 'getConsultantsTestimonials',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return view('errors.404')->with('error', 'Unable to fetch testimonials.');
        }
    }
    public function getCourseTestimonials(Request $request)
    {

        try {
            $nodeId = $request->get('nodeId');

            $commonheaderData = $this->getHeaderData($request);
            $courseTestimonialsData = $this->homeService->getCourseTestimonials($request, $nodeId);
            $courseTestimonials = $courseTestimonialsData->getTestinomials;
            $metaDetails = $courseTestimonialsData->metaDetails;
                    $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
            return view('pages.upcoming_courses.course_testimonials', array_merge($commonheaderData, $metaDetails, compact(
                'courseTestimonials', 'footercopyright'
            )));
        } catch (\Exception $e) {
            Log::error('Error in fetching course testimonials page', [
                'function' => 'getCourseTestimonials',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return view('errors.404')->with('error', 'Unable to fetch testimonials.');
        }
    }

    public function getTeamVideoTestimonialsPageData($nodeId)
    {
        $getTeamVideoTestimonials = $this->homeService->getTeamVideoTestimonialsPageData($nodeId);

        return response()->json(['getTeamVideoTestimonials' => $getTeamVideoTestimonials]);

    }
    public function getTeamPageContent($nodeId)
    {
        $getTeamPageContent = $this->homeService->getTeamPageContent($nodeId);
        $iframeSrcs = $getTeamPageContent->iframeSrcs;
        return response()->json([
            'getTeamPageContent' => $getTeamPageContent,
            'iframe_srcs' => $iframeSrcs,
        ]);
    }
    public function getRequestingQuote(Request $request)
    {
        $getPageContent = $this->homeService->getRequestingQuote($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);

    }
    public function getWorkingForUs(Request $request)
    {
        $getPageContent = $this->homeService->getWorkingForUs($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getUrlDetails(Request $request, $nodeId)
    {
        $getPageContent = $this->homeService->getUrlDetails($request, $nodeId);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getAllBlogContent(Request $request, $nodeId)
    {
        $getPageContent = $this->homeService->getAllBlogContent($request, $nodeId);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
     }
     public function getSiteMapDetails(Request $request)
     {

        $sitemap = $this->homeService->getSiteMapDetails($request);

        return view('sitemap', compact('sitemap'));

     }
     public function getManagementCoursesPage(Request $request)
     {
         return $this->getCoursesPage($request, 'getManagementCoursesPage');
     }

     public function getAgileCoursesPage(Request $request)
     {
         return $this->getCoursesPage($request, 'getAgileCoursesPage');
     }
     public function getleadershipCoursesPage(Request $request)
     {
         return $this->getCoursesPage($request, 'getleadershipCoursesPage');
     }
     private function getCoursesPage(Request $request, $method)
     {
     
         $commonheaderData = $this->getHeaderData($request);
         $leadershipCoursesPageDetails = $this->homeService->$method($request);
         Log::info('////////////////-----------leadershipCoursesPageDetails-------------------/////////////');
         Log::info('LeadershipCoursesPageDetails:', (array) $leadershipCoursesPageDetails);
         Log::info('////////////////-----------trainingDetails-------------------/////////////');
         Log::info($leadershipCoursesPageDetails->trainingDetails);
        $trainingDetails = $leadershipCoursesPageDetails->trainingDetails;
         $getLeadershipCourses = $leadershipCoursesPageDetails->getCoursesDetails;
         $getcoursesTypeImage = $leadershipCoursesPageDetails->getCoursesTypeImage;
         $metaDetails = $leadershipCoursesPageDetails->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

         return view(
             'pages.upcoming_courses.leadershipcourses',
            array_merge($commonheaderData, $metaDetails, compact('getcoursesTypeImage', 'getLeadershipCourses', 'trainingDetails', 'footercopyright'))
         );
     }
     public function getPayingUsPage(Request $request)
     {
         return $this->getTabPage($request, 'getPayingUsPage');
     }

     public function getPMIPage(Request $request)
     {
         return $this->getTabPage($request, 'getPMIPage');
     }

     private function getTabPage(Request $request, string $method)
     {
         $commonheaderData = $this->getHeaderData($request);
         $page_content_data = $this->homeService->$method($request);

        $page_content = $page_content_data->payingUscontentData ?? '';
        $beforeFirstTab = $page_content_data->beforeFirstTab ?? '';
        $tabButtons = $page_content_data->tabButtons ?? [];
        $tabContents = $page_content_data->tabContents ?? [];
        $afterLastTab = $page_content_data->afterLastTab ?? '';
        $metaDetails = $page_content_data->metaDetails ?? [];
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

        return view('pages.basic_page.paying_us', array_merge(
            $commonheaderData,
            $metaDetails,
            compact('beforeFirstTab', 'tabButtons', 'tabContents', 'afterLastTab', 'page_content', 'footercopyright')
        ));
    }
    public function getAwardsPage(Request $request)
    {
        $getPageContent = $this->homeService->getAwardsPage($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getSponsorships(Request $request)
    {

        $getPageContent = $this->homeService->getSponsorships($request);
        $page_content = $getPageContent->getContent ?? [];
        $metaDetails = $getPageContent->metaDetails ?? [];
        return $this->genericPage($request, $page_content, $metaDetails);
    }
    public function getAllSlug(Request $request)
    {
        try {
            $requestUrl = urldecode($request->path());
            $getAllSlugData = $this->homeService->getAllSlug($requestUrl);

            if ($getAllSlugData) {
                $nodeId = $getAllSlugData->nid;
                switch ($getAllSlugData->type) {
                    case AppConstants::TEAM:
                        return $this->getAllTeam($request, $nodeId);
                    case AppConstants::NODETYPE_EVENTS:
                        return $this->showevents($request, $nodeId);
                    case AppConstants::PAGE:
                        return $this->getAllBasicPage($request, $nodeId);
                    case AppConstants::NODETYPE_COURSE:
                        return $this->getAllUpcomingCourses($request, $nodeId);
                    case AppConstants::NODETYPE_ARTICLE:
                        return $this->getAllBlogContent($request, $nodeId);
                    default:
                        return $this->getAllUpcomingCourses($request, $nodeId);
                }
            }
        } catch (\Exception $e) {

            Log::error('Error in fetching get all slug page', [
                'function' => 'getAllSlug',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Unable to fetch the URL.'], 404);
        }
    }
    public function getOtherCoursesPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $otherCoursesPageDetails = $this->homeService->getOtherCoursesPage($request);
        $getOtherCourses = $otherCoursesPageDetails->getCoursesDetails;
        $trainingDetails = $otherCoursesPageDetails->trainingDetails;
        $courseLevel = $otherCoursesPageDetails->courseLevel;
        $getcoursesTypeImage = $otherCoursesPageDetails->getCoursesTypeImage;
        $metaDetails = $otherCoursesPageDetails->metaDetails;
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);
        return view('pages.upcoming_courses.othercourses', array_merge($commonheaderData, $metaDetails, compact('getcoursesTypeImage', 'getOtherCourses', 'trainingDetails', 'courseLevel', 'footercopyright')));
    }
    public function getConsultingPage(Request $request)
    {
        $commonheaderData = $this->getHeaderData($request);
        $getPageContent = $this->homeService->getConsultingPage($request);
        $pageContent = $getPageContent->getContent ?? [];
        $pageBodyContent = $this->homeService->updateContent($pageContent);
        $metaDetails = $getPageContent->metaDetails ?? [];
        $footercopyright = $this->homeService->getFooterCopyrightDetails($request);

        return view('pages.consultants.consulting_landing_page', array_merge($commonheaderData, $metaDetails, compact('pageContent', 'pageBodyContent','footercopyright')));
    }
   public function serveFromSites($path)
    {
        Log::info('Controller log Serving file from sites: ' . $path);
        return $this->homeService->serveFromSites($path);
    }

    public function serveFromImages($path)
    {
        Log::info('Controller log Serving file from images: ' . $path);
        return $this->homeService->serveFromImages($path);
    }
}
