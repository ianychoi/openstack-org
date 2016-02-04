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
class AffiliationFactory implements IAffiliationFactory
{

    /**
     * @param $data
     * @param Member $member
     * @param Org $org
     * @return Affiliation
     */
    public function build($data, Member $member, Org $org)
    {
        $affiliation = Affiliation::create();
        $affiliation->OrganizationID = $org->ID;
        $affiliation->MemberID       = $member->ID;
        $affiliation->StartDate      = $data->StartDate;
        $affiliation->EndDate        = !empty($data->EndDate) ? $data->EndDate : null;
        $affiliation->Current        = $data->Current == 1 ? true : false;
        if (empty($affiliation->EndDate)) {
            $affiliation->Current = true;
        }
        return $affiliation;
    }
}