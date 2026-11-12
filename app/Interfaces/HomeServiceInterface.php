<?php

namespace App\Interfaces;

interface HomeServiceInterface
{
  public function contact($request);

  public function index($request);
  public function getallevents($request);
  public function showWhoWeAre($request);
  public function showManagementTeam($request);
  public function showPartners($request);
  public function showEarncredits($request);
  public function showExamPassGuarantess($request);
  public function showCaseStudies();
  public function showUpcomingCourse($request);
  public function showUpcomingPublicCourse($request);

  public function showCaseStudy($id);
  public function getContentByAlias($alias);
  public function showevents($request,$nodeId);
  public function getAllCompanyNews($request);
  public function showfiltercourse($request);
  public function showPromotionalMessageBar($request);
  public function showTestimonials($request);
  public function showMenubar($request);
  public function industryRecoginationLogoslider($request);
  public function contactInformationBlock($request);
  public function getRssFeed($request);
  public function getAllUpcomingCourses($request,$nodeId);
  public function getAllUpcomingCoursesSessions($request);
  public function getAllCourseType($request, $course_type_slug);
  public function showfilterPageCourse($request);

  public function getAllArticlePage($request);
  public function getAllBasicPage($request,$nodeId);
  public function getAllTeam($request,$nodeId);
  public function getTrainingPage($request);
  public function getCoursesDetails($request, $course_alias, $course_type_alias);
  public function getPMCoursesPage($request);
  public function getCMCoursesPage($request);
  public function getBACoursesPage($request);
  public function getleadershipCoursesPage($request);
  public function getCourseTypeImageDetails($course_type_url);
  public function getCanadaJobGrantPage($request);
  public function getPayingUsPage($request);
  public function getHistoryPage($request);
  public function getAccessibilityPolicyPage($request);
  public function getDiversityPolicyPage($request);
  public function getDrugAndAlcoholPolicyPage($request);
  public function getEnvironmentalPolicyPage($request);
  public function getHealthAndSafetyPolicyPage($request);
  public function getPolicyRecordingTrainingSessionsPage($request);
  public function getConsultantsDetails($request, $consultant_tab);
  public function partnersLogoslider($request);
  public function getConsultantsQueryData($consultantTypes);
  public function getTeamTestimonialsPage($nodeId);
  public function getTeamVideoTestimonialsPage($nodeId);
  public function getTeamLinksPage($nodeId);
  public function getconsultantstestimonials($request, $nodeId);
  public function getCourseTestimonials($request, $nodeId);
  public function getTeamVideoTestimonialsPageData($nodeId);
  public function getTeamPageContent($nodeId);
  public function getRequestingQuote($request);
  public function getWorkingForUs($request);
  public function getUrlDetails($request, $nodeId);
  public function getAllBlogContent($request,$nodeId);
  public function getSiteMapDetails($request);
  public function getManagementCoursesPage($request);
  public function getAgileCoursesPage($request);
  public function getPMIPage($request);
  public function getAwardsPage($request);
  public function getSponsorships($request);
  public function getAllSlug($requestUrl);
  public function getOtherCoursesPage($request);
  public function getConsultingPage($request);

}


