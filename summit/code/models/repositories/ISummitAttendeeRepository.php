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
interface ISummitAttendeeRepository extends IEntityRepository
{
    /**
     * @param int $member_id
     * @param int $summit_id
     * @return ISummitAttendee
     */
    public function getByMemberAndSummit($member_id, $summit_id);

    /**
     * @param string $search_term
     * @param int $page
     * @param int $page_size
     * @param int $summit_id
     * @return array
     */
    public function findAttendeesBySummit($search_term, $page, $page_size, $summit_id);

}