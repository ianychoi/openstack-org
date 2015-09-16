<?php
/**
 * Copyright 2014 Openstack Foundation
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
 * Class ScheduleManager
 */
final class ScheduleManager {

	/**
	 * @var IEntityRepository
	 */
	private $summitevent_repository;

    /**
     * @var IEntityRepository
     */
    private $attendee_repository;

	/**
	 * @var ITransactionManager
	 */
	private $tx_manager;

	/**
	 * @param IEntityRepository   $schedule_repository
	 * @param ITransactionManager $tx_manager
	 */
	public function __construct(IEntityRepository $summitevent_repository,IEntityRepository $attendee_repository,
	                            ITransactionManager $tx_manager){
		$this->summitevent_repository = $summitevent_repository;
        $this->attendee_repository = $attendee_repository;
        $this->tx_manager          = $tx_manager;
	}


    /**
     * @param $event_id
     */
    public function addEventToSchedule($event_id){

        $this_var              = $this;
        $summitevent_repository  = $this->summitevent_repository;
        $attendee_repository  = $this->attendee_repository;

        return  $this->tx_manager->transaction(function() use ($this_var, $event_id, $attendee_repository, $summitevent_repository){

            $event = $summitevent_repository->getById($event_id);
            if(!$event)
                throw new NotFoundEntityException('Event',sprintf('id %s',$event_id ));

            $member_id = Member::currentUserID();
            $attendee = $attendee_repository->getByMemberAndSummit($member_id,$event->Summit->getIdentifier());

            if(!$attendee)
                throw new NotFoundEntityException('Attendee',sprintf('id %s',$event_id ));

            $attendee->addToSchedule($event);

            return $attendee;
        });
    }

    /**
     * @param $event_id
     */
    public function removeEventFromSchedule($event_id){

        $this_var              = $this;
        $summitevent_repository  = $this->summitevent_repository;
        $attendee_repository  = $this->attendee_repository;

        return  $this->tx_manager->transaction(function() use ($this_var, $event_id, $attendee_repository, $summitevent_repository){

            $event = $summitevent_repository->getById($event_id);
            if(!$event)
                throw new NotFoundEntityException('Event',sprintf('id %s',$event_id ));

            $member_id = Member::currentUserID();
            $attendee = $attendee_repository->getByMemberAndSummit($member_id,$event->Summit->getIdentifier());

            if(!$attendee)
                throw new NotFoundEntityException('Attendee',sprintf('id %s',$event_id ));

            $attendee->removeFromSchedule($event);

            return $attendee;
        });
    }

} 