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
 * Class SapphireRoomMetricsRepository
 */
class SapphireRoomMetricsRepository
    extends SapphireRepository
    implements IRoomMetricsRepository {

    public function __construct(){
        parent::__construct(new RoomMetricSampleData());
    }

    /**
     * @param int $event_id
     * @param int $attendee_id
     * @return IRSVP|null
     */
    public function getByRoomAndDate($room_id, $start_date, $end_date)
    {
        $metrics = RoomMetricSampleData::get()
            ->leftJoin("RoomMetricType","RoomMetricType.ID = RoomMetricSampleData.TypeID")
            ->where("RoomMetricType.RoomID = $room_id
                     AND RoomMetricSampleData.TimeStamp BETWEEN ".strtotime($start_date)." AND ".strtotime($end_date));

        return $metrics;
    }

}