<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckRouteNid;
use App\Http\Controllers\EditablePdfController;
use App\Http\Controllers\Dkp\AuthenticateController;
use App\Http\Controllers\Dkp\DashboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('index');
Route::middleware(['check.route.nid'])->group(function () {
    Route::get('/casestudy/{nodeId}/{url?}/{url2?}', [HomeController::class, 'showCaseStudy'])->name('casestudy.show');
    Route::get('/course-testimonial/{nodeId}', [HomeController::class, 'getCourseTestimonials'])->name('course.testimonials');
    Route::get('/consultants-testimonial/{nodeId}', [HomeController::class, 'getconsultantstestimonials'])->name('consultants.testimonials');
    Route::get('/team-page/{nodeId}', [HomeController::class, 'getTeamVideoTestimonialsPageData'])->name('team.videotestimonialspage');
    Route::get('/team-page-content/{nodeId}', [HomeController::class, 'getTeamPageContent'])->name('team.content.page');

    Route::get('/node/{nodeId}', [HomeController::class, 'getUrlDetails'])->name('url.details');

});

Route::get('/project-management-courses', [HomeController::class, 'getPMCoursesPage'])->name('training.pmcourses.page');
Route::get('/change-management-courses', [HomeController::class, 'getCMCoursesPage'])->name('training.cmcourses.page');
Route::get('/business-data-analysis-courses', [HomeController::class, 'getBACoursesPage'])->name('training.bacourses.page');
Route::get('/leadership-management-courses', [HomeController::class, 'getleadershipCoursesPage'])->name('training.leadershipcourses.page');
Route::get('/page/management-courses', [HomeController::class, 'getManagementCoursesPage'])->name('training.management.page');
Route::get('/agile-courses', [HomeController::class, 'getAgileCoursesPage'])->name('training.agile.page');
Route::get('/page/earn-credits', [HomeController::class, 'showEarncredits'])->name('page.earningcredits.show');
Route::get('/other-courses', [HomeController::class, 'getOtherCoursesPage'])->name('training.othercourses.page');

Route::get('/working-for-us', [HomeController::class, 'getWorkingForUs'])->name('working.for.us');
Route::get('/request-a-quote', [HomeController::class, 'getRequestingQuote'])->name('requesting.quote');

Route::get('/sponsorships', [HomeController::class, 'getSponsorships'])->name('sponsorships.show');

Route::get('/contact', [App\Http\Controllers\HomeController::class, 'show'])->name('contact.show');
Route::post('/contactus', [App\Http\Controllers\HomeController::class, 'contact'])->name('contact.create');


Route::get('/allevents', [HomeController::class, 'getallevents'])->name('getallevents');
Route::post('/allevents', [HomeController::class, 'getallevents'])->name('filterevents');
Route::get('/company-news', [HomeController::class, 'getAllCompanyNews'])->name('company.news');
Route::get('/who-we-are', [HomeController::class, 'showWhoWeAre'])->name('who-we-are.show');
Route::get('/management-team', [HomeController::class, 'showManagementTeam'])->name('managementteam.show');
Route::get('/partners', [HomeController::class, 'showPartners'])->name('partners.show');
Route::get('/earn-credits', [HomeController::class, 'showEarncredits'])->name('earningcredits.show');
Route::get('/exam-pass-guarantee', [HomeController::class, 'showExamPassGuarantess'])->name('exampassguarantees.show');
Route::get('/case-studies', [HomeController::class, 'showCaseStudies'])->name('casestudies.show');

Route::get('/upcoming-public-courses', [HomeController::class, 'showUpcomingPublicCourse'])->name('upcoming.public.course.list');
Route::get('/filter/coursetitle', [HomeController::class, 'showfiltercourse'])->name('filtercourse.show');
Route::get('/courses_page_nid', [HomeController::class, 'showCoursePageNid'])->name('courses.page.nid');
Route::get('/rss-feed', [HomeController::class, 'showRssFeed'])->name('rss.feed');
Route::post('/upcoming-public-courses', [HomeController::class, 'showUpcomingPublicCourse'])->name('filterCourses');
Route::get('/privacy-policy', [HomeController::class, 'showprivacypolicy'])->name('privacypolicy.show');


//Training Overview
Route::get('/training', [HomeController::class, 'getTrainingPage'])->name('training.page');


//training menu
Route::get('course-type/{courses_type}', [HomeController::class, 'getAllCourseType'])->name('course.type.details');


Route::get('/filter/coursepagetitle', [HomeController::class, 'showfilterPageCourse'])->name('filtercoursepage.show');


Route::get('/canada-job-grant', [HomeController::class, 'getCanadaJobGrantPage'])->name('canada-job-grant');
Route::get('/paying_us', [HomeController::class, 'getPayingUsPage'])->name('paying-us');
Route::get('/PMI', [HomeController::class, 'getPMIPage'])->name('pmi-page');

Route::get('/about-us', [HomeController::class, 'getAboutUsPage'])->name('aboutus.page');
Route::get('/history', [HomeController::class, 'getHistoryPage'])->name('history.page');
Route::get('/awards', [HomeController::class, 'getAwardsPage'])->name('awards.page');
Route::get('/accessibility-policy', [HomeController::class, 'getAccessibilityPolicyPage'])->name('accessibility.policy.page');
Route::get('/diversity-policy', [HomeController::class, 'getDiversityPolicyPage'])->name('diversity.policy.page');
Route::get('/drug-and-alcohol-policy', [HomeController::class, 'getDrugAndAlcoholPolicyPage'])->name('drug.and.alcohol.policy.page');
Route::get('/environmental-policy', [HomeController::class, 'getEnvironmentalPolicyPage'])->name('environmental.policy.page');
Route::get('/health-and-safety-policy', [HomeController::class, 'getHealthAndSafetyPolicyPage'])->name('health.and.safety.policy.page');
Route::get('/policy-recording-training-sessions', [HomeController::class, 'getPolicyRecordingTrainingSessionsPage'])->name('policy.recording.training.sessions.page');

Route::get('/sites/default/files/{path}', [HomeController::class, 'serveFromSites'])
    ->where('path', '.*')
    ->name('image.proxy.sites');
Route::get('/images/{path}', [HomeController::class, 'serveFromImages'])
    ->where('path', '.*')
    ->name('image.proxy.images');
// Our Consultants
Route::get('/consultants/{consultant_tab}', [HomeController::class, 'getOurConsultantsPage'])->name('our.consultants.page');
Route::get('/consultants/get-testimonial-content', [HomeController::class, 'getTestimonialContent'])->name('consultants.getTestimonialContent');
Route::get('/consulting', [HomeController::class, 'getConsultingPage'])->name('consulting.page');


Route::get('/sitemap.xml', [HomeController::class, 'getSiteMapDetails'])->name('sitemap.xml');

Route::prefix('materials')->name('materials.')->group(function () {
    Route::get('/', [AuthenticateController::class, 'index'])->name('index');
    Route::post('/verify-key', [AuthenticateController::class, 'verifySecretKey'])->name('verifyKey');
    Route::post('/verify-otp-key', [AuthenticateController::class, 'verifyOtpKey'])->name('verifyOtpKey');
    Route::get('/verify-otp-form', [AuthenticateController::class, 'showOtpVerificationForm'])->name('verifyOtpForm');
    Route::post('/register-details', [AuthenticateController::class, 'registerDetails'])->name('registerDetails');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/alreadylogin', [AuthenticateController::class, 'alreadylogin'])->name('alreadylogin');
    Route::post('/verify-existing-email', [AuthenticateController::class, 'verifyExistingEmail'])->name('verifyExistingEmail');
    Route::get('/logout', [AuthenticateController::class, 'logout'])->name('logout');
    // Resend OTP
    Route::post('/resend-otp',[AuthenticateController::class, 'resendOtp'])->name('resend-otp');
    
    // Back to Step 2
    Route::get('/back-to-details',[AuthenticateController::class, 'backToDetails'])->name('back-to-details');

    Route::get('/download-box/{documentId}', [DashboardController::class, 'download'])->name('downloadBox');
    Route::get('/editor/{document?}', [EditablePdfController::class, 'editor'])->name('editor');
    Route::get('/load/{document?}', [EditablePdfController::class, 'loadPdf'])->name('load');
    Route::post('/save/{document?}', [EditablePdfController::class, 'save'])->name('save');
    Route::post('/revert/{document}', [EditablePdfController::class, 'revert'])->name('revert');
    Route::get('/download/{document?}', [EditablePdfController::class, 'download'])->name('download');
});

// Direct alias routes for compatibility
Route::post('/secret-key/verify', [AuthenticateController::class, 'verifySecretKey'])->name('secret-key.verify');
Route::post('/register/details', [AuthenticateController::class, 'registerDetails'])->name('register.details');
Route::post('/existing-user/verify', [AuthenticateController::class, 'verifyExistingEmail'])->name('existing-user.verify');

Route::middleware(['check.route.slug'])->group(function () {
    Route::get('upcoming_sessions/{course_title_slug}/{course_title_slug1?}/{course_title_slug2?}', [HomeController::class, 'getAllUpcomingCoursesSessions'])->name('upcomingcourses.sessions.list');
    Route::get('/{slug}/{slug1?}/{slug2?}', [HomeController::class, 'getAllSlug'])->name('getAllSlug');
});

