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
final class EventbriteEventManager implements IEventbriteEventManager
{

    /**
     * @var IEventbriteEventRepository
     */
    private $repository;

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IEventbriteEventFactory
     */
    private $factory;

    /**
     * @var IEventbriteRestApi
     */
    private $api;

    /**
     * @var IMemberRepository
     */
    private $member_repository;

    /**
     * @var ISummitAttendeeFactory
     */
    private $attendee_factory;

    /**
     * @var ISummitAttendeeRepository
     */
    private $attendee_repository;

    /**
     * @var ISummitRepository
     */
    private $summit_repository;

    public function __construct
    (
        IEventbriteEventRepository $repository,
        IEventbriteEventFactory $factory,
        IEventbriteRestApi $api,
        IMemberRepository $member_repository,
        ISummitAttendeeFactory $attendee_factory,
        ISummitAttendeeRepository $attendee_repository,
        ISummitRepository $summit_repository,
        ITransactionManager $tx_manager
    ) {
        $this->repository = $repository;
        $this->factory = $factory;
        $this->api = $api;
        $this->member_repository = $member_repository;
        $this->attendee_factory = $attendee_factory;
        $this->attendee_repository = $attendee_repository;
        $this->summit_repository = $summit_repository;
        $this->tx_manager = $tx_manager;
    }

    /**
     * @param string $type
     * @param string $api_url
     * @return IEventbriteEvent
     */
    public function registerEvent($type, $api_url)
    {
        $repository = $this->repository;
        $factory = $this->factory;

        return $this->tx_manager->transaction(function () use ($type, $api_url, $repository, $factory) {

            $old_one = $repository->getByApiUrl($api_url);
            if ($old_one) {
                throw new EntityAlreadyExistsException('IEventbriteEvent');
            }

            $new_event = $factory->build($type, $api_url);

            $repository->add($new_event);

            return $new_event;
        });
    }

    /**
     * @param int $bach_size
     * @return int
     */
    public function ingestEvents($bach_size)
    {
        $repository = $this->repository;
        $api                 = $this->api;
        $member_repository   = $this->member_repository;
        $attendee_factory    = $this->attendee_factory;
        $attendee_repository = $this->attendee_repository;
        $summit_repository   = $this->summit_repository;

        return $this->tx_manager->transaction(function () use (
            $bach_size,
            $repository,
            $api,
            $member_repository,
            $attendee_factory,
            $attendee_repository,
            $summit_repository
        ) {

            list($list, $count) = $repository->getUnprocessed(0, $bach_size);

            $api->setCredentials(array('token' => EVENTBRITE_PERSONAL_OAUTH2_TOKEN));

            foreach ($list as $event)
            {
                $json_data = $api->getEntity($event->getApiUrl(), array('expand' => 'attendees'));
                if (isset($json_data['attendees']))
                {
                    $order_date = $json_data['created'];

                    foreach ($json_data['attendees'] as $attendee)
                    {
                        $profile         = $attendee['profile'];
                        $email           = $profile['email'];
                        $answers         = $attendee['answers'];
                        $order_id        = $attendee['order_id'];
                        $event_id        = $attendee['event_id'];
                        $ticket_class_id = $attendee['ticket_class_id'];

                        if (empty($email))
                        {
                            continue;
                        }
                        $member = $member_repository->findByEmail($email);
                        if (is_null($member))
                        {
                            continue;
                        }
                        $current_summit = $summit_repository->getByExternalEventId($event_id);

                        if(!$current_summit) continue;

                        $old_attendee   = $attendee_repository->getByMemberAndSummit
                        (
                            $member->getIdentifier(),
                            $current_summit->getIdentifier()
                        );

                        if ($old_attendee)
                        {
                            continue;
                        }

                        $attendee = $attendee_factory->build($member, $current_summit, $order_id, $ticket_class_id, $order_date);

                        $attendee_repository->add($attendee);
                    }
                }
                $event->markAsProcessed();
            };
        });
    }
}