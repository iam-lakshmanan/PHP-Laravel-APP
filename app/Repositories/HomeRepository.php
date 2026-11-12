<?php

namespace App\Repositories;

use App\Constants\AppConstants;
use App\Mail\ContactFormMail;
use App\Models\Block;
use App\Models\Contact;
use App\Models\FieldDataBody;
use App\Models\FieldDataFieldCourseTarget;
use App\Models\FieldDataFieldCourseType;
use App\Models\FieldDataFieldLinks;
use App\Models\FieldDataFieldTeamTarget;
use App\Models\FieldDataFieldVideoTestimonial;
use App\Models\IndustryRecognitionImages;
use App\Models\MenuCustom;
use App\Models\MenuLinks;
use App\Models\Metatag;
use App\Models\Node;
use App\Models\Xmlsitemap;
use App\Models\PublicPromoMessage;
use App\Models\TaxonomyVocabulary;
use App\Models\UrlAlias;
use App\Models\MetatagConfig;
use Carbon\Carbon;
use DOMDocument;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Models\Variable;
use Illuminate\Support\Facades\DB;
use Spatie\Sitemap\Sitemap;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;

class HomeRepository
{

    public function contact($contactformdetails, $request): object
    {
        try {

            $recipients = Contact::where('cid', '=', AppConstants::CONTACT_CID)->first();

            if (!$recipients) {
                return response()->json(['message' => 'No recipients found.'], 404);
            }
            $data = [
                'name' => $contactformdetails->name,
                'email' => $contactformdetails->email,
                'subject' => $contactformdetails->subject,
                'message' => $contactformdetails->message,
            ];

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

            $recipientArray = explode(',', $recipients->recipients);
            foreach ($recipientArray as $recipient) {
                Mail::to(trim($recipient))->send(new ContactFormMail($data));
            }
            $secretRow = Variable::where('name', AppConstants::RECAPTCHA_SECRET_KEY)->first();
            $recaptchaSecretKey = is_array($val = unserialize($secretRow->value ?? '')) ? reset($val) : $val;

            // Validate CAPTCHA via Google
            $captchaResponse = Http::asForm()->post(AppConstants::RECAPTCHA_API_URL, [
                'secret' => $recaptchaSecretKey,
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]);

            // Check if the request was successful
            if ($captchaResponse->successful()) {
                // Parse the response as an array
                $data = $captchaResponse->json();

                // Check if CAPTCHA verification was successful
                if ($data['success']) {
                    log::info("CAPTCHA verified successfully.");
                } else {
                    log::info("CAPTCHA verification failed.");
                }
            } else {
                // Handle failure (e.g., connection issue or invalid reCAPTCHA response)
                log::info("Failed to connect to the reCAPTCHA API.");
            }
            return $recipients;
        } catch (\Exception $e) {
            Log::error('Error in sending email', [
                'function' => 'contact',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function index($listevents): object
    {
        try {

            $events = Node::leftJoin('field_data_field_event_type', 'field_data_field_event_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_event_date', 'field_data_field_event_date.entity_id', '=', 'node.nid')
                ->where('field_data_field_event_type.field_event_type_value', '=', AppConstants::FIELD_EVENT_TYPE_VALUE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->whereDate('field_data_field_event_date.field_event_date_value', '>=', Carbon::today())
                ->orderBy('field_data_field_event_date.field_event_date_value', 'asc')
                ->take(3)
                ->get()
                ->map(function ($event) {

                    $urlAlias = UrlAlias::where('source', '=', 'node/' . $event->nid)
                        ->select('alias')
                        ->first();
                    $event->url = !empty($urlAlias) ? $this->getBaseUrl($urlAlias->alias) : '';
                    return $event;
                });

            return $events;
        } catch (\Exception $e) {
            Log::error('Error in fetching events', [
                'function' => 'homeServiceIndex',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function showevents($request,$nodeId): object
    {

        try {

            $metaDetails = $this->getMetaTagDetails($nodeId);
            $eventlist = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_event_type', 'field_data_field_event_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_event_date', 'field_data_field_event_date.entity_id', '=', 'node.nid')
                ->where('node.type', AppConstants::NODETYPE_EVENTS)
                ->where('field_data_field_event_type.field_event_type_value', AppConstants::FIELD_EVENT_TYPE_VALUE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('node.nid', $nodeId)
                ->first();

            if ($eventlist && $eventlist->body_value) {
                $eventlist->body_value = $this->getUrlContent($eventlist->body_value);

                $eventlist->body_value = str_replace(AppConstants::BASIC_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $eventlist->body_value);
                $eventlist->body_value = str_replace(AppConstants::BLOG_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $eventlist->body_value);
                $eventlist->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE, '</button>', $eventlist->body_value);
                $eventlist->body_value = $this->getAnimateRowColumn($eventlist->body_value);
            }

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $eventlist,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching allevents', [
                'function' => 'showevents',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function showCaseStudies()
    {
        try {
            $casestudies = Node::where('node.type', '=', AppConstants::NODETYPE_DEXP_PORTFOLIO)
                ->select('node.title', 'node.nid')
                ->get()
                ->map(function ($case) {
                    $urlAlias = UrlAlias::where('source', '=', 'node/' . $case->nid)
                        ->select('alias')
                        ->first();

                    $case->url_alias = $urlAlias ? $urlAlias->alias : null;

                    return $case;
                });

            $metaDetails = $this->getMetaTagGlobalDetails(AppConstants::SUBMENU_CASE_STUDIES);
            return (object) [
                'metaDetails' => $metaDetails,
                'casestudies' => $casestudies,
            ];
        } catch (\Exception $e) {
            Log::error('Error in showCaseStudies', [
                'function' => 'showCaseStudies',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return [];
        }
    }

    public function showCaseStudy(int $nodeId): object
    {
        try {
            $getCaseListDetails = FieldDataBody::where('entity_id', '=', $nodeId)
                ->select('body_value')
                ->get();
            $metaDetails = $this->getMetaTagDetails($nodeId);

            return (object) [
                'metaDetails' => $metaDetails,
                'getCaseListDetails' => $getCaseListDetails,
            ];
        } catch (\Exception $e) {
            Log::error('Error in showCaseStudy', [
                'function' => 'showCaseStudy',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }


    public function showManagementTeam()
    {

        try {
            $content = $this->getContentByAlias(AppConstants::MANAGEMENT_TEAM_NID);
            $metaDetails = $this->getMetaTagDetails(AppConstants::MANAGEMENT_TEAM_NID);
            if ($content) {
                $content->block_custom_details = $this->getBlockCustomDetails(AppConstants::MANAGEMENT_TEAM_NID);
                $content->meet_our_management_team = $this->getMeetOurManagementTeam();
            }

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $content,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Management Team page content', [
                'function' => 'showManagementTeam',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }




    public function getMeetOurManagementTeam()
    {

        $getTeamPageDetails = Node::leftJoin('field_data_field_members_visible', 'field_data_field_members_visible.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_team_image', 'field_data_field_team_image.entity_id', '=', 'node.nid')
            ->leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_team_image.field_team_image_fid')
            ->leftJoin('field_data_field_teamtype', 'field_data_field_teamtype.entity_id', '=', 'node.nid')
            ->where('node.type', AppConstants::TEAM)
            ->where('field_data_field_teamtype.field_teamtype_value', AppConstants::ADMIN_TEAM)
            ->where('field_data_field_members_visible.field_members_visible_value', AppConstants::FIELD_RESALES_YES)
            ->where('node.status', AppConstants::NODE_STATUS)
            ->orderBy('node.title', 'asc')
            ->get();
        return $getTeamPageDetails;
    }

    public function showPartners()
    {
        return $this->showPageContent(AppConstants::PARTNERS_NID);
    }
    public function showEarncredits()
    {
        return $this->showPageContent(AppConstants::EARNING_CREDITS_NID);
    }
    public function showExamPassGuarantess()
    {
        return $this->showPageContent(AppConstants::EXAM_PASS_GUARANTEES_NID);
    }
    public function showprivacypolicy()
    {
        return $this->showPageContent(AppConstants::PROCEPT_PRIVACY_POLICY_NID);
    }


    public function getContentByAlias($alias)
    {
        try {
            $aboutus = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')
                ->where('node.nid', '=', $alias)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->first();

            if (!empty($aboutus->body_value)) {
                $aboutus->body_value = $this->getAnimateRowColumn($aboutus->body_value);
                return $aboutus;
            } else {
                throw new Exception('No content found');
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }



    public function getBlockCustomDetails($nodeId)
    {
        $getBlockCustom = Block::leftjoin('block_custom', 'block_custom.bid', '=', 'block.delta')
            ->where('pages', 'node/' . $nodeId)
            ->where('visibility', AppConstants::VISIBILITY)
            ->where('status', AppConstants::NODE_STATUS)
            ->where('theme', AppConstants::EXCEPTION)
            ->first();

        return $getBlockCustom;
    }

    public function getallevents($request): object
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $todayDate = now()->startOfDay();

            $filterevent = Node::join('field_data_field_event_type', 'field_data_field_event_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_event_date', 'field_data_field_event_date.entity_id', '=', 'node.nid')
                ->where('field_data_field_event_type.field_event_type_value', '=', AppConstants::FIELD_EVENT_TYPE_VALUE)
                ->where('node.status', AppConstants::NODE_STATUS)->whereDate('field_data_field_event_date.field_event_date_value', '>=', $todayDate);

            if ($startDate && $endDate) {
                $filterevent->whereBetween('field_data_field_event_date.field_event_date_value', [$startDate, $endDate]);
            }

            $listallevents = $filterevent->select('node.title', 'field_data_field_event_date.field_event_date_value', 'node.nid')
                ->orderBy('field_data_field_event_date.field_event_date_value', 'asc')
                ->get()
                ->map(function ($event) {
                    $event->formatted_date = Carbon::parse($event->field_event_date_value)->format('l, F j, Y - H:i');
                    $urlAlias = UrlAlias::where('source', 'node/' . $event->nid)->first();
                    $event->url = !empty($urlAlias) ? $this->getBaseUrl($urlAlias->alias) : '';
                    return $event;
                });
            $metaDetails = $this->getMetaTagGlobalDetails(AppConstants::UPCOMING_EVENTS);
            return (object) [
                'metaDetails' => $metaDetails,
                'listallevents' => $listallevents,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching event list', [
                'function' => 'getallevents',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function getAllCompanyNews(): object
    {

        try {

            $allCompanyNews = Node::with('fieldDataBody')
                ->leftJoin('field_data_field_media', function ($join) {
                    $join->on('field_data_field_media.entity_id', '=', 'node.nid')
                        ->where('field_data_field_media.delta', '=', 0);
                })
                ->leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_media.field_media_fid')
                ->where('node.type', AppConstants::NODETYPE_ARTICLE)
                ->where('node.promote', AppConstants::NODE_PROMOTE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->orderBy('node.nid', 'desc')
                ->paginate(10);
            $metaDetails = $this->getMetaTagGlobalDetails(AppConstants::COMPANY_NEWS);

            $allCompanyNews->getCollection()->transform(function ($item) {
                $urlAlias = UrlAlias::where('source', 'node/' . $item->nid)->first();
                $item->url = !empty($urlAlias) ? $this->getBaseUrl($urlAlias->alias) : '';
                $pageTitle = explode(':', $item->title);
                $item->titleOne = $pageTitle[1] ?? '';
                $item->titleTwo = $pageTitle[0] ?? '';
                return $item;
            });

            return (object) [
                'metaDetails' => $metaDetails,
                'allCompanyNews' => $allCompanyNews,
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching company news', [
                'function' => 'getAllCompanyNews',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function getFooterCopyrightDetails(): object{

try{

         $footercopyright = Block::join('block_custom', 'block_custom.bid', '=', 'block.delta')
                ->where('region', AppConstants::FOOTER_COPYRIGHT)
                ->where('theme', AppConstants::EXCEPTION)
                ->first();

} catch (Exception $e) {
            Log::error('Error in fetching footer copyright details', [
                'function' => 'getFooterCopyrightDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
        return (object) [
            'footercopyright' => $footercopyright
        ];

    }


    public function showPromotionalMessageBar(): object
    {
        try {
            $promoMessageDetails = PublicPromoMessage::get();
            return (object) $promoMessageDetails;
        } catch (Exception $e) {

            Log::error('Error in fetching Promotional Message Bar list', [
                'function' => 'showPromotionalMessageBar',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function showUpcomingCourse(): object
    {
        try {
            $todayDate = date('Y-m-d 00:00:00');

            $upcomingCoursesNodeData = Node::with('fieldDataFieldCourseNodeDetails.fieldDataFieldCourseNode')->join('field_data_field_choose_session_type', 'field_data_field_choose_session_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_session_dates', 'field_data_field_session_dates.entity_id', '=', 'node.nid')->join('field_data_field_resale', 'field_data_field_resale.entity_id', 'node.nid')
                ->join('field_data_field_procept_sell_ticket_course', 'field_data_field_procept_sell_ticket_course.entity_id', 'field_data_field_resale.field_resale_value')
                ->join('field_data_field_if_yes_eventbrite_link', 'field_data_field_if_yes_eventbrite_link.entity_id', 'field_data_field_resale.field_resale_value')
                ->leftJoin('field_data_field_start_date1', 'field_data_field_start_date1.entity_id', '=', 'node.nid')
                ->where('node.type', AppConstants::NODETYPE_SESSION)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('field_data_field_procept_sell_ticket_course.field_procept_sell_ticket_course_value', AppConstants::FIELD_RESALES_YES)
                ->where(function ($query) {
                    $query->where('field_data_field_choose_session_type.field_choose_session_type_value', AppConstants::SESSION_TYPE_CONTIGUOUS)
                        ->orWhere('field_data_field_choose_session_type.field_choose_session_type_value', AppConStants::SESSION_TYPE_BROKEN_UP);
                })
                ->where(function ($query) use ($todayDate) {
                    $query->where('field_data_field_session_dates.field_session_dates_value', '>=', $todayDate)
                        ->orWhere('field_data_field_start_date1.field_start_date1_value', '>=', $todayDate);
                })
                ->orderByRaw('COALESCE(field_data_field_session_dates.field_session_dates_value,field_data_field_start_date1.field_start_date1_value) ASC')
                ->take(5)
                ->get();

            return (object) $upcomingCoursesNodeData;
        } catch (Exception $e) {

            Log::error('Error in fetching upcoming public courses on the home page', [
                'function' => 'showUpcomingCourse',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function showUpcomingPublicCourse(): object
    {
        try {


            $todayDate = now()->startOfDay();

            $filtercourse = Node::with([
                'fieldDataFieldCourseNodeDetails.fieldDataFieldCourseNode',
                'fieldDataFieldCourseInstructor.fieldCourseInstructorNode',
                'fieldDataFieldInstructor1.fieldDataFieldInstructorNode1',
                'fieldDataFieldInstructor2.fieldDataFieldInstructorNode2',
                'fieldDataFieldInstructor3.fieldDataFieldInstructorNode3',
                'fieldDataFieldInstructor4.fieldDataFieldInstructorNode4',
                'fieldDataFieldInstructor5.fieldDataFieldInstructorNode5',
                'fieldDataFieldInstructor6.fieldDataFieldInstructorNode6',
                'fieldDataFieldInstructor7.fieldDataFieldInstructorNode7',
                'fieldDataFieldInstructor8.fieldDataFieldInstructorNode8',
                'fieldDataFieldInstructor9.fieldDataFieldInstructorNode9',
                'fieldDataFieldInstructor10.fieldDataFieldInstructorNode10',
                'FieldDataFieldSessionLocLocation.fieldDataFieldSessionLocation.FieldDataFieldSessionLocationNode'
            ])
                ->leftJoin('field_data_field_choose_session_type', 'field_data_field_choose_session_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_resale', 'field_data_field_resale.entity_id', 'node.nid')
                ->leftJoin('field_data_field_procept_sell_ticket_course', 'field_data_field_procept_sell_ticket_course.entity_id', 'field_data_field_resale.field_resale_value')
                ->leftJoin('field_data_field_if_yes_eventbrite_link', 'field_data_field_if_yes_eventbrite_link.entity_id', 'field_data_field_resale.field_resale_value')
                ->leftJoin('field_data_field_session_dates', 'field_data_field_session_dates.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_start_date1', 'field_data_field_start_date1.entity_id', '=', 'node.nid')
                ->where('node.type', AppConstants::NODETYPE_SESSION)
                ->where('field_data_field_procept_sell_ticket_course.field_procept_sell_ticket_course_value', AppConstants::FIELD_RESALES_YES)
                ->where('node.status', AppConstants::NODE_STATUS);

            // Filter by upcoming dates from today
            $filtercourse->where(function ($filtercourse) use ($todayDate) {
                $filtercourse->where('field_data_field_session_dates.field_session_dates_value', '>=', $todayDate)
                    ->orWhere('field_data_field_start_date1.field_start_date1_value', '>=', $todayDate);
            });

            $filtercourse->whereIn('field_data_field_choose_session_type.field_choose_session_type_value', [
                AppConstants::SESSION_TYPE_CONTIGUOUS,
                AppConstants::SESSION_TYPE_BROKEN_UP
            ])
                ->orderByRaw('COALESCE(field_data_field_session_dates.field_session_dates_value,field_data_field_start_date1.field_start_date1_value) ASC');

            // Pagination
            $upcomingPublicCourse = $filtercourse->paginate(10);

            // Process and return results
            $this->getUpcomingPublicCourseUrl($upcomingPublicCourse);
            $metaDetails = $this->getMetaTagGlobalDetails(AppConstants::SUBMENU_UPCOMING_PUBLIC_COURSES);

            return (object) [
                'metaDetails' => $metaDetails,
                'upcomingPublicCourse' => $upcomingPublicCourse,
            ];
        } catch (Exception $e) {

            Log::error('Error in fetching upcoming public courses on the List', [
                'function' => 'showUpcomingPublicCourse',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function getUpcomingPublicCourseUrl($upcomingPublicCourse)
    {
        $upcomingPublicCourse->each(function ($course) {
            // Course URL
            $course->course_url = $this->getUrlAlias('node/' . $course->fieldDataFieldCourseNodeDetails->fieldDataFieldCourseNode->nid);

            if (!empty($course->fieldDataFieldCourseInstructor) && isset($course->fieldDataFieldCourseInstructor->fieldCourseInstructorNode->nid)) {
                $course->fieldCourseInstructor_url = $this->getUrlAlias('node/' . $course->fieldDataFieldCourseInstructor->fieldCourseInstructorNode->nid);
            }

            // Instructor 1 URL
            if ($course->fieldDataFieldInstructor1) {
                $instructor1 = $course->fieldDataFieldInstructor1->fieldDataFieldInstructorNode1;
                if ($instructor1) {
                    $course->instructor1_url = $this->getUrlAlias('node/' . $instructor1->nid);
                }
            }
            // Instructor 2 URL
            if ($course->fieldDataFieldInstructor2) {
                $instructor2 = $course->fieldDataFieldInstructor2->fieldDataFieldInstructorNode2;
                if ($instructor2) {
                    $course->instructor2_url = $this->getUrlAlias('node/' . $instructor2->nid);
                }
            }
            // Instructor 3 URL
            if ($course->fieldDataFieldInstructor3) {
                $instructor3 = $course->fieldDataFieldInstructor3->fieldDataFieldInstructorNode3;
                if ($instructor3) {
                    $course->instructor3_url = $this->getUrlAlias('node/' . $instructor3->nid);
                }
            }
            // Instructor 4 URL
            if ($course->fieldDataFieldInstructor4) {
                $instructor4 = $course->fieldDataFieldInstructor4->fieldDataFieldInstructorNode4;
                if ($instructor4) {
                    $course->instructor4_url = $this->getUrlAlias('node/' . $instructor4->nid);
                }
            }
            // Instructor 5 URL
            if ($course->fieldDataFieldInstructor5) {
                $instructor5 = $course->fieldDataFieldInstructor5->fieldDataFieldInstructorNode5;
                if ($instructor5) {
                    $course->instructor5_url = $this->getUrlAlias('node/' . $instructor5->nid);
                }
            }
            // Instructor 6 URL
            if ($course->fieldDataFieldInstructor6) {
                $instructor6 = $course->fieldDataFieldInstructor6->fieldDataFieldInstructorNode6;
                if ($instructor6) {
                    $course->instructor6_url = $this->getUrlAlias('node/' . $instructor6->nid);
                }
            }
            // Instructor 7 URL
            if ($course->fieldDataFieldInstructor7) {
                $instructor7 = $course->fieldDataFieldInstructor7->fieldDataFieldInstructorNode7;
                if ($instructor7) {
                    $course->instructor7_url = $this->getUrlAlias('node/' . $instructor7->nid);
                }
            }
            // Instructor 8 URL
            if ($course->fieldDataFieldInstructor8) {
                $instructor8 = $course->fieldDataFieldInstructor8->fieldDataFieldInstructorNode8;
                if ($instructor8) {
                    $course->instructor8_url = $this->getUrlAlias('node/' . $instructor8->nid);
                }
            }

            // Instructor 9 URL
            if ($course->fieldDataFieldInstructor9) {
                $instructor9 = $course->fieldDataFieldInstructor9->fieldDataFieldInstructorNode9;
                if ($instructor9) {
                    $course->instructor9_url = $this->getUrlAlias('node/' . $instructor9->nid);
                }
            }

            // Instructor 10 URL
            if ($course->fieldDataFieldInstructor10) {
                $instructor10 = $course->fieldDataFieldInstructor10->fieldDataFieldInstructorNode10;
                if ($instructor10) {
                    $course->instructor10_url = $this->getUrlAlias('node/' . $instructor10->nid);
                }
            }

            if ($course->FieldDataFieldSessionLocLocation != null) {
                if ($course->FieldDataFieldSessionLocLocation->fieldDataFieldOnline->field_online_value == AppConstants::LOCATION_INPERSON) {
                    $locationDetails = $course->FieldDataFieldSessionLocLocation->fieldDataFieldSessionLocation->FieldDataFieldSessionLocationNode;
                    if ($locationDetails) {
                        $course->locationDetails_url = $this->getUrlAlias('node/' . $locationDetails->nid);
                    }
                }
            }
        });
    }

    private function getUrlAlias($source)
    {
        static $urlAliases = [];

        if (!isset($urlAliases[$source])) {
            $urlAlias = UrlAlias::where('source', $source)->first();
            $urlAliases[$source] = $urlAlias ? $urlAlias->alias : '';
        }

        return $urlAliases[$source];
    }


    public function showfiltercourse($request): object
    {

        try {
            $search = $request->input('title');

            $filtercourse = Node::join('field_data_field_active_course', 'field_data_field_active_course.entity_id', '=', 'node.nid')
                ->where('node.type', '=', AppConstants::NODETYPE_COURSE)
                ->where('node.status', '=', AppConstants::NODE_STATUS)
                ->where('field_data_field_active_course.field_active_course_value', '=', AppConstants::FIELD_ACTIVE_COURSE_VALUE)
                ->where('node.title', 'LIKE', '%' . $search . '%')
                ->select('node.title', 'node.nid')
                ->get()
                ->map(function ($courseurl) {
                    $urlAlias = UrlAlias::where('source', '=', 'node/' . $courseurl->nid)
                        ->select('alias')
                        ->first();
                    $courseurl->url = !empty($urlAlias) ? $urlAlias->alias : '';
                    return $courseurl;
                });


            return (object) $filtercourse;
        } catch (\Exception $e) {

            Log::error('Error in fetching course title', [
                'function' => 'showfiltercourse',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function showTestimonials(): object
    {
        try {
            $getTestimonials = Node::with('fieldDataBody', 'fieldDataFieldHomepageTestimonialImage.fileManagedHomepageTestimonialImage', 'fieldDataFieldSpeaker')
                ->where('node.type', AppConstants::NODETYPE_HOME_PAGE_TESTIMONIAL)
                ->where('node.status', '=', AppConstants::NODE_STATUS)
                //  EXCLUDE domain_id
                ->whereNotExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('domain_access')
                        ->whereColumn('domain_access.nid', 'node.nid')
                        ->where('gid', 4); // or AppConstants::BARNESCONTI_DOMAIN_ID
                })

                ->get();

            return $getTestimonials;
        } catch (Exception $e) {
            Log::error('Error in fetching Testimonials', [
                'function' => 'showTestimonials',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return collect();
        }
    }
    public function showMenubar(): object
    {
        try {
            $menuCustomDetails = MenuCustom::where('menu_name', Appconstants::PROCEPT_MENU_LINKS)->first();
            $menuCustomDetails = MenuCustom::where('menu_name', Appconstants::PROCEPT_MENU_LINKS)->first();

            $menuBarDetails = MenuLinks::where('menu_name', $menuCustomDetails->menu_name)
                ->where('p2', 0)
                ->where('p3', 0)
                ->where('p4', 0)
                ->where('p5', 0)
                ->where('p6', 0)
                ->where('p7', 0)
                ->where('p8', 0)
                ->where('p9', 0)
                ->with(['subMenu.megaMenu'])
                ->where('hidden', 0)
                ->orderBy('weight', 'asc')
                ->get()
                ->map(function ($item) {
                    $urlAlias = UrlAlias::where('source', $item->link_path)->first();
                    $item->link_url = $urlAlias ? $urlAlias->alias : '';

                    $item->subMenu->map(function ($subMenuItem) {
                        $subUrlAlias = UrlAlias::where('source', $subMenuItem->link_path)->first();
                        $subMenuItem->link_url = $subUrlAlias ? $subUrlAlias->alias : '';
                        if ($subMenuItem->megaMenu) {

                            $subMenuItem->megaMenu->map(function ($megaMenuItem) {
                                $megaUrlAlias = UrlAlias::where('source', $megaMenuItem->link_path)->first();
                                $megaMenuItem->link_url = $megaUrlAlias ? $megaUrlAlias->alias : '';
                                return $megaMenuItem;
                            });
                        }

                        return $subMenuItem;
                    });

                    return $item;
                });

            return (object) $menuBarDetails;
        } catch (Exception $e) {
            Log::error('Error in fetching Menu Bar', [
                'function' => 'showMenubar',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function industryRecoginationLogoslider(): object
    {

        try {
            $logoslider = IndustryRecognitionImages::join('file_managed', 'file_managed.fid', 'industry_recognition_images.fid')->get();
            return (object) $logoslider;
        } catch (Exception $e) {
            Log::error('Error in fetching industry recogination logo slider', [
                'function' => 'industryRecoginationLogoslider',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function contactInformationBlock(): object
    {
        try {
            $contactInformation = Block::join('block_custom', 'block_custom.bid', '=', 'block.delta')
                ->where('pages', AppConstants::CONTACT)
                ->where('visibility', AppConstants::VISIBILITY)
                ->where('theme', AppConstants::EXCEPTION)
                ->get()
                ->map(function ($item) {
                    $content = $item->body;
                    $content = preg_replace(
                        '/\[animate type="(.*?)"\]/',
                        AppConstants::ANIMATE_DELAY . ' ' . '$1 ' . AppConstants::ANIMATE_CLASS . '$1">',
                        $content
                    );
                    $content = str_replace(AppConstants::CLOSE_ANIMATE, AppConstants::CLOSE_DIV, $content);
                    $content = str_replace(AppConstants::COLUMN_COLS_FIVE, AppConstants::ROW_COLS_FIVE, $content);
                    $content = str_replace(AppConstants::COLUMN_COLS_END_FIVE, AppConstants::ROW_COLS_END_FIVE, $content);
                    $content = str_replace(AppConstants::CLOSE_COLUMN, AppConstants::CLOSE_DIV, $content);
                    $item->body = $content;
                    return $item;
                });
            $siteKeyDetails = Variable::where('name', AppConstants::RECAPTCHA_SITE_KEY)->first();
            $recaptchaSiteKey = is_array($val = unserialize($siteKeyDetails->value ?? '')) ? reset($val) : $val;
            return (object) [
                'contactInformation' => $contactInformation,
                'recaptchaSiteKey' => $recaptchaSiteKey,
            ];
        } catch (Exception $e) {
            Log::error('Error in fetching contact information block', [
                'function' => 'contactInformationBlock',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function getRssFeed(): object
    {
        try {
            $allRssFeed = Node::with('fieldDataBody')
                ->leftJoin('field_data_field_media', function ($join) {
                    $join->on('field_data_field_media.entity_id', '=', 'node.nid')
                        ->where('field_data_field_media.delta', '=', 0);
                })
                ->leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_media.field_media_fid')
                ->where('node.type', AppConstants::NODETYPE_ARTICLE)
                ->where('node.promote', AppConstants::NODE_PROMOTE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->orderBy('node.nid', 'desc')
                ->get()
                ->take(10)
                ->map(function ($item) {
                    $urlAlias = UrlAlias::where('source', 'node/' . $item->nid)->first();
                    $item->url = $urlAlias ? $urlAlias->alias : '';
                    $pageTitle = explode(':', $item->title);
                    $item->titleOne = $pageTitle[1] ?? '';
                    $item->titleTwo = $pageTitle[0] ?? '';
                    return $item;
                });

            return (object) $allRssFeed;
        } catch (\Exception $e) {

            Log::error('Error in fetching company news in rss feed', [
                'function' => 'getRssFeed',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function getAllUpcomingCourses($request,$nodeId): object
    {

        try {

            $todayDate = now()->startOfDay();

            $metaDetails = $this->getMetaTagDetails($nodeId);

            //Courses Content
            $getAllUpcomingCourses = Node::with('fieldDataFieldCourseId', 'fieldDataFieldDuration')
                ->LeftJoin('field_data_field_course_level', 'field_data_field_course_level.entity_id', 'node.nid')
                ->LeftJoin('taxonomy_term_data', 'taxonomy_term_data.tid', 'field_data_field_course_level.field_course_level_tid')
                ->LeftJoin('field_data_field_abstract', 'field_data_field_abstract.entity_id', '=', 'node.nid')
                ->LeftJoin('field_data_field_brochure_link', 'field_data_field_brochure_link.entity_id', 'node.nid')
                ->LeftJoin('field_data_field_pdu', 'field_data_field_pdu.entity_id', 'node.nid')
                ->where('node.nid', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->first();

               if (strpos($getAllUpcomingCourses->field_pdu_value, AppConstants::BASIC_PAGE_BUTTON) !== false) {
                $getAllUpcomingCourses->field_pdu_value = str_replace(AppConstants::BASIC_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getAllUpcomingCourses->field_pdu_value);
            } elseif (strpos($getAllUpcomingCourses->field_pdu_value, AppConstants::BLOG_PAGE_BUTTON) !== false) {
                $getAllUpcomingCourses->field_pdu_value = str_replace(AppConstants::BLOG_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getAllUpcomingCourses->field_pdu_value);
            }

            if (strpos($getAllUpcomingCourses->field_pdu_value, AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE) !== false) {
                $getAllUpcomingCourses->field_pdu_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE, '</button>', $getAllUpcomingCourses->field_pdu_value);
            }
            //Courses testinomials
            $getTestinomials = FieldDataFieldCourseTarget::with('FieldDataBodyCourse', 'FieldDataFieldTestimonialPositionNode', 'FieldDataFieldTestimonialImage.FileManagedTestimonialImage')
                ->join('node', 'node.nid', 'field_data_field_course_target.entity_id')
                ->LeftJoin('field_data_field_author', 'field_data_field_author.entity_id', 'node.nid')
                ->where('field_data_field_course_target.field_course_target_target_id', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->get();

            //Courses upcoming session
            $getUpcomingSession = Node::with('fieldDataFieldCourseInstructor.fieldCourseInstructorNode', 'fieldDataFieldInstructor1.fieldDataFieldInstructorNode1')
                ->join('field_data_field_course', 'field_data_field_course.entity_id', '=', 'node.nid')
                ->join('field_data_field_choose_session_type', 'field_data_field_choose_session_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_session_dates', 'field_data_field_session_dates.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_start_date1', 'field_data_field_start_date1.entity_id', '=', 'node.nid')
                ->join('field_data_field_resale', 'field_data_field_resale.entity_id', 'node.nid')
                ->join('field_data_field_procept_sell_ticket_course', 'field_data_field_procept_sell_ticket_course.entity_id', 'field_data_field_resale.field_resale_value')
                ->join('field_data_field_if_yes_eventbrite_link', 'field_data_field_if_yes_eventbrite_link.entity_id', 'field_data_field_resale.field_resale_value')
                ->join('field_data_field_session_loc_location', 'field_data_field_session_loc_location.entity_id', 'node.nid')
                ->join('field_data_field_online', 'field_data_field_online.entity_id', 'field_data_field_session_loc_location.field_session_loc_location_value')
                ->where(function ($query) {
                    $query->where('field_data_field_choose_session_type.field_choose_session_type_value', AppConstants::SESSION_TYPE_CONTIGUOUS)
                        ->orWhere('field_data_field_choose_session_type.field_choose_session_type_value', AppConstants::SESSION_TYPE_BROKEN_UP);
                })
                ->where(function ($query) use ($todayDate) {
                    $query->where('field_data_field_session_dates.field_session_dates_value', '>=', $todayDate)
                        ->orWhere('field_data_field_start_date1.field_start_date1_value', '>=', $todayDate);
                })
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('field_data_field_course.field_course_target_id', $nodeId)
                ->where('field_data_field_procept_sell_ticket_course.field_procept_sell_ticket_course_value', AppConstants::FIELD_RESALES_YES)
                ->orderByRaw('COALESCE(field_data_field_session_dates.field_session_dates_value,field_data_field_start_date1.field_start_date1_value) ASC')
                ->take(3)
                ->get();

            //courses video testinomials
            $getVideoTestinomials = Block::join('block_custom', 'block_custom.bid', '=', 'block.delta')
                ->where('pages', 'node/' . $nodeId)
                ->where('visibility', AppConstants::VISIBILITY)
                ->where('status', AppConstants::NODE_STATUS)
                ->where('theme', AppConstants::EXCEPTION)->first();


            if ($getVideoTestinomials && !empty($getVideoTestinomials->body)) {

                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML($getVideoTestinomials->body);
                libxml_clear_errors();
                $iframes = $dom->getElementsByTagName('iframe');
                $iframeSrcs = [];

                foreach ($iframes as $iframe) {

                    $src = $iframe->getAttribute('src');
                    $iframeSrcs[] = $src;
                }
            } else {
                $iframeSrcs = [];
            }

            //courses type
            $getCoursesType = FieldDataFieldCourseType::where('entity_id', $nodeId)->first();

            if ($getCoursesType->field_course_type_tid) {
                $urlAlias = UrlAlias::where('source', AppConstants::TAXONOMY_TERM_COURSE . $getCoursesType->field_course_type_tid)
                    ->select('alias')
                    ->first();
                $aliasParts = explode('/', $urlAlias->alias);
            }
            if ($urlAlias) {
                $courseTypeImage = $this->getCourseTypeImageDetails($aliasParts[1]);
            }

            $trainingDetails = $this->getContentByAlias($nodeId);



            return (object) [
                'course' => $getAllUpcomingCourses,
                'testimonials' => $getTestinomials,
                'upcomingSession' => $getUpcomingSession,
                'getVideoTestinomials' => $getVideoTestinomials,
                'videolinkiframe' => $iframeSrcs,
                'getCourseTypeImage' => $courseTypeImage,
                'trainingDetails' => $trainingDetails,
                'metaDetails' => $metaDetails
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all upcoming courses', [
                'function' => 'getAllUpcomingCourses',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function getAllUpcomingCoursesSessions($request): object
    {
        try {

            $todayDate = now()->startOfDay();


            $nodeId = $request->get('nodeId');
            $getCourseTitle = Node::where('node.nid', $nodeId)->first();

            $getAllUpcomingCoursesSessions = Node::with([
                'fieldDataFieldCourseInstructor.fieldCourseInstructorNode',
                'fieldDataFieldInstructor1.fieldDataFieldInstructorNode1',
                'fieldDataFieldInstructor2.fieldDataFieldInstructorNode2',
                'fieldDataFieldInstructor3.fieldDataFieldInstructorNode3',
                'fieldDataFieldInstructor4.fieldDataFieldInstructorNode4',
                'fieldDataFieldInstructor5.fieldDataFieldInstructorNode5',
                'fieldDataFieldInstructor6.fieldDataFieldInstructorNode6',
                'fieldDataFieldInstructor7.fieldDataFieldInstructorNode7',
                'fieldDataFieldInstructor8.fieldDataFieldInstructorNode8',
                'fieldDataFieldInstructor9.fieldDataFieldInstructorNode9',
                'fieldDataFieldInstructor10.fieldDataFieldInstructorNode10',
                'FieldDataFieldSessionLocLocation.fieldDataFieldOnline',
                'FieldDataFieldSessionLocLocation.fieldDataFieldSessionLocation.FieldDataFieldSessionLocationNode'
            ])
                ->join('field_data_field_choose_session_type', 'field_data_field_choose_session_type.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_session_dates', 'field_data_field_session_dates.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_start_date1', 'field_data_field_start_date1.entity_id', '=', 'node.nid')
                ->join('field_data_field_course', 'field_data_field_course.entity_id', '=', 'node.nid')
                ->join('field_data_field_resale', 'field_data_field_resale.entity_id', 'node.nid')
                ->join('field_data_field_procept_sell_ticket_course', 'field_data_field_procept_sell_ticket_course.entity_id', 'field_data_field_resale.field_resale_value')
                ->join('field_data_field_if_yes_eventbrite_link', 'field_data_field_if_yes_eventbrite_link.entity_id', 'field_data_field_resale.field_resale_value')->where(function ($query) {
                    $query->where('field_data_field_choose_session_type.field_choose_session_type_value', AppConstants::SESSION_TYPE_CONTIGUOUS)
                        ->orWhere('field_data_field_choose_session_type.field_choose_session_type_value', AppConstants::SESSION_TYPE_BROKEN_UP);
                })
                ->where(function ($query) use ($todayDate) {
                    $query->where('field_data_field_session_dates.field_session_dates_value', '>=', $todayDate)
                        ->orWhere('field_data_field_start_date1.field_start_date1_value', '>=', $todayDate);
                })
                ->where('field_data_field_procept_sell_ticket_course.field_procept_sell_ticket_course_value', AppConstants::FIELD_RESALES_YES)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('field_data_field_course.field_course_target_id', $nodeId)
                ->orderByRaw('COALESCE(field_data_field_session_dates.field_session_dates_value,field_data_field_start_date1.field_start_date1_value) ASC')
                ->paginate(10);

            $this->getUpcomingPublicCourseUrl($getAllUpcomingCoursesSessions);
            $metaDetails = $this->getMetaTagDetails($nodeId);
            return (object) [
                'getAllUpcomingCoursesSessions' => $getAllUpcomingCoursesSessions,
                'getCourseTitle' => $getCourseTitle,
                'metaDetails' => $metaDetails
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all upcoming courses', [
                'function' => 'getAllUpcomingCoursesSessions',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }


    public function getAllCourseType($request, $course_type_slug): object
    {
        try {

            $courseTypeUrl = '';
            //dd($course_type_slug);
            if ($course_type_slug == AppConstants::COURSE_TYPE_PM_COURSES) {
                //pm
                $courseTypeUrl = AppConstants::COURSE_TYPE_PM_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_BDA_COURSES) {
                //bd
                $courseTypeUrl = AppConstants::COURSE_TYPE_BDA_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_IT_COURSES) {
                //it
                $courseTypeUrl = AppConstants::COURSE_TYPE_IT_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_MANAGEMENT_COURSES) {
                //mag
                $courseTypeUrl = AppConstants::COURSE_TYPE_MANAGEMENT_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_AGILE_COURSES) {
                //agile
                $courseTypeUrl = AppConstants::COURSE_TYPE_AGILE_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_TRAIN_THE_TRAINER_COURSES) {
                //train
                $courseTypeUrl = AppConstants::COURSE_TYPE_TRAIN_THE_TRAINER_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_CM_COURSES) {
                //cm
                $courseTypeUrl = AppConstants::COURSE_TYPE_CM_COURSES_NID;
            } elseif ($course_type_slug == AppConstants::COURSE_TYPE_LEADERSHIP_COURSES) {
                //cm
                $courseTypeUrl = AppConstants::COURSE_TYPE_LEADERSHIP_COURSES_NID;
            }


            $urlAlias = UrlAlias::where('alias', AppConstants::MENU_BAR_COURSE_TYPE . $course_type_slug)->first();

            if ($urlAlias) {
                $taxonomyTermCourse = preg_quote(AppConstants::TAXONOMY_TERM_COURSE, '/');
                $pattern = '/' . $taxonomyTermCourse . '(\d+)/';
                $matches = [];
                if (preg_match($pattern, $urlAlias->source, $matches)) {
                    $nodeId = $matches[1];
                } else {
                    $nodeId = null;
                }
            } else {
                $nodeId = null;
            }

            $getcourseslevel = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::TAXONOMY_VOCABULARY_NAME)
                ->leftJoin('taxonomy_term_data', 'taxonomy_vocabulary.vid', '=', 'taxonomy_term_data.vid')
                ->leftJoin('field_data_field_course_level', 'taxonomy_term_data.tid', '=', 'field_data_field_course_level.field_course_level_tid')
                ->leftJoin('node', 'field_data_field_course_level.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_course_id', 'node.nid', '=', 'field_data_field_course_id.entity_id')
                ->leftJoin('field_data_field_course_type', 'node.nid', '=', 'field_data_field_course_type.entity_id')
                ->leftJoin('field_data_field_duration', 'field_data_field_course_type.entity_id', '=', 'field_data_field_duration.entity_id')
                ->join('field_data_field_active_course', 'field_data_field_active_course.entity_id', '=', 'node.nid')
                ->where('field_data_field_course_type.field_course_type_tid', '=', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('field_data_field_active_course.field_active_course_value', AppConstants::FIELD_PUBLIC_VALUE)
                ->orderBy('node.title', 'asc')
                ->get()->map(function ($getcourseslevel) {
                    $urlAlias = UrlAlias::where('source', '=', 'node/' . $getcourseslevel->entity_id)
                        ->select('alias')
                        ->first();
                    $getcourseslevel->course_url = !empty($urlAlias) ? $urlAlias->alias : '';
                    return $getcourseslevel;
                });

            $trainingDetails = $this->getContentByAlias($courseTypeUrl);

            if (isset($trainingDetails->body_value)) {
                $trainingDetails->body_value = $this->getUrlContent($trainingDetails->body_value);
                $trainingDetails->body_value = $this->updateImageUrls($trainingDetails->body_value);
            }
            $getcoursesTypeImage = $this->getCourseTypeImageDetails($course_type_slug);
            $metaDetails = $this->getMetaTagDetails($courseTypeUrl);
            return (object) [
                'trainingDetails' => $trainingDetails,
                'getcourseslevel' => $getcourseslevel,
                'getcoursesTypeImage' => $getcoursesTypeImage,
                'metaDetails' => $metaDetails
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all course type', [
                'function' => 'getAllCourseType',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    private function updateImageUrls($bodyValue)
    {
        return preg_replace_callback(
            '/<img\s+[^>]*src=["\']([^"\']+)["\'][^>]*>/i',
            function ($matches) {
                $url = $matches[1];
                $newUrl = $url;
                if (strpos($url, '/sites') === 0) {
                    $newUrl = env('TRAINING_URL') . $url;
                } elseif (strpos($url, '/images') === 0) {
                    $newUrl =  env('TRAINING_URL') . $url;
                } else {
                    $newUrl = $url;
                }
                return str_replace($url, $newUrl, $matches[0]);
            },
            $bodyValue
        );
    }

public function showfilterPageCourse($request): object
{
    try {
        $search = $request->input('title');

        $filterPageCourse = Node::leftjoin('field_data_field_active_course', 'field_data_field_active_course.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_event_type', 'field_data_field_event_type.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_members_visible', 'field_data_field_members_visible.entity_id', '=', 'node.nid')
            ->leftJoin('url_alias', 'url_alias.source', '=', DB::raw("CONCAT('node/', node.nid)"))

            ->where(function ($query) {
                $query->where('node.type', AppConstants::ARTICLE)
                    ->orWhere('node.type', AppConstants::PAGE)
                    ->orWhere(function ($query) {
                        $query->where('node.type', AppConstants::NODETYPE_COURSE)
                            ->where('field_data_field_active_course.field_active_course_value', AppConstants::FIELD_ACTIVE_COURSE_VALUE);
                    })
                    ->orWhere(function ($query) {
                        $query->where('node.type', AppConstants::NODETYPE_EVENTS)
                            ->where('field_data_field_event_type.field_event_type_value', AppConstants::FIELD_EVENT_TYPE_VALUE);
                    })
                    ->orWhere(function ($query) {
                        $query->where('node.type', AppConstants::TEAM)
                            ->where('field_data_field_members_visible.field_members_visible_value', AppConstants::FIELD_RESALES_YES);
                    });
            })

            ->where('node.status', AppConstants::NODE_STATUS)

            //  exclude Barnes & Conti (domain_id = 4)
            ->whereNotIn('node.nid', function ($query) {
                $query->select('nid')
                    ->from('domain_access')
                    ->where('gid', '=', AppConstants::BARNESCONTI_DOMAIN_ID);
            })

            ->where('node.title', 'LIKE', '%' . $search . '%')

            ->select('node.*', 'url_alias.alias as url')

            ->get();

        return (object) $filterPageCourse;

    } catch (\Exception $e) {

        Log::error('Error in fetching filter course page', [
            'function' => 'showfilterPageCourse',
            'line' => $e->getLine(),
            'message' => $e->getMessage()
        ]);

        return (object) [];
    }
}
    public function getAllArticlePage($request): object
    {
        try {
            $nodeId = $request->get('nodeId');

            $getArticlePage = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')->where('node.type', AppConstants::ARTICLE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('node.nid', $nodeId)
                ->first();
            $metaDetails = $this->getMetaTagDetails($nodeId);
            if ($getArticlePage && $getArticlePage->body_value) {

                if ($getArticlePage && $getArticlePage->body_value) {
                    $getArticlePage->body_value = $this->getUrlContent($getArticlePage->body_value);

                    $getArticlePage->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getArticlePage->body_value);

                    $getArticlePage->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE, '</button>', $getArticlePage->body_value);
                }
            }
            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $getArticlePage,
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all article page', [
                'function' => 'getAllArticlePage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }


    public function getAllBasicPage($request,$nodeId): object
    {

        try {
            $metaDetails = $this->getMetaTagDetails($nodeId);
            $getBasicPage = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')->where('node.type', AppConstants::PAGE)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('node.nid', $nodeId)
                ->first();
            $getBasicPage->body_value = $this->getAnimateRowColumn($getBasicPage->body_value);
            $content = preg_replace('/\[tabs class="vertical"\]/', AppConstants::ABOUTS_PAGE_NAV_TABS, $getBasicPage->body_value);
            if (!empty($getBasicPage->body_value)) {
                $getBasicPage->body_value = $this->getProceptChangeUrl($getBasicPage->body_value);
            } else {
                $getBasicPage->body_value = $this->getUrlContent($getBasicPage->body_value);
            }
            switch (true) {
                case strpos($content, AppConstants::ABOUTS_PAGE_NAV_TABS) !== false:
                    $getBasicPage->body_value = $this->parseTabs($getBasicPage->body_value);
                case strpos($getBasicPage->body_value, AppConstants::BASIC_PAGE_BUTTON) !== false:
                    $getBasicPage->body_value = str_replace(AppConstants::BASIC_PAGE_BUTTON, AppConstants::DEXP_BUTTON, $getBasicPage->body_value);
                case strpos($getBasicPage->body_value, AppConstants::BLOG_PAGE_BUTTON) !== false:
                    $getBasicPage->body_value = str_replace(AppConstants::BLOG_PAGE_BUTTON, AppConstants::DEXP_BUTTON, $getBasicPage->body_value);
                case strpos($getBasicPage->body_value, AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE) !== false:
                    $getBasicPage->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE, AppConstants::CLOSE_BUTTON, $getBasicPage->body_value);
                case preg_match('/<table\s+[^>]*border="0"[^>]*>/i', $getBasicPage->body_value):
                    $getBasicPage->body_value = preg_replace('/<table\s+[^>]*border="0"[^>]*>/i', AppConstants::TABLE_BORDER, $getBasicPage->body_value);
                case strpos($getBasicPage->body_value, AppConstants::FIX_MISPLACED_P_IN_DIV) !== false:
                        $getBasicPage->body_value = str_replace(AppConstants::FIX_MISPLACED_P_IN_DIV, '', $getBasicPage->body_value);
                default:
                    $getBasicPage->body_value = $this->getUrlContent($getBasicPage->body_value);
            }

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $getBasicPage,
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all basic page', [
                'function' => 'getAllBasicPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }



    public function getCoursesDetails($request, $course_alias, $course_type_alias): object
    {

        try {
            $urlAlias = UrlAlias::where('alias', AppConstants::MENU_BAR_COURSE_TYPE . $course_type_alias)->first();


            $taxonomyTermCourse = preg_quote(AppConstants::TAXONOMY_TERM_COURSE, '/');
            $pattern = '/' . $taxonomyTermCourse . '(\d+)/';
            $matches = [];
            if (preg_match($pattern, $urlAlias->source, $matches)) {
                $nodeId = $matches[1];
            } else {
                $nodeId = null;
            }

            $nodeId = $matches[1];

            $getCoursesDetails = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::TAXONOMY_VOCABULARY_NAME)
                ->leftJoin('taxonomy_term_data', 'taxonomy_vocabulary.vid', '=', 'taxonomy_term_data.vid')
                ->leftJoin('field_data_field_course_level', 'taxonomy_term_data.tid', '=', 'field_data_field_course_level.field_course_level_tid')
                ->leftJoin('node', 'field_data_field_course_level.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_course_id', 'node.nid', '=', 'field_data_field_course_id.entity_id')
                ->leftJoin('field_data_field_course_type', 'node.nid', '=', 'field_data_field_course_type.entity_id')
                ->leftJoin('field_data_field_duration', 'field_data_field_course_type.entity_id', '=', 'field_data_field_duration.entity_id')
                ->leftJoin('field_data_field_active_course', 'field_data_field_active_course.entity_id', '=', 'node.nid')
                ->where('field_data_field_course_type.field_course_type_tid', '=', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('field_data_field_active_course.field_active_course_value', AppConstants::FIELD_PUBLIC_VALUE)
                ->orderBy('node.title', 'asc')
                ->get()->map(function ($getPMCourses) {
                    $urlAlias = UrlAlias::where('source', '=', 'node/' . $getPMCourses->entity_id)
                        ->select('alias')
                        ->first();
                    $getPMCourses->course_url = !empty($urlAlias) ? $this->getBaseUrl($urlAlias->alias) : '';

                    return $getPMCourses;
                });

            $metaDetails = $this->getMetaTagDetails($course_alias);


            $getCoursesTypeImage = $this->getCourseTypeImageDetails($course_type_alias);

            $trainingDetails = $this->getContentByAlias($course_alias);

            if (isset($trainingDetails->body_value)) {
                $trainingDetails->body_value = $this->getUrlContent($trainingDetails->body_value);

                $trainingDetails->body_value = $this->updateImageUrls($trainingDetails->body_value);
            }

            return (object) [
                'trainingDetails' => $trainingDetails,
                'getCoursesDetails' => $getCoursesDetails,
                'getCoursesTypeImage' => $getCoursesTypeImage,
                'metaDetails' => $metaDetails
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching Courses Page Details', [
                'function' => 'getCoursesDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function getPMCoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_PM_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_PM_COURSES;
        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }
    public function getCMCoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_CM_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_CM_COURSES;

        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }

    public function getBACoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_BDA_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_BDA_COURSES;
        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }
    public function getleadershipCoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_LEADERSHIP_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_LEADERSHIP_COURSES;
        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }
    public function getAgileCoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_AGILE_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_AGILE_COURSES;
        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }

    public function getManagementCoursesPage($request)
    {
        $course_alias = AppConstants::COURSE_TYPE_MANAGEMENT_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_MANAGEMENT_COURSES;
        return $this->getCoursesDetails($request, $course_alias, $course_type_alias);
    }

    public function getCourseTypeImageDetails($course_type_url): string
    {
        try {

            if ($course_type_url == AppConstants::COURSE_TYPE_PM_COURSES) {
                $courseTypeImage = AppConstants::COURSE_TYPE_PM_COURSES_IMAGE;
            } else {
                $courseTypeImage = AppConstants::COURSE_TYPE_CM_COURSES_IMAGE;
            }

            return (string) $courseTypeImage;
        } catch (\Exception $e) {

            Log::error('Error in fetching get course type image details', [
                'function' => 'getCourseTypeImageDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (string) [];
        }
    }
    public function getCanadaJobGrantPage()
    {
        return $this->showPageContent(AppConstants::PAGE_CANADA_JOB_GRANT);
    }
    public function getHistoryPage()
    {
        return $this->showPageContent(AppConstants::HISTORY);
    }
    public function getAccessibilityPolicyPage()
    {
        return $this->showPageContent(AppConstants::ACCESSIBILITY_POLICY_NID);
    }
    public function getDiversityPolicyPage()
    {
        return $this->showPageContent(AppConstants::DIVERSITY_POLICY_NID);
    }
    public function getDrugAndAlcoholPolicyPage()
    {
        return $this->showPageContent(AppConstants::DRUG_AND_ALCOHOL_POLICY_NID);
    }
    public function getEnvironmentalPolicyPage()
    {
        return $this->showPageContent(AppConstants::ENVIRONMENTAL_POLICY_NID);
    }
    public function getHealthAndSafetyPolicyPage()
    {
        return $this->showPageContent(AppConstants::HEALTH_AND_SAFETY_POLICY_NID);
    }

    public function getPolicyRecordingTrainingSessionsPage()
    {
        try {
            $metaDetails = $this->getMetaTagDetails(AppConstants::POLICY_RECORDING_TRAINING_SESSIONS_NID);
            $content = $this->getContentByAlias(AppConstants::POLICY_RECORDING_TRAINING_SESSIONS_NID);
            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $content,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Policy Recording Training Sessions page content', [
                'function' => 'getPolicyRecordingTrainingSessionsPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function getConsultantsDetails($request, $consultant_tab): object
    {

        try {

            $currentTab = $request->input('tab', strtolower(str_replace(' ', '-', $consultant_tab)));



            $consultantsTypes = [
                AppConstants::OUR_CONSULTANTS => [
                    AppConstants::PM_CONSULTANT,
                    AppConstants::BA_CONSULTANT,
                    AppConstants::IT_CONSULTANT,
                    AppConstants::AGILE_CONSULTANT,
                    AppConstants::CM_CONSULTANT,
                    AppConstants::MANAGEMENT_CONSULTANT,
                    AppConstants::HR_CONSULTANT,
                    AppConstants::LEADERSHIP_CONSULTANT,
                ],
                AppConstants::PM_CONSULTANTS => [
                    AppConstants::PM_CONSULTANT
                ],
                AppConstants::BA_CONSULTANTS => [
                    AppConstants::BA_CONSULTANT
                ],
                AppConstants::AGILE_CONSULTANTS => [
                    AppConstants::AGILE_CONSULTANT
                ],
                AppConstants::MANAGEMENT_CONSULTANTS => [
                    AppConstants::MANAGEMENT_CONSULTANT
                ],
                AppConstants::HR_CONSULTANTS => [
                    AppConstants::HR_CONSULTANT
                ],
                AppConstants::IT_CONSULTANTS => [
                    AppConstants::IT_CONSULTANT
                ],
            ];

            $consultants_type_details = [
                [
                    'consultants_type' => AppConstants::OUR_CONSULTANTS,

                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::OUR_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::PM_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::PM_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::BA_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::BA_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::AGILE_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::AGILE_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::MANAGEMENT_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::MANAGEMENT_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::HR_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::HR_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
                [
                    'consultants_type' => AppConstants::IT_CONSULTANTS,
                    'consultants_details' => $this->paginateCollection(
                        $this->getConsultantsQueryData($consultantsTypes[AppConstants::IT_CONSULTANTS])->unique('nid'),
                        20
                    )
                ],
            ];
            $metaTags = [];

            $metaTagGlobalConfig = MetatagConfig::where('instance', AppConstants::META_GLOBAL)->first();
            $metaTagsGlobal = unserialize($metaTagGlobalConfig->config ?? '') ?: [];
            $nodeId = '';
            $nodeTitle = AppConstants::OUR_CONSULTANTS;
            $nodeSummary = $metaTagsGlobal['description']['value'] ?? '';
            $nodeAbstract = '';
            $formattedDate = '';
            $formattedChangedDate = '';
            $siteSlogan = '';

            $currentUrl = url()->current();
            $ogDescription = '';

            $getMetaGlobalDetails = $this->getMetaGlobalDetails();
            $getMetaGlobalData = [
                $nodeTitle,
                '',
                AppConstants::SITE_NAME,
                $nodeSummary,
                $nodeTitle,
                $currentUrl,
                $formattedDate,
                $formattedChangedDate,
                env('APP_URL'),
                $siteSlogan
            ];

            $metaDetailsData = $this->getMetaTagGlobal($metaTags, $metaTagsGlobal, $nodeTitle, $nodeSummary, $nodeAbstract, $formattedDate, $formattedChangedDate, $ogDescription, $nodeId);

            $metaDetails = $this->replaceMetaDetails($metaDetailsData, $getMetaGlobalDetails, $getMetaGlobalData);

            return (object) [
                'consultants_type_details' => $consultants_type_details,
                'current_tab' => $currentTab,
                'metaDetails' => $metaDetails,
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching consultants details', [
                'function' => 'getConsultantsDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }




    public function getConsultantsQueryData(array $consultantTypes)
    {
        return Node::leftJoin('field_data_field_teamtype', 'field_data_field_teamtype.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_team_image', 'field_data_field_team_image.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_members_visible', 'field_data_field_members_visible.entity_id', '=', 'node.nid')
            ->leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_team_image.field_team_image_fid')
            ->leftJoin('field_data_field_credentials', 'field_data_field_credentials.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_team_position', 'field_data_field_team_position.entity_id', '=', 'node.nid')
            ->where('field_data_field_members_visible.field_members_visible_value', AppConstants::FIELD_RESALES_YES)
            ->where('node.type', AppConstants::TEAM)
            ->where('node.status', AppConstants::NODE_STATUS)
            ->whereIn('field_data_field_teamtype.field_teamtype_value', $consultantTypes)
            ->orderBy('node.title', 'asc')
            ->get()->map(function ($getConsultant) {
                $urlAlias = UrlAlias::where('source', '=', 'node/' . $getConsultant->entity_id)
                    ->select('alias')
                    ->first();

                $getConsultant->team_url = !empty($urlAlias) ? $urlAlias->alias : '';
            if (empty($getConsultant->team_url)) {
                $fallbackAlias = UrlAlias::where('source', 'node/' . $getConsultant->nid)
                    ->whereIn('language', ['en', 'und'])
                    ->value('alias');

                $getConsultant->team_url = $fallbackAlias ?? '';
            }
                return $getConsultant;
            });
    }

    protected function paginateCollection($collection, $perPage)
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $currentItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->all();
        return new LengthAwarePaginator($currentItems, $collection->count(), $perPage, $currentPage, [
            'path' => LengthAwarePaginator::resolveCurrentPath()
        ]);
    }


    public function partnersLogoslider($request)
    {

        try {
            $getPartnersDetails = $this->getContentByAlias(AppConstants::PARTNERS_NID);
            $Logoslider = $this->extractImagesAndUrls($getPartnersDetails->body_value);

            return $Logoslider;
        } catch (Exception $e) {
            Log::error('Error in fetching partners logo slider', [
                'function' => 'partnersLogoslider',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }



    public function extractImagesAndUrls(string $html): array
    {
        $dom = new \DOMDocument();


        libxml_use_internal_errors(true);
        $dom->loadHTML($html);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $rows = $xpath->query('//tr');
        $results = [];
        foreach ($rows as $row) {
            $tds = $row->getElementsByTagName('td');

            if ($tds->length === 0) {
                continue;
            }

            $firstTd = $tds->item(0);
            $image = $firstTd->getElementsByTagName('img')->item(0);
            $link = $firstTd->getElementsByTagName('a')->item(0);

            $imageSrc = $image ? $image->getAttribute('src') : null;
            $linkHref = $link ? $link->getAttribute('href') : null;
            $imgStyle = $image ? $image->getAttribute('style') : null;

            $results[] = [
                'image' => $imageSrc,
                'url' => $linkHref,
                'style' => $imgStyle,
            ];
        }

        return $results;
    }


    public function getAllTeam($request,$nodeId): object
    {
        try {
            $metaDetails = $this->getMetaTagDetails($nodeId);

            $getTeamPage = Node::leftJoin('field_data_field_members_visible', 'field_data_field_members_visible.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_credentials', 'field_data_field_credentials.entity_id', '=', 'node.nid')
                ->leftJoin('field_data_field_team_image', 'field_data_field_team_image.entity_id', '=', 'node.nid')
                ->leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_team_image.field_team_image_fid')
                ->leftJoin('field_data_field_team_summary', 'field_data_field_team_summary.entity_id', '=', 'node.nid')
                ->where('node.type', AppConstants::TEAM)
                ->where('field_data_field_members_visible.field_members_visible_value', AppConstants::FIELD_RESALES_YES)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('node.nid', $nodeId)
                ->first();

            if (!$getTeamPage) {
                throw new \Exception("Team page not found.");
            }


            if (isset($getTeamPage->field_team_summary_value)) {
                $getTeamPage->field_team_summary_value = $this->updateImageUrls($getTeamPage->field_team_summary_value);
            }

            $getTeamTestinomial = $this->getTeamTestimonialsPage($nodeId);
            $getTeamVideoTestimonials = $this->getTeamVideoTestimonialsPage($nodeId);
            $getTeamLinks = $this->getTeamLinksPage($nodeId);
            $getSpecialties = $this->getSpecialtiesAreas($nodeId);
            $getSpecialtiesIndustry = $this->getSpecialtiesIndustry($nodeId);
            return (object) [
                'getTeamPage' => $getTeamPage,
                'getTeamTestinomial' => $getTeamTestinomial,
                'getTeamVideoTestimonials' => $getTeamVideoTestimonials,
                'getTeamLinks' => $getTeamLinks,
                'getSpecialties' => $getSpecialties,
                'getSpecialtiesindustry' => $getSpecialtiesIndustry,
                'metaDetails' => $metaDetails,
            ];
        } catch (\Exception $e) {

            Log::error('Error in fetching all team page', [
                'function' => 'getAllTeam',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);


            return (object) [];
        }
    }

    public function getTeamTestimonialsPage(int $nodeId): object
    {
        try {
            $getTeamTestinomials = FieldDataFieldTeamTarget::with('FieldDataBodyCourse', 'FieldDataFieldTestimonialPositionNode', 'FieldDataFieldTestimonialImage.FileManagedTestimonialImage')
                ->join('node', 'node.nid', 'field_data_field_team_target.entity_id')
                ->LeftJoin('field_data_field_author', 'field_data_field_author.entity_id', 'node.nid')
                ->where('field_data_field_team_target.field_team_target_target_id', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->get();

            return (object) $getTeamTestinomials;
        } catch (\Exception $e) {
            Log::error('Error in fetching team testimonials page', [
                'function' => 'getTeamTestimonialsPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }


    public function getTeamVideoTestimonialsPage(int $nodeId)
    {
        try {

            $getTeamVideoTestimonials = FieldDataFieldVideoTestimonial::leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_video_testimonial.field_video_testimonial_fid')
                ->where('field_data_field_video_testimonial.entity_id', $nodeId)
                ->get();
            return (object) $getTeamVideoTestimonials;
        } catch (\Exception $e) {
            Log::error('Error in fetching team video testimonials page', [
                'function' => 'getTeamVideoTestimonialsPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function getTeamLinksPage($nodeId)
    {
        $getTeamLinksPage = FieldDataFieldLinks::where('field_data_field_links.entity_id', $nodeId)
            ->get();
        return (object) $getTeamLinksPage;
    }
    public function getSpecialtiesAreas($nodeId)
    {
        try {

            $getSpecialties = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::MACHINE_NAME_AREAS)
                ->leftJoin('taxonomy_term_data', 'taxonomy_term_data.vid', '=', 'taxonomy_vocabulary.vid')
                ->leftJoin('field_data_field_areas', 'field_data_field_areas.field_areas_tid', '=', 'taxonomy_term_data.tid')
                ->leftJoin('node', 'node.nid', '=', 'field_data_field_areas.entity_id')
                ->where('node.nid', '=', $nodeId)
                ->where('node.type', '=', AppConstants::TEAM)
                ->where('node.status', '=', AppConstants::NODE_STATUS)
                ->select('taxonomy_vocabulary.name as vocabulary_name', 'taxonomy_vocabulary.vid', 'field_data_field_areas.field_areas_tid', 'taxonomy_term_data.name')
                ->get();
            return $getSpecialties;
        } catch (\Exception $e) {
            Log::error('Error in fetching specialtiesarea', [
                'function' => 'getSpecialtiesAreas',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function getSpecialtiesIndustry($nodeId)
    {
        try {
            $getSpecialtiesindustry = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::MACHINE_NAME_INDUSTRY)
                ->leftJoin('taxonomy_term_data', 'taxonomy_term_data.vid', '=', 'taxonomy_vocabulary.vid')
                ->leftJoin('field_data_field_industry', 'field_data_field_industry.field_industry_tid', '=', 'taxonomy_term_data.tid')
                ->leftJoin('node', 'node.nid', '=', 'field_data_field_industry.entity_id')
                ->where('node.nid', '=', $nodeId)
                ->where('node.type', '=', AppConstants::TEAM)
                ->where('node.status', '=', AppConstants::NODE_STATUS)
                ->select('taxonomy_vocabulary.name as vocabulary_name', 'taxonomy_vocabulary.vid', 'field_data_field_industry.field_industry_tid', 'taxonomy_term_data.name')
                ->get();

            return $getSpecialtiesindustry;
        } catch (\Exception $e) {
            Log::error('Error in fetching specialtiesindustry', [
                'function' => 'getSpecialtiesIndustry',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function getconsultantstestimonials($request, $nodeId): object
    {
        $perPage = 20;

        try {

            $getallTestinomials = FieldDataFieldTeamTarget::with('FieldDataBodyCourse', 'FieldDataFieldTestimonialPositionNode', 'FieldDataFieldTestimonialImage.FileManagedTestimonialImage','FieldDataFieldAuthor')
                ->join('node', 'node.nid', 'field_data_field_team_target.entity_id')
                ->where('field_data_field_team_target.field_team_target_target_id', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->orderBy('node.nid', 'desc')
                ->paginate($perPage);

            $metaDetails = $this->getMetaTagDetails($nodeId);
            return (object) [
                'metaDetails' => $metaDetails,
                'getallTestinomials' => $getallTestinomials,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching team testimonials page', [
                'function' => 'getTeamTestimonialsPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return collect();
        }
    }



    public function getCourseTestimonials($request, $nodeId): object
    {
        try {
            //Courses testinomials
            $getTestinomials = FieldDataFieldCourseTarget::with('FieldDataBodyCourse', 'FieldDataFieldTestimonialPositionNode', 'FieldDataFieldTestimonialImage.FileManagedTestimonialImage','FieldDataFieldAuthor')
                ->join('node', 'node.nid', 'field_data_field_course_target.entity_id')
                ->where('field_data_field_course_target.field_course_target_target_id', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->orderBy('node.nid', 'desc')
                ->paginate(20);
            $metaDetails = $this->getMetaTagDetails($nodeId);

            return (object) [
                'metaDetails' => $metaDetails,
                'getTestinomials' => $getTestinomials,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching course testimonials page', [
                'function' => 'getCourseTestimonials',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function getTeamVideoTestimonialsPageData($nodeId): object
    {
        try {

            $getTeamVideoTestimonials = FieldDataFieldVideoTestimonial::leftJoin('file_managed', 'file_managed.fid', '=', 'field_data_field_video_testimonial.field_video_testimonial_fid')
                ->where('field_data_field_video_testimonial.entity_id', $nodeId)
                ->select('file_managed.uri')
                ->get();
            return $getTeamVideoTestimonials;
        } catch (\Exception $e) {
            Log::error('Error in fetching video testimonials page', [
                'function' => 'getTeamVideoTestimonialsPageData',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }




    public function getTeamPageContent($nodeId): object
    {
        try {
            $getTeamPageContent = Node::leftJoin('field_data_field_team_summary', 'field_data_field_team_summary.entity_id', '=', 'node.nid')
                ->where('node.status', AppConstants::NODE_STATUS)
                ->where('node.nid', $nodeId)
                ->select('field_data_field_team_summary.field_team_summary_value')
                ->first();


            $iframeSrcs = [];
            $linksAndImages = [];

            if ($getTeamPageContent && isset($getTeamPageContent->field_team_summary_value)) {
                $getTeamPageContent->field_team_summary_value = $this->updateImageUrls($getTeamPageContent->field_team_summary_value);

                $dom = new DOMDocument();
                libxml_use_internal_errors(true);
                $dom->loadHTML($getTeamPageContent->field_team_summary_value);
                libxml_clear_errors();

                $iframeSrcs = $this->processIframes($dom);

                $getTeamPageContent->field_team_summary_value = $dom->saveHTML();


                $patterns = [
                    '/<iframe\b([^>]*)>/i' => '<span class="loader loader-container" id="iframe-loader"></span><iframe $1>',
                    '/<iframe\s+([^>]*\s)src=["\'][^"\']*["\']([^>]*)>/i' => '<iframe $1src="" $2>',
                ];

                foreach ($patterns as $pattern => $replacement) {
                    $getTeamPageContent->field_team_summary_value = preg_replace($pattern, $replacement, $getTeamPageContent->field_team_summary_value);
                }
            }

            return (object) [
                'getTeamPageContent' => $getTeamPageContent,
                'iframeSrcs' => $iframeSrcs,
                'linksAndImages' => $linksAndImages
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching page content page', [
                'function' => 'getTeamPageContent',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }


    private function processIframes(DOMDocument $dom): array
    {
        $iframeSrcs = [];
        $iframes = $dom->getElementsByTagName('iframe');

        foreach ($iframes as $iframe) {
            $src = $iframe->getAttribute('src');

            if ($src) {
                $iframeSrcs[] = $src;
            } else {
                $iframe->setAttribute('src', $iframeSrcs[0] ?? '');
            }
        }
        return $iframeSrcs;
    }
    public function getPMIPage(): object
    {
        return $this->getTabPage(AppConstants::PMI_NID);
    }
    public function getPayingUsPage(): object
    {
        return $this->getTabPage(AppConstants::PAGE_PAYING_US);
    }

    public function getTabPage($nodeID): object
    {

        try {
            $contentData = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')
                ->where('node.nid', '=', $nodeID)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->first();

            $content = $contentData->body_value;
            $content = preg_replace_callback(
                '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,7}\b/',
                function ($matches) {
                    $email = $matches[0];
                    return '<span class="content-mail-body">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</span>';
                },
                $content
            );
            $imageBaseUrl = env('PROCEPT_COM');

            $content = preg_replace('/src="\/images\//', 'src="' . rtrim($imageBaseUrl, '/') . '/images/', $content);


            $metaDetails = $this->getMetaTagDetails($nodeID);


            $content = preg_replace('/\[tabs class="vertical"\]/', AppConstants::ABOUTS_PAGE_NAV_TABS, $content);
            $content = preg_replace('/\[\/tabs\]/', AppConstants::ABOUTS_PAGE_NAV_UL_TABS, $content);

            $tabButtons = '';
            $tabContents = '';
            $isFirstTab = true;


            $tabs = preg_split('/(\[tab title=".*?" icon=".*?"\]|\[\/tab\])/', $content, -1, PREG_SPLIT_DELIM_CAPTURE |
                PREG_SPLIT_NO_EMPTY);
            $beforeFirstTab = '';
            $afterLastTab = '';
            $insideTab = false;
            $processedTabs = [];
            foreach ($tabs as $tab) {

                if (preg_match('/^\[tab title="(.*?)" icon="(.*?)"\]$/', $tab, $matches)) {
                    $tabTitle = htmlspecialchars($matches[1]);
                    $tabId = str_replace(' ', '-', $tabTitle);

                    if (!in_array($tabTitle, $processedTabs)) {


                        $tabButtons .= '<button class="' . AppConstants::TAB_BUTTON_CLASS . ($isFirstTab ? ' ' . AppConstants::TAB_ACTIVE_CLASS : '') . '" id="' . AppConstants::TAB_BUTTON_ID_PREFIX . $tabId . '-tab"
' . AppConstants::DATA_TOGGLE_ATTRIBUTE . ' ' . AppConstants::DATA_TARGET_ATTRIBUTE . '="#' . AppConstants::TAB_BUTTON_ID_PREFIX . $tabId . '" type="button" ' . AppConstants::ROLE_ATTRIBUTE . '
' . AppConstants::ARIA_CONTROLS_ATTRIBUTE . '="' . AppConstants::TAB_BUTTON_ID_PREFIX . $tabId . '" ' . AppConstants::ARIA_SELECTED_ATTRIBUTE . '>' . $tabTitle . '</button>';


                        $tabContents .= '<div class="' . AppConstants::TAB_PANE_CLASS . ($isFirstTab ? ' ' . AppConstants::TAB_SHOW_CLASS . ' ' . AppConstants::TAB_ACTIVE_CLASS_PANE : '') . '" id="' . AppConstants::TAB_BUTTON_ID_PREFIX . $tabId . '"
' . AppConstants::ROLE_PANEL_ATTRIBUTE . ' ' . AppConstants::ARIA_LABELLED_BY_ATTRIBUTE . '="' . AppConstants::TAB_BUTTON_ID_PREFIX . $tabId . '-tab">';

                        $isFirstTab = false;

                        $processedTabs[] = $tabTitle;
                        $insideTab = true;
                    }
                } elseif ($tab === '[/tab]') {

                    $tabContents .= '</div>';
                    $insideTab = false;
                } else {

                    if ($insideTab) {

                        $tabContents .= trim($tab);
                    } else {

                        if ($isFirstTab) {
                            $beforeFirstTab .= trim($tab);
                        } else {
                            $afterLastTab .= trim($tab);
                        }
                    }
                }
            }
            if (!empty($tabButtons)) {
                $tabContents .= '</div>';
            }
            return (object) [
                'payingUscontentData' => $contentData,
                'beforeFirstTab' => $beforeFirstTab,
                'tabButtons' => $tabButtons,
                'tabContents' => $tabContents,
                'afterLastTab' => $afterLastTab,
                'metaDetails' => $metaDetails,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching paying us page content page', [
                'function' => 'getPayingUsPage',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }


public function parseTabs($content)
{
    $content = preg_replace_callback('/<p>(.*?)<\/p>/i', function ($matches) {
        if (preg_match_all('/\[(\/?\w+)([^\]]*)\]/', $matches[1], $shortcodes)) {
            return $matches[1];
        }
        return $matches[0];
    }, $content);

    // Clean up unwanted <p> tags around shortcodes
    $content = preg_replace([
        '/<p>\s*(\[tabs[^\]]*\])<\/p>/i',
        '/<p>\s*(\[tab[^\]]*\])<\/p>/i',
        '/<p>\s*(\[\/tabs\])\s*<\/p>/i',
        '/<p>\s*(\[\/tab\])\s*<\/p>/i'
    ], '$1', $content);

    // Replace [tabs class="vertical"] with our wrapper
    $content = str_replace('[tabs class="vertical"]', '<div class="tab-container">', $content);
    $content = str_replace('[/tabs]', '</div>', $content);

    $tabDefinitions = [];
    $tabContents = '';
    $tabButtons = '';
    $beforeFirstTab = '';
    $afterLastTab = '';
    $insideTab = false;
    $isFirstTab = true;

    // Split content into parts while keeping tab shortcode delimiters
    $tabs = preg_split(
        '/(\[tab title=".*?" icon=".*?"\]|\[\/tab\])/',
        $content,
        -1,
        PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY
    );

    // First, collect all opening [tab ...] shortcodes
    foreach ($tabs as $tab) {
        if (preg_match('/^\[tab title="(.*?)" icon="(.*?)"\]$/', $tab, $matches)) {
            $tabTitle = htmlspecialchars($matches[1]);
            $tabId = preg_replace('/[^a-z0-9\-]/i', '-', strtolower($tabTitle));
            $tabIcon = $matches[2];

            $tabDefinitions[] = [
                'title' => $tabTitle,
                'id' => $tabId,
                'icon' => $tabIcon
            ];
        }
    }

    // Generate tab buttons and content
    $currentTabIndex = 0;
    $processedTabs = [];

    foreach ($tabs as $tab) {
        $tab = trim($tab);
        if (empty($tab)) continue;

        if (preg_match('/^\[tab title="(.*?)" icon="(.*?)"\]$/', $tab, $matches)) {
            $tabTitle = htmlspecialchars($matches[1]);
            $tabId = preg_replace('/[^a-z0-9\-]/i', '-', strtolower($tabTitle));
            $tabIcon = $matches[2];
            $isLastTab = ($currentTabIndex === count($tabDefinitions) - 1);

            $extraClass = $isFirstTab ? ' active' : '';
            $extraClass .= $isLastTab ? ' last-tab' : '';

            $tabButtons .= sprintf(
                '<li class="nav-item%s" role="presentation">
                    <button class="nav-link%s" id="%s-tab" data-bs-toggle="pill" data-bs-target="#%s" type="button" role="tab" aria-controls="%s" aria-selected="%s">
                        <i class="%s"></i> %s
                    </button>
                </li>',
                $isLastTab ? ' last-li' : '',
                $extraClass,
                $tabId,
                $tabId,
                $tabId,
                $isFirstTab ? 'true' : 'false',
                $tabIcon,
                $tabTitle
            );

            $tabContents .= sprintf(
                '<div class="tab-pane fade%s" id="%s" role="tabpanel" aria-labelledby="%s-tab">',
                $isFirstTab ? ' show active' : '',
                $tabId,
                $tabId
            );

            $processedTabs[] = $tabId;
            $isFirstTab = false;
            $insideTab = true;
            $currentTabIndex++;
        } elseif ($tab === '[/tab]') {
            if ($insideTab) {
                $tabContents .= '</div>';
                $insideTab = false;
            }
        } else {
            if ($insideTab) {
                $tabContents .= $this->cleanTabContent($tab);
            } else {
                if ($isFirstTab) {
                    $beforeFirstTab .= $this->cleanTabContent($tab);
                } else {
                    $afterLastTab .= $this->cleanTabContent($tab);
                }
            }
        }
    }

    $tabStructure = sprintf(
        '%s
        <div class="container tab-container">
            <div class="row paying-us-content">
                <div class="col-3 about-us-tab p-md-0 p-lg-0 mt-4">
                    <ul class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        %s
                    </ul>
                </div>
                <div class="col-9 paying-us-tab-content-list mt-4">
                    <div class="tab-content" id="myTabContent">
                        %s
                    </div>
                </div>
            </div>
        </div>
        %s',
        $beforeFirstTab,
        $tabButtons,
        $tabContents,
        $afterLastTab
    );

    return $tabStructure;
}


protected function cleanTabContent($content)
{
    $content = preg_replace([
        // Remove <p> tags around shortcode tags
        '/<p>\s*(\[tabs[^\]]*\])\s*<\/p>/i',
        '/<p>\s*(\[\/tabs\])\s*<\/p>/i',
        '/<p>\s*(\[tab[^\]]*\])\s*<\/p>/i',
        '/<p>\s*(\[\/tab\])\s*<\/p>/i',

        // Remove empty <p> tags
        '/<p[^>]*>\s*<\/p>/i',

        // Remove orphan </p> before block tags
        '/<\/p>\s*(<(h[1-6]|div|section|article|ul|ol|li|table|thead|tbody|tfoot|tr|td|th|img|figure|blockquote)[^>]*>)/i',

        // Remove <p> before </div>
        '/<p>\s*(<\/div>)/i',

        // Remove <p> after closing blocks or shortcodes
        '/(<\/(div|section|article|tab|tabs)>|\])\s*<p>/i',

        // Fix bad sequences like </div></p><p>
        '/(<\/div>)\s*<\/p>\s*<p>/i',

        // Remove double <p> or </p>
        '/<p>\s*<p>/i',
        '/<\/p>\s*<\/p>/i',

        '/(<\/div>)(?:&nbsp;|\xC2\xA0|\s)*<\/p>/i',

        '/\s*aria-selected="[^"]*"\s*aria-selected="[^"]*"/i',

    ], [
        '$1',  // [tabs]
        '$1',  // [/tabs]
        '$1',  // [tab]
        '$1',  // [/tab]

        '',    // Empty <p>
        '$1',  // Orphan </p> before block
        '$1',  // <p> before </div>
        '$1<p>', // <p> after closing blocks

        '$1',  // Fix </div></p><p>
        '<p>', // Double <p>
        '</p>', // Double </p>

        '$1',

        '',    // Duplicate aria-selected
    ], $content);

    return trim($content);
}






    public function getRequestingQuote()
    {
        try {
            $metaDetails = $this->getMetaTagDetails(AppConstants::REQUESTING_QUOTE_NID);
            $content = $this->getContentByAlias(AppConstants::REQUESTING_QUOTE_NID);


            $content->body_value = preg_replace_callback(
                '/\b[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Z|a-z]{2,7}\b/',
                function ($matches) {
                    $email = $matches[0];
                    return '<span class="content-mail-body">' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '</span>';
                },
                $content->body_value
            );

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $content,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching Requesting Quote page content', [
                'function' => 'getRequestingQuote',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);
            return (object) [];
        }
    }
    public function getWorkingForUs()
    {
        return $this->showPageContent(AppConstants::WORKING_FOR_US_NID);
    }

    public function getUrlDetails($request, $nodeId): object
    {
        try {

            $geturlData = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')
                ->where('node.nid', '=', $nodeId)
                ->select('node.title', 'field_data_body.body_value')
                ->first();
            $geturlData->body_value = preg_replace('/\[[^\]]*\]/', '', $geturlData->body_value);
            $geturlData->body_value = $this->getUrlContent($geturlData->body_value);

            $metaDetails = $this->getMetaTagDetails($nodeId);
            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $geturlData,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching generic page', [
                'function' => 'getUrlDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function getProceptChangeUrl($body_value)
    {
        try {
            $body_value = preg_replace_callback(
                '/<a\s+[^>]*href=["\'](http[s]?:\/\/' . preg_quote(parse_url(env('TRAINING_URL'), PHP_URL_HOST), '/') . '[^"\']*)["\'][^>]*>(.*?)<\/a>/i',
                function ($matches) {
                    $newHref = str_replace([env('TRAINING_DATA_URL'), env('TRAINING_URL')], '', $matches[1]);
                    return '<a href="' . $newHref . '">' . $matches[2] . '</a>';
                },
                $body_value
            );

            $body_value = preg_replace_callback(
                '/<a\s+[^>]*href=["\'](https?:\/\/' . preg_quote(parse_url(env('TRAINING_URL'), PHP_URL_HOST), '/') . '[^"\']*)["\'][^>]*>(.*?)<\/a>/i',
                function ($matches) {
                    $fullUrl = $matches[1];
                    $newHref = str_replace([env('PROCEPT_NOT_COM'), env('TRAINING_URL')], '', $fullUrl);

                    return '<a href="' . $newHref . '">' . $matches[2] . '</a>';
                },
                $body_value
            );

            return $body_value;
        } catch (\Exception $e) {
            Log::error('Error in fetching generic page', [
                'function' => 'getProceptChangeUrl',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }



    public function getAllBlogContent($request,$nodeId): object
    {
        try {

            $metaDetails = $this->getMetaTagDetails($nodeId);

            $getAllBlogContent = Node::leftJoin('field_data_body', 'field_data_body.entity_id', '=', 'node.nid')
                ->where('node.nid', $nodeId)
                ->where('node.status', AppConstants::NODE_STATUS)
                ->first();


            $getAllBlogContent->body_value = str_replace(AppConstants::BASIC_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getAllBlogContent->body_value);
            $getAllBlogContent->body_value = str_replace(AppConstants::BLOG_PAGE_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getAllBlogContent->body_value);

            $getAllBlogContent->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON, '<button type="button" class="dexp-btn-reflect">', $getAllBlogContent->body_value);
            $getAllBlogContent->body_value = $this->getUrlContent($getAllBlogContent->body_value);

            $getAllBlogContent->body_value = str_replace(AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE, '</button>', $getAllBlogContent->body_value);
            $getAllBlogContent->body_value = $this->getProceptChangeUrl($getAllBlogContent->body_value);
            $getAllBlogContent->body_value = $this->getAnimateRowColumn($getAllBlogContent->body_value);

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $getAllBlogContent,
            ];
        } catch (\Exception $e) {
            Log::error('Error in fetching generic page', [
                'function' => 'getProceptChangeUrl',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }

    public function showPageContent($pageAlias)
    {
        try {
            $metaDetails = $this->getMetaTagDetails($pageAlias);
            $content = $this->getContentByAlias($pageAlias);

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $content,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching page content', [
                'function' => __FUNCTION__,
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'pageAlias' => $pageAlias
            ]);
            return (object) [];
        }
    }

    public function showWhoWeAre()
    {
        return $this->showPageContent(AppConstants::WHO_WE_ARE_NID);
    }

    public function getSponsorships()
    {
        return $this->showPageContent(AppConstants::SPONSORSHIPS_NID);
    }
    public function getMetaGlobalDetails()
    {
        return [
            AppConstants::META_NODE_TITLE,
            AppConstants::META_CURRENT_PAGE_PAGER,
            AppConstants::META_SITE_NAME,
            AppConstants::META_NODE_SUMMARY,
            AppConstants::META_CURRENT_PAGE_TITLE,
            AppConstants::META_CURRENT_PAGE_URL_ABSOLUTE,
            AppConstants::META_CONTENT_MODIFICATION_DATE_TIME,
            AppConstants::META_CONTENT_CREATED_TIME,
            AppConstants::META_SITE_URL,
            AppConstants::META_SITE_SLOGAN,

        ];
    }

    private function replaceMetaDetails(array $metaDetails, array $placeholders, array $values): array
    {
        foreach ($metaDetails as $key => $value) {
            $metaDetails[$key] = str_replace($placeholders, $values, $value);
        }
        return $metaDetails;
    }


    public function getMetaTagGlobal($metaTags, $metaTagsGlobal, $nodeTitle, $nodeSummary, $nodeAbstract, $formattedDate, $formattedChangedDate, $ogDescription, $nodeId)
    {
        return [
            'title' => !empty($metaTags['title']['value']) ?
                $metaTags['title']['value'] : (!empty($metaTagsGlobal['title']['value']) ?
                    $metaTagsGlobal['title']['value'] :
                    $nodeTitle . ' | ' . AppConstants::META_SITE_NAME),

            'description' => !empty($metaTags['description']['value']) ?
                strip_tags($metaTags['description']['value']) :
                strip_tags($nodeSummary),


            'abstract' => !empty($metaTags['abstract']['value']) ?
                $metaTags['abstract']['value'] :
                $nodeAbstract,

            'keywords' => !empty($metaTags['keywords']['value']) ?
                $metaTags['keywords']['value'] : (!empty($metaTagsGlobal['keywords']['value']) ?
                    $metaTagsGlobal['keywords']['value'] :
                    ''),

            'og:title' => !empty($metaTags['og:title']['value']) ?
                $metaTags['og:title']['value'] : (!empty($metaTagsGlobal['og:title']['value']) ?
                    $metaTagsGlobal['og:title']['value'] :
                    $nodeTitle),

            'og_type' => !empty($metaTags['og:type']['value']) ?
                $metaTags['og:type']['value'] : (!empty($metaTagsGlobal['og:type']['value']) ?
                    $metaTagsGlobal['og:type']['value'] :
                    ''),

            'og_url' => !empty($metaTags['og:url']['value']) ?
                $metaTags['og:url']['value'] : (!empty($metaTagsGlobal['og:url']['value']) ?
                    $metaTagsGlobal['og:url']['value'] :
                    url()->current()),

            'og_site_name' => !empty($metaTags['og:site_name']['value']) ?
                $metaTags['og:site_name']['value'] : (!empty($metaTagsGlobal['og:site_name']['value']) ?
                    $metaTagsGlobal['og:site_name']['value'] :
                    AppConstants::META_SITE_NAME),
            'og_determiner' => !empty($metaTags['og:determiner']['value']) ?
                $metaTags['og:determiner']['value'] : (!empty($metaTagsGlobal['og:determiner']['value']) ?
                    $metaTagsGlobal['og:determiner']['value'] :
                    ''),


            'og_description' => !empty($metaTags['og:description']['value']) ?
                $metaTags['og:description']['value'] :
                $ogDescription,

            'og_image' => !empty($metaTags['og:image']['value']) ?
                $metaTags['og:image']['value'] : (!empty($metaTagsGlobal['og:image']['value']) ?
                    $metaTagsGlobal['og:image']['value'] :
                    ''),
            'og_updated_time' => !empty($metaTags['og:updated_time']['value']) ?
                $metaTags['og:updated_time']['value'] :
                $formattedChangedDate,

            'article_published_time' => !empty($metaTags['article:published_time']['value']) ?
                $metaTags['article:published_time']['value'] :
                $formattedDate,

            'shortlink' => !empty($nodeId) ? url("/node/{$nodeId}") : url()->current(),

            'twitter_card' => !empty($metaTags['twitter:card']['value']) ?
                $metaTags['twitter:card']['value'] : '',

            'twitter_url' => !empty($metaTags['twitter:url']['value']) ?
                $metaTags['twitter:url']['value'] : '',

            'twitter_title' => !empty($metaTags['twitter:title']['value']) ?
                $metaTags['twitter:title']['value'] : '',

            'twitter_description' => !empty($metaTags['twitter:description']['value']) ?
                $metaTags['twitter:description']['value'] : '',

        ];
    }

    public function getMetaTagDetails(int $nodeId): array
    {
        $getMetaTagDetails = Metatag::where('entity_id', $nodeId)
            ->where('entity_type', AppConstants::NODE)
            ->first();

        $metaTags = $getMetaTagDetails ? unserialize($getMetaTagDetails->data) : [];

        if (!$getMetaTagDetails) {
            Log::warning("No meta tag details found for node ID: {$nodeId}");
        }

        $currentUrl = url()->current();

        $node = Node::where('nid', $nodeId)->select('title', 'created', 'changed')->first();
        $nodeTitle = $node->title ?? '';
        $createdDate = $node->created ?? '';
        $changedDate = $node->changed ?? '';

        $formattedDate = Carbon::parse($createdDate)->format('Y-m-d\TH:i:sP');
        $formattedChangedDate = Carbon::parse($changedDate)->format('Y-m-d\TH:i:sP');

        $nodeSummaryData = FieldDataBody::where('entity_id', $nodeId)->value('body_value');
        $nodeSummary = $nodeSummaryData ? substr($nodeSummaryData, 0, 380) : '';
        $nodeAbstract = '';

        $site_slogan = Variable::where('name', AppConstants::SITE_SLOGAN)->first();
        $siteSlogan = unserialize($site_slogan->value ?? '') ?: [];
        $ogDescription = '';

        $getMetaGlobalDetails = $this->getMetaGlobalDetails();
        $getMetaGlobalData = [
            $nodeTitle,
            '',
            AppConstants::SITE_NAME,
            $nodeSummary,
            $nodeTitle,
            $currentUrl,
            $formattedDate,
            $formattedChangedDate,
            env('APP_URL'),
            $siteSlogan
        ];


        $getMetaTagGlobalDetails = MetatagConfig::where('instance', AppConstants::META_GLOBAL)->first();
        $metaTagsGlobal = unserialize($getMetaTagGlobalDetails->config ?? '') ?: [];


        $metaDetails = $this->getMetaTagGlobal($metaTags, $metaTagsGlobal, $nodeTitle, $nodeSummary, $nodeAbstract, $formattedDate, $formattedChangedDate, $ogDescription, $nodeId);

        if (array_filter($metaDetails)) {
            return $this->replaceMetaDetails($metaDetails, $getMetaGlobalDetails, $getMetaGlobalData);
        }

        return [];
    }


    public function indexMetaTagDetails(): array
    {
        try {

            $metaTags = [];

            $metaTagGlobalConfig = MetatagConfig::where('instance', AppConstants::META_GLOBAL_FRONTPAGE)->first();
            $metaTagsGlobal = unserialize($metaTagGlobalConfig->config ?? '') ?: [];
            $nodeTitle = $metaTagsGlobal['title']['value'] ?? '';
            $nodeSummary = $metaTagsGlobal['description']['value'] ?? '';
            $nodeAbstract = $metaTagsGlobal['abstract']['value'] ?? '';
            $formattedDate = '';
            $formattedChangedDate = '';
            $nodeId = '';
            $site_slogan = Variable::where('name', AppConstants::SITE_SLOGAN)->first();
            $siteSlogan = unserialize($site_slogan->value ?? '') ?: [];

            $currentUrl = url()->current();
            $ogDescription = $siteSlogan;

            $getMetaGlobalDetails = $this->getMetaGlobalDetails();
            $getMetaGlobalData = [
                $nodeTitle,
                '',
                AppConstants::SITE_NAME,
                $nodeSummary,
                $nodeTitle,
                $currentUrl,
                $formattedDate,
                $formattedChangedDate,
                env('APP_URL'),
                $siteSlogan
            ];

            $metaDetailsData = $this->getMetaTagGlobal($metaTags, $metaTagsGlobal, $nodeTitle, $nodeSummary, $nodeAbstract, $formattedDate, $formattedChangedDate, $ogDescription, $nodeId);

            $metaDetails = $this->replaceMetaDetails($metaDetailsData, $getMetaGlobalDetails, $getMetaGlobalData);

            return (array) $metaDetails;
        } catch (Exception $e) {

            Log::error('Error in fetching index metatag details', [
                'function' => 'indexMetaTagDetails',
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function getMetaTagGlobalDetails(string $nodeTitle): array
    {
        try {
            $metaTags = [];

            $metaTagGlobalConfig = MetatagConfig::where('instance', AppConstants::META_GLOBAL)->first();
            $metaTagsGlobal = unserialize($metaTagGlobalConfig->config ?? '') ?: [];

            $nodeSummary = $metaTagsGlobal['description']['value'] ?? '';
            $nodeAbstract = '';
            $formattedDate = '';
            $formattedChangedDate = '';
            $nodeId = '';
            $currentUrl = url()->current();
            $siteSlogan = Variable::where('name', AppConstants::SITE_SLOGAN)->first();
            $siteSloganValue = unserialize($siteSlogan->value ?? '') ?: [];
            $ogDescription = '';

            $getMetaGlobalDetails = $this->getMetaGlobalDetails();
            $getMetaGlobalData = [
                $nodeTitle,
                '',
                AppConstants::SITE_NAME,
                $nodeSummary,
                $nodeTitle,
                $currentUrl,
                $formattedDate,
                $formattedChangedDate,
                env('APP_URL'),
                $siteSloganValue
            ];

            $metaDetailsData = $this->getMetaTagGlobal($metaTags, $metaTagsGlobal, $nodeTitle, $nodeSummary, $nodeAbstract, $formattedDate, $formattedChangedDate, $ogDescription, $nodeId);
            $metaDetails = $this->replaceMetaDetails($metaDetailsData, $getMetaGlobalDetails, $getMetaGlobalData);

            return (array)$metaDetails;
        } catch (Exception $e) {
            Log::error('Error in fetching meta tag details', [
                'function' => debug_backtrace()[1]['function'],
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }
    public function contactMetaTagDetails(): array
    {
        return $this->getMetaTagGlobalDetails(AppConstants::MENU_CONTACT);
    }

    public function getTrainingPage(): array
    {
        return $this->getMetaTagGlobalDetails(AppConstants::MENU_TRAINING);
    }


    public function getSiteMapDetails(): object
    {
        try {
            $getSiteMap = Xmlsitemap::leftJoin('node', 'xmlsitemap.loc', '=', DB::raw("CONCAT('node/', node.nid)"))
                ->leftJoin('url_alias', 'url_alias.source', '=', DB::raw("CONCAT('node/', node.nid)"))
                ->where('xmlsitemap.status', AppConstants::NODE_STATUS)
                ->where('xmlsitemap.access', AppConstants::NODE_STATUS)
                ->orderBy('url_alias.alias', 'asc')
                ->get();

            return (object) $getSiteMap;
        } catch (Exception $e) {

            Log::error('Error in fetching upcoming public courses on the List', [
                'function' => 'showUpcomingPublicCourse',
                'line' => $e->getLine(),
                'message' => $e->getMessage()
            ]);

            return (object) [];
        }
    }
    public function getAwardsPage()
    {
        return $this->showPageContent(AppConstants::AWARDS_NID);
    }
    public function getUrlContent($aboutus)
    {
        $baseUrls = [
            env('PROCEPT_NOT_COM'),
            env('PROCEPT_COM'),
            env('TRAINING_URL'),
            env('TRAINING_DATA_URL'),
            env('WWW_PROCEPT_COM'),
            env('WWW_SEC_PROCEPT_COM'),
            env('STAGE_PROCEPT_COM'),
            env('STAGE_SEC_PROCEPT_COM'),
            env('STAGE_TRAINING_URL'),
            env('STAGE_SEC_TRAINING_URL'),
        ];

        $aboutus = preg_replace_callback('/<a[^>]+href=["\'](.*?)["\'][^>]*>/', function ($matches) use ($baseUrls) {
            $url = $matches[1];
            foreach ($baseUrls as $baseUrl) {
                if (strpos($url, $baseUrl) !== false) {
                    $url = preg_replace('/' . preg_quote($baseUrl, '/') . '/', env('APP_URL') . '/', $url);
                }
            }
            return str_replace($matches[1], $url, $matches[0]);
        }, $aboutus);

        return $aboutus;
    }

    public function getAllSlug($requestUrl): object
    {

        $nodeDetails = UrlAlias::where('alias', $requestUrl)->first();

        if ($nodeDetails) {
            $getNodedata = str_replace('node/', '', $nodeDetails->source);
            $getNodeDetails = Node::where('nid', $getNodedata)->select('nid', 'type')->first();
            if ($getNodeDetails) {
                return $getNodeDetails;
            }
        }
    }
    public function getOtherCoursesPage($request)
    {
        $course_alias = AppConstants::OTHER_COURSES_NID;
        $course_type_alias = AppConstants::COURSE_TYPE_OTHER_COURSES;
        return $this->getOtherCoursesDetails($request, $course_alias, $course_type_alias);
    }
    public function getOtherCoursesDetails($request, $course_alias, $course_type_alias): object
    {

         try {
        $urlAlias = UrlAlias::where('alias', AppConstants::MENU_BAR_COURSE_TYPE . $course_type_alias)->first();


        $taxonomyTermCourse = preg_quote(AppConstants::TAXONOMY_TERM_COURSE, '/');
        $pattern = '/' . $taxonomyTermCourse . '(\d+)/';
        $matches = [];
        if (preg_match($pattern, $urlAlias->source, $matches)) {
            $nodeId = $matches[1];
        } else {
            $nodeId = null;
        }

        $desiredTids = [AppConstants::BEGINNER, AppConstants::INTERMEDIATE, AppConstants::ADVANCED];
        $nodeId = $matches[1];
 $courseLevel = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::TAXONOMY_VOCABULARY_NAME)
    ->leftJoin('taxonomy_term_data', 'taxonomy_vocabulary.vid', '=', 'taxonomy_term_data.vid')
    ->orderByRaw('FIELD(taxonomy_term_data.tid, ' . implode(',', $desiredTids) . ')')
    ->get();



        $courseBackgroundImage = [
            AppConstants::BEGINNER => AppConstants::BEGINNER_IMAGE,
            AppConstants::INTERMEDIATE => AppConstants::INTERMEDIATE_IMAGE,
            AppConstants::ADVANCED => AppConstants::ADVANCED_IMAGE,
        ];

        $courseTitleColor = [
            AppConstants::BEGINNER => AppConstants::BEGINNER_CLASS,
            AppConstants::INTERMEDIATE => AppConstants::INTERMEDIATE_CLASS,
            AppConstants::ADVANCED => AppConstants::ADVANCED_CLASS,
        ];

        $courseLevel = $courseLevel->map(function ($level) use ($courseBackgroundImage, $courseTitleColor) {
            $level->icon = $courseBackgroundImage[$level->tid] ?? 'default_background.jpeg';
            $level->titleClass = $courseTitleColor[$level->tid] ?? 'default-course-title';
            return $level;
        });


        $courseLevelTids = $courseLevel->pluck('tid')->toArray();

        $getCoursesDetails = TaxonomyVocabulary::where('taxonomy_vocabulary.machine_name', AppConstants::TAXONOMY_VOCABULARY_NAME)
            ->leftJoin('taxonomy_term_data', 'taxonomy_vocabulary.vid', '=', 'taxonomy_term_data.vid')
            ->leftJoin('field_data_field_course_level', 'taxonomy_term_data.tid', '=', 'field_data_field_course_level.field_course_level_tid')
            ->leftJoin('node', 'field_data_field_course_level.entity_id', '=', 'node.nid')
            ->leftJoin('field_data_field_course_id', 'node.nid', '=', 'field_data_field_course_id.entity_id')
            ->leftJoin('field_data_field_course_type', 'node.nid', '=', 'field_data_field_course_type.entity_id')
            ->leftJoin('field_data_field_duration', 'field_data_field_course_type.entity_id', '=', 'field_data_field_duration.entity_id')
            ->leftJoin('field_data_field_active_course', 'field_data_field_active_course.entity_id', '=', 'node.nid')
            ->where('field_data_field_course_type.field_course_type_tid', '=', $nodeId)
            ->where('node.status', AppConstants::NODE_STATUS)
            ->where('field_data_field_active_course.field_active_course_value', AppConstants::FIELD_PUBLIC_VALUE)
            ->whereIn('field_data_field_course_level.field_course_level_tid', $courseLevelTids)
            ->orderBy('node.title', 'asc')
            ->get()
            ->map(function ($getPMCourses) {
                $urlAlias = UrlAlias::where('source', '=', 'node/' . $getPMCourses->entity_id)
                    ->select('alias')
                    ->first();
                $getPMCourses->course_url = !empty($urlAlias) ? $urlAlias->alias : '';
                return $getPMCourses;
            });
        $metaDetails = $this->getMetaTagDetails($course_alias);

        $getCoursesTypeImage = $this->getCourseTypeImageDetails($course_type_alias);

        $trainingDetails = $this->getContentByAlias($course_alias);

        if (isset($trainingDetails->body_value)) {
            $trainingDetails->body_value = $this->getUrlContent($trainingDetails->body_value);

            $trainingDetails->body_value = $this->updateImageUrls($trainingDetails->body_value);
        }
        return (object) [
            'trainingDetails' => $trainingDetails,
            'getCoursesDetails' => $getCoursesDetails,
            'getCoursesTypeImage' => $getCoursesTypeImage,
            'metaDetails' => $metaDetails,
            'courseLevel' => $courseLevel,
        ];

         } catch (\Exception $e) {

             Log::error('Error in fetching Courses Page Details', [
                 'function' => 'getCoursesDetails',
                 'line' => $e->getLine(),
                 'message' => $e->getMessage()
             ]);

             return (object) [];
         }
    }

    public function getConsultingPage()
    {
        try {
            $consultingNid=AppConstants::CONSULTING_NID;
            $metaDetails = $this->getMetaTagDetails($consultingNid);
            $content = $this->getContentByAlias($consultingNid);

            return (object) [
                'metaDetails' => $metaDetails,
                'getContent' => $content,
            ];
        } catch (\Exception $e) {
            Log::error('Error fetching page content', [
                'function' => __FUNCTION__,
                'line' => $e->getLine(),
                'message' => $e->getMessage(),
                'pageAlias' => $consultingNid
            ]);
            return (object) [];
        }
    }
   public function getBaseUrl($urlAlias) {
        $baseUrls = [
            env('PROCEPT_NOT_COM'),
            env('PROCEPT_COM'),
            env('TRAINING_URL'),
            env('TRAINING_DATA_URL'),
            env('WWW_PROCEPT_COM'),
            env('WWW_SEC_PROCEPT_COM'),
            env('STAGE_PROCEPT_COM'),
            env('STAGE_SEC_PROCEPT_COM'),
            env('STAGE_TRAINING_URL'),
            env('STAGE_SEC_TRAINING_URL'),
        ];
        return str_replace($baseUrls, '', $urlAlias);
    }
    public function getAnimateRowColumn($bodyValue)
    {

        switch (true) {
            case strpos($bodyValue, '<img src="' . AppConstants::CCA_IMAGE_URL . '">') !== false:
                $bodyValue = str_replace(
                    '<img src="' . AppConstants::CCA_IMAGE_URL . '">',
                    '<img src="' . asset(AppConstants::CHANGE_MANAGEMENT_IMG) . '" ' . AppConstants::CHANGE_MANAGEMENT_IMG_CLASS . '>',
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::PRIVACY_POLICY_MAIL) !== false:
                $bodyValue = str_replace(
                    AppConstants::PRIVACY_POLICY_MAIL,
                    AppConstants::PRIVACY_POLICY_SENDMAIL,
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::BASIC_PAGE_BUTTON) !== false:
            case strpos($bodyValue, AppConstants::BLOG_PAGE_BUTTON) !== false:
                $bodyValue = str_replace(
                    strpos($bodyValue, AppConstants::BASIC_PAGE_BUTTON) !== false
                    ? AppConstants::BASIC_PAGE_BUTTON
                    : AppConstants::BLOG_PAGE_BUTTON,
                    '<button type="button" class="dexp-btn-reflect">',
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE) !== false:
                $bodyValue = str_replace(
                    AppConstants::ARTICLE_PAGE_REGISTER_BUTTON_CLOSE,
                    '</button>',
                    $bodyValue
                );

            case strpos($bodyValue, AppConstants::FADEINRIGHT_BEGINCOL) !== false:
                $bodyValue = str_replace(
                    AppConstants::FADEINRIGHT_BEGINCOL,
                    AppConstants::CLOSE_DIV,
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::FADEINRIGHT_ENDCOL) !== false:
                $bodyValue = str_replace(
                    AppConstants::FADEINRIGHT_ENDCOL,
                    AppConstants::TWOCLOSE_DIV,
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::BOX_CLOSE_DIV) !== false:
                $bodyValue = str_replace(
                    AppConstants::BOX_CLOSE_DIV,
                    AppConstants::CLOSE_DIV,
                    $bodyValue
                );
            case strpos($bodyValue, AppConstants::BOX_BORDER) !== false:
                $bodyValue = str_replace(
                    AppConstants::BOX_BORDER,
                    AppConstants::DEXP_SHORTCODES_BOX,
                    $bodyValue
                );
            default:
                break;
        }

        $fadeInRightBegin = [
            AppConstants::FADEINRIGHT_BEGIN_ONE,
            AppConstants::FADEINRIGHT_BEGIN_TWO,
            AppConstants::FADEINRIGHT_BEGIN_THREE,
            AppConstants::FADEINRIGHT_BEGIN_FOUR,
            AppConstants::FADEINRIGHT_BEGIN_FIVE,
            AppConstants::FADEINRIGHT_BEGIN_SIX,
            AppConstants::FADEINRIGHT_BEGIN_SEVEN,
            AppConstants::FADEINRIGHT_BEGIN_EIGHT,
            AppConstants::FADEINRIGHT_BEGIN_NINE,
            AppConstants::FADEINRIGHT_BEGIN_TEN,
            AppConstants::FADEINRIGHT_BEGIN_ELEVEN
        ];

        $fadeInRightEnd = [
            AppConstants::FADEINRIGHT_END_ONE,
            AppConstants::FADEINRIGHT_END_TWO,
            AppConstants::FADEINRIGHT_END_THREE,
            AppConstants::FADEINRIGHT_END_FOUR,
            AppConstants::FADEINRIGHT_END_FIVE,
            AppConstants::FADEINRIGHT_END_SIX,
            AppConstants::FADEINRIGHT_END_SEVEN,
            AppConstants::FADEINRIGHT_END_EIGHT,
            AppConstants::FADEINRIGHT_END_NINE,
            AppConstants::FADEINRIGHT_END_TEN
        ];

        $rowCols = [
            AppConstants::ROW_COLS_ONE,
            AppConstants::ROW_COLS_TWO,
            AppConstants::ROW_COLS_THREE,
            AppConstants::ROW_COLS_FOUR,
            AppConstants::ROW_COLS_FIVES,
            AppConstants::ROW_COLS_SIX,
            AppConstants::ROW_COLS_SEVEN,
            AppConstants::ROW_COLS_EIGHT,
            AppConstants::ROW_COLS_NINE,
            AppConstants::ROW_COLS_TEN,
            AppConstants::ROW_COLS_ELEVEN
        ];
        $rowEndCols = [
            AppConstants::ROWCOLS_END_ONE,
            AppConstants::ROWCOLS_END_TWO,
            AppConstants::ROWCOLS_END_THREE,
            AppConstants::ROWCOLS_END_FOUR,
            AppConstants::ROWCOLS_END_FIVE,
            AppConstants::ROWCOLS_END_SIX,
            AppConstants::ROWCOLS_END_SEVEN,
            AppConstants::ROWCOLS_END_EIGHT,
            AppConstants::ROWCOLS_END_NINE,
            AppConstants::ROWCOLS_END_TEN,
            AppConstants::ROWCOLS_END_ELEVEN
        ];
        $searchTerms = array_merge($fadeInRightBegin, $fadeInRightEnd);

        if (str_replace($searchTerms, "", $bodyValue) !== $bodyValue) {
            $bodyValue = str_replace($fadeInRightBegin, $rowCols, $bodyValue);
            $bodyValue = str_replace($fadeInRightEnd, $rowEndCols, $bodyValue);
        }
        if (!empty($bodyValue)) {
            $bodyValue = $this->getUrlContent($bodyValue);
            $bodyValue = $this->getProceptChangeUrl($bodyValue);
        }
        return $bodyValue;
    }
}
