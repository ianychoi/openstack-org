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
final class SummitEntity extends DataExtension
{

    private $updating  = false;
    private $inserting = false;

    public function onBeforeWrite()
    {
        if($this->owner->ID === 0)
        {
            $this->inserting = true;
            $this->updating  = false;
        }
        else
        {
            $this->inserting = false;
            $this->updating  = true;
        }
    }

    public function onAfterWrite()
    {
        if($this->inserting)
        {
            // insert

            PublisherSubscriberManager::getInstance()->publish
            (
                'inserted_summit_entity',
                array
                (
                    $this->owner->ID,
                    $this->owner->ClassName,
                )
            );
        }
        else
        {
            PublisherSubscriberManager::getInstance()->publish
            (
                'updated_summit_entity',
                array
                (
                    $this->owner->ID,
                    $this->owner->ClassName,
                )
            );
        }
    }

    public function onBeforeDelete()
    {

        PublisherSubscriberManager::getInstance()->publish
        (
            'deleted_summit_entity',
            array
            (
                $this->owner->ID,
                $this->owner->ClassName,
            )
        );
    }

    public function onAfterDelete() {
    }

}