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
 * Class ScheduleManager
 */
final class ScheduleManager
{

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
     * @param IEntityRepository $summitevent_repository
     * @param IEntityRepository $attendee_repository
     * @param ITransactionManager $tx_manager
     */
    public function __construct(
        IEntityRepository $summitevent_repository,
        IEntityRepository $attendee_repository,
        ITransactionManager $tx_manager
    ) {
        $this->summitevent_repository = $summitevent_repository;
        $this->attendee_repository = $attendee_repository;
        $this->tx_manager = $tx_manager;
    }


    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function addEventToSchedule($member_id, $event_id)
    {

        $this_var = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $member_id,
            $event_id,
            $attendee_repository,
            $summitevent_repository
        ) {

            $event = $summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }

            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            $attendee->addToSchedule($event);
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::AddedToSchedule,
                array($member_id, $event));

            return $attendee;
        });
    }

    /**
     * @param $member_id
     * @param $event_id
     * @return mixed
     */
    public function removeEventFromSchedule($member_id, $event_id)
    {

        $this_var = $this;
        $summitevent_repository = $this->summitevent_repository;
        $attendee_repository = $this->attendee_repository;

        return $this->tx_manager->transaction(function () use (
            $this_var,
            $member_id,
            $event_id,
            $attendee_repository,
            $summitevent_repository
        ) {

            $event = $summitevent_repository->getById($event_id);
            if (!$event) {
                throw new NotFoundEntityException('Event', sprintf('id %s', $event_id));
            }
            $attendee = $attendee_repository->getByMemberAndSummit($member_id, $event->Summit->getIdentifier());

            if (!$attendee) {
                throw new NotFoundEntityException('Attendee', sprintf('id %s', $event_id));
            }

            $attendee->removeFromSchedule($event);
            PublisherSubscriberManager::getInstance()->publish(ISummitEntityEvent::RemovedToSchedule,
                array($member_id, $event));

            return $attendee;
        });
    }

} 