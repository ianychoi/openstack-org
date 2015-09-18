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

PublisherSubscriberManager::getInstance()->subscribe('updated_summit_entity', function($entity_id, $class_name){

    $event = new SummitEntityEvent();
    $event->EntityClassName = $class_name;
    $event->EntityID = $entity_id;
    $event->Type = 'UPDATE';
    $event->OwnerID = Member::currentUserID();
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe('inserted_summit_entity', function($entity_id, $class_name){

    $event = new SummitEntityEvent();
    $event->EntityClassName = $class_name;
    $event->EntityID = $entity_id;
    $event->Type = 'INSERT';
    $event->OwnerID = Member::currentUserID();
    $event->write();
});

PublisherSubscriberManager::getInstance()->subscribe('deleted_summit_entity', function($entity_id, $class_name){

    $event = new SummitEntityEvent();
    $event->EntityClassName = $class_name;
    $event->EntityID = $entity_id;
    $event->Type = 'DELETE';
    $event->OwnerID = Member::currentUserID();
    $event->write();
});