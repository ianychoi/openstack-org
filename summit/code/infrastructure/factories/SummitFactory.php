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
 * Class SummitFactory
 */
final class SummitFactory
	implements ISummitFactory {

	/**
	 * @param SummitMainInfo $info
	 * @return ISummit
	 */
	public function buildSummit(SummitMainInfo $info)
	{
		$summit = new Summit();
        $summit->registerMainInfo($info);

		return $summit;
	}

	/**
	 * @param array $data
	 * @return SummitMainInfo
	 */
	public function buildMainInfo(array $data)
	{
		$main_info = new SummitMainInfo(trim($data['name']) ,trim($data['start_date']), trim($data['end_date']));
		return $main_info;
	}

}