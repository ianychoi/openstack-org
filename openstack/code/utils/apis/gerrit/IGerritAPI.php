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
 * Interface IGerritAPI
 */
interface IGerritAPI {
	/**
	 * @param string $group_id
	 * @return array
	 */
	public function listAllMembersFromGroup($group_id);

	/**
     * @param string $gerrit_user_id
     * @param string $status
     * @param int $batch_size
     * @param int|null $start
     * @return mixed
     */
	public function getUserCommits($gerrit_user_id, $status, $batch_size = 500, $start = null);
} 