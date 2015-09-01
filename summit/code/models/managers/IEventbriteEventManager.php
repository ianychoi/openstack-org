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
interface IEventbriteEventManager
{
    /**
     * @param string $type
     * @param string $api_url
     * @return IEventbriteEvent
     */
    public function registerEvent($type, $api_url);

    /**
     * @param int $bach_size
     * @return int
     */
    public function ingestEvents($bach_size);


    /**
     * @param $member
     * @param string $order_external_id
     * @param string $attendee_external_id
     * @return ISummitAttendee
     * @throws MultipleAttendeesOrderException
     */
    public function registerAttendee($member, $order_external_id, $attendee_external_id = null);

    /**
     * @param $order_external_id
     * @throw InvalidEventbriteOrderStatusException
     */
    public function getOrderAttendees($order_external_id);
}