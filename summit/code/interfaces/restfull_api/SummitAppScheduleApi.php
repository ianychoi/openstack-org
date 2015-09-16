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
    private $attendee_repository;

    /**
     * @var IScheduleManager
     */
    private $schedule_manager;

    public function __construct(){
        parent::__construct();
        $this->summit_repository  = new SapphireSummitRepository;
        $this->summitevent_repository = new SapphireSummitEventRepository();
        $this->attendee_repository = new SapphireSummitAttendeeRepository();

        $this->schedule_manager = new ScheduleManager($this->summitevent_repository, $this->attendee_repository,
                                                  SapphireTransactionManager::getInstance());


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
    );

    static $allowed_actions = array(
        'getSchedule',
        'addToSchedule',
        'removeFromSchedule',
    );

    public function getSchedule() {
        $filters = $this->getJsonRequest();
        $summit_types_filter = explode(',',$filters['summit_types']);
        $summit_id = (int)$this->request->param('SummitID');
        $summit = $this->summit_repository->getById($summit_id);
        $events = $summit->getSchedule();
        $filtered_events = new ArrayList();

        if (count($summit_types_filter)) {
            foreach ($events as $event) {
                $allowed_summit_types = $event->getAllowedSummitTypes();
                if (count($allowed_summit_types)) {
                    foreach ($allowed_summit_types as $type) {
                        $event->SummitTypes .= ' summit_type_'.$type->ID;
                        if (in_array($type->ID,$summit_types_filter)) {
                            $filtered_events->push($event);
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
            $event_id = (int)$this->request->param('EventID');
            $this->schedule_manager->addEventToSchedule($event_id);
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
            $event_id = (int)$this->request->param('EventID');
            $this->schedule_manager->removeEventFromSchedule($event_id);
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

}