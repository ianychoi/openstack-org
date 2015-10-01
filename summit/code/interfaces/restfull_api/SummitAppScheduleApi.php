<?php
/**
 * Copyright 2015 OpenStack Foundation
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 **/

/**
 * Class SummitAppScheduleApi
 */
final class SummitAppScheduleApi extends AbstractRestfulJsonApi {

    const ApiPrefix = 'api/v1/summitschedule';

    /**
     * @var IEntityRepository
     */
    private $summit_repository;

    /**
     * @var IEntityRepository
     */
    private $summitevent_repository;

    /**
     * @var IEntityRepository
     */
    private $summitpresentation_repository;

    /**
     * @var IEntityRepository
     */
    private $eventfeedback_repository;

    /**
     * @var IEntityRepository
     */
    private $speakerfeedback_repository;

    /**
     * @var IEntityRepository
     */
    private $attendee_repository;

    /**
     * @var IScheduleManager
     */
    private $schedule_manager;

    /**
     * @var SecurityToken
     */
    private $securityToken;

    public function __construct(){
        parent::__construct();

        $this->securityToken     = new SecurityToken();
        $this->summit_repository  = new SapphireSummitRepository;
        $this->summitevent_repository = new SapphireSummitEventRepository();
        $this->summitpresentation_repository = new SapphireSummitPresentationRepository();
        $this->eventfeedback_repository = new SapphireEventFeedbackRepository();
        $this->speakerfeedback_repository = new SapphireSpeakerFeedbackRepository();
        $this->attendee_repository = new SapphireSummitAttendeeRepository();

        $this->schedule_manager = new ScheduleManager($this->summitevent_repository, $this->summitpresentation_repository,
                                                      $this->eventfeedback_repository, new EventFeedbackFactory(), $this->speakerfeedback_repository,
                                                      $this->attendee_repository, SapphireTransactionManager::getInstance());

        $this_var           = $this;
        $security_token     = $this->securityToken;

        $this->addBeforeFilter('addFeedback','check_access_reject',function ($request) use($this_var, $security_token){
            $data = $this_var->getJsonRequest();
            if (!$data) return $this->serverError();
            if (!$security_token->checkRequest($request)) return $this->forbiddenError();
            if ($data['field_98438688'] != '') return $this->forbiddenError();
        });


    }

    public function checkOwnAjaxRequest($request){
        $referer = @$_SERVER['HTTP_REFERER'];
        if(empty($referer)) return false;
        //validate
        if (!Director::is_ajax()) return false;
        return Director::is_site_url($referer);
    }

    public function checkAdminPermissions($request){
        return true; //Permission::check("SUMMITAPP_ADMIN_ACCESS");
    }

    protected function isApiCall(){
        $request = $this->getRequest();
        if(is_null($request)) return false;
        return  strpos(strtolower($request->getURL()),self::ApiPrefix) !== false;
    }

    /**
     * @return bool
     */
    protected function authorize(){
        return true;
    }

    protected function authenticate() {
        return true;
    }

    static $url_handlers = array(
        'PUT $SummitID!/get-schedule' => 'getSchedule',
        'PUT $EventID!/add-to-schedule' => 'addToSchedule',
        'PUT $EventID!/remove-from-schedule' => 'removeFromSchedule',
        'PUT $EventID!/add-feedback' => 'addFeedback',
    );

    static $allowed_actions = array(
        'getSchedule',
        'addToSchedule',
        'removeFromSchedule',
        'addFeedback',
    );

    public function getSchedule() {
        $filters = $this->getJsonRequest();
        $summit_types_filter = explode(',',$filters['summit_types']);
        $source = $filters['summit_source'];
        $summit_id = (int)$this->request->param('SummitID');

        if ($source == 'public') {
            $summit = $this->summit_repository->getById($summit_id);
            $events = $summit->getSchedule();
        } else {
            $attendee = Member::currentUser()->getSummitAttendee($summit_id);
            $events = $attendee->getSchedule();
        }


        $filtered_events = new ArrayList();

        if (count($summit_types_filter)) {
            foreach ($events as $event) {
                $allowed_summit_types = $event->getAllowedSummitTypes();
                if (count($allowed_summit_types)) {
                    $added = false;
                    foreach ($allowed_summit_types as $type) {
                        $event->SummitTypes .= ' summit_type_'.$type->ID;
                        if (in_array($type->ID,$summit_types_filter) && !$added) {
                            $filtered_events->push($event);
                            $added = true;
                        }
                    }
                } else {
                    $filtered_events->push($event);
                }

            }
        } else {
            $filtered_events = $events;
        }

        $sched_page = new SummitAppSchedPage();

        return $sched_page->renderSchedule($filtered_events);
    }

    public function addToSchedule() {
        try{
            $member = Member::currentUser();
            if(is_null($member)) return $this->permissionFailure();

            $event_id = (int)$this->request->param('EventID');
            $this->schedule_manager->addEventToSchedule(Member::currentUserID(), $event_id);
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    public function removeFromSchedule() {
        try{
            $member = Member::currentUser();
            if(is_null($member)) return $this->permissionFailure();

            $event_id = (int)$this->request->param('EventID');
            $this->schedule_manager->removeEventFromSchedule(Member::currentUserID(), $event_id);
        }
        catch(NotFoundEntityException $ex1){
            SS_Log::log($ex1,SS_Log::WARN);
            return $this->notFound($ex1->getMessage());
        }
        catch(Exception $ex){
            SS_Log::log($ex,SS_Log::ERR);
            return $this->serverError();
        }
    }

    /**
     * @return SS_HTTPResponse
     */
    public function addFeedback(){
        try {
            $data = $this->getJsonRequest();
            $event_id = (int)$this->request->param('EventID');
            $member_id = Member::CurrentUserID();

            if (!$data) return $this->serverError();

            $data['event_id'] = $event_id;
            $data['member_id'] = $member_id;

            $feedback = $this->eventfeedback_repository->getFeedback($event_id,$member_id);
            if ($feedback) {
                $this->schedule_manager->updateFeedback($data,$feedback);
                return $this->updated();
            } else {
                return $this->created($this->schedule_manager->addFeedback($data));
            }
        }
        catch (PolicyException $ex2) {
            SS_Log::log($ex2,SS_Log::ERR);
            return $this->validationError($ex2->getMessage());
        }
        catch (EntityValidationException $ex3) {
            SS_Log::log($ex3,SS_Log::ERR);
            return $this->validationError($ex3->getMessages());
        }
    }

}