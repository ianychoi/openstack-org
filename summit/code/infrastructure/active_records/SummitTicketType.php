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
 * Class SummitTicketType
 * https://www.eventbrite.com/developer/v3/endpoints/events/#ebapi-get-events-id-ticket-classes-ticket-class-id
 */
class SummitTicketType extends DataObject
{
    private static $db = array
    (
        'ExternalId' => 'Text',
        'Name' => 'Text',
    );

    private static $has_one = array
    (
        'Summit' => 'Summit'
    );

    private static $has_many = array
    (
        'AllowedSummitTypes' => 'SummitType'
    );

    private static $summary_fields = array
    (
        'Title'  => 'Title',
        'Status' => 'Status',
    );

    private static $searchable_fields = array
    (
    );
}