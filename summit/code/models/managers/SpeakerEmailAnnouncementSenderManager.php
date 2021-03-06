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
final class SpeakerEmailAnnouncementSenderManager
    implements ISpeakerEmailAnnouncementSenderManager
{

    const TaskName = 'SpeakerSelectionAnnouncementSenderTask';

    /**
     * @var ITransactionManager
     */
    private $tx_manager;

    /**
     * @var IBatchTaskFactory
     */
    private $batch_task_factory;

    /**
     * @var IBatchTaskRepository
     */
    private $batch_repository;

    /**
     * @var ISpeakerRepository
     */
    private $speaker_repository;

    /**
     * @var ISpeakerSelectionAnnouncementSenderFactory
     */
    private $sender_factory;

    /**
     * @var ISpeakerSummitRegistrationPromoCodeRepository
     */
    private $promo_code_repository;

    public function __construct
    (
        IBatchTaskRepository $batch_repository,
        IBatchTaskFactory $batch_task_factory,
        IEntityRepository $speaker_repository,
        ISpeakerSelectionAnnouncementSenderFactory $sender_factory,
        ISpeakerSummitRegistrationPromoCodeRepository $promo_code_repository,
        ITransactionManager $tx_manager
    )
    {

        $this->batch_repository = $batch_repository;
        $this->batch_task_factory = $batch_task_factory;
        $this->speaker_repository = $speaker_repository;
        $this->tx_manager = $tx_manager;
        $this->sender_factory = $sender_factory;
        $this->promo_code_repository = $promo_code_repository;
    }

    public function sendSpeakersSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size)
    {

        $speaker_repository    = $this->speaker_repository;
        $sender_factory        = $this->sender_factory;
        $promo_code_repository = $this->promo_code_repository;
        $batch_repository      = $this->batch_repository;
        $batch_task_factory    = $this->batch_task_factory;

        return $this->tx_manager->transaction(function () use (
            $current_summit,
            $batch_size,
            $speaker_repository,
            $sender_factory,
            $promo_code_repository,
            $batch_repository,
            $batch_task_factory
        ) {
            try {

                $page      = 1;
                $page_size = $batch_size;
                $task      = $batch_repository->findByName(self::TaskName . '_' . $current_summit->getIdentifier());

                if (is_null($task)) {
                    //create task
                    $task = $batch_task_factory->buildBatchTask(self::TaskName . '_' . $current_summit->getIdentifier(), 0, $page);
                    $batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;
                // get speakers with not email sent for this current summit

                list($page, $page_size, $count, $speakers) = $speaker_repository->searchBySummitPaginated
                (
                    $current_summit,
                    $page,
                    $page_size
                );

                $speakers_notified = 0;

                echo sprintf('total speakers %s - count %s', $count, count($speakers)).PHP_EOL;

                foreach ($speakers as $speaker) {

                    if (!$speaker instanceof IPresentationSpeaker) continue;
                    // we need an email for this speaker ...
                    $email = $speaker->getEmail();
                    if (empty($email)) continue;

                    if ($speaker->announcementEmailAlreadySent($current_summit->ID)) continue;

                    $sender_service = $sender_factory->build($current_summit, $speaker, IPresentationSpeaker::RoleSpeaker);
                    // get registration code
                    if (is_null($sender_service)) {
                        echo sprintf('excluding email to %s', $email).PHP_EOL;
                        continue;
                    }

                    $code = null;

                    if ($speaker->hasPublishedPresentations($current_summit->getIdentifier(), IPresentationSpeaker::RoleSpeaker)) //get approved code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAccepted,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available promo code!!!');
                    } else if ($speaker->hasAlternatePresentations($current_summit->getIdentifier(), IPresentationSpeaker::RoleSpeaker)) // get alternate code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAlternate,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available alternate promo code!!!');
                    }

                    $params = array
                    (
                        'Speaker' => $speaker,
                        'Summit'  => $current_summit,
                        "Role"    => IPresentationSpeaker::RoleSpeaker
                    );

                    if (!is_null($code)) {
                        $speaker->registerSummitPromoCode($code);
                        $code->setEmailSent(true);
                        $code->write();
                        $params['PromoCode'] = $code;
                    }
                    echo sprintf('sending email to %s', $email).PHP_EOL;
                    $sender_service->send($params);
                    ++$speakers_notified;
                }
                $task->updatePage($count, $page_size);
                $task->write();
                return $speakers_notified;
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
                throw $ex;
            }
        });
    }

    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return void
     */
    public function sendModeratorsSelectionAnnouncementBySummit(ISummit $current_summit, $batch_size)
    {
        $speaker_repository = $this->speaker_repository;
        $sender_factory = $this->sender_factory;
        $promo_code_repository = $this->promo_code_repository;
        $batch_repository = $this->batch_repository;
        $batch_task_factory = $this->batch_task_factory;

        return $this->tx_manager->transaction(function () use (
            $current_summit,
            $batch_size,
            $speaker_repository,
            $sender_factory,
            $promo_code_repository,
            $batch_repository,
            $batch_task_factory
        ) {
            try {

                $page      = 1;
                $page_size = $batch_size;
                $task      = $batch_repository->findByName(self::TaskName . '_MODERATORS_' . $current_summit->getIdentifier());

                if (is_null($task)) {
                    //create task
                    $task = $batch_task_factory->buildBatchTask(self::TaskName . '_MODERATORS_' . $current_summit->getIdentifier(), 0, $page);
                    $batch_repository->add($task);
                }

                $page = $task->getCurrentPage();
                echo "Processing Page " . $page . PHP_EOL;
                // get speakers with not email sent for this current summit

                list($page, $page_size, $count, $moderators) = $speaker_repository->searchModeratorsBySummitPaginated
                (
                    $current_summit,
                    $page,
                    $page_size
                );

                $speakers_notified = 0;

                echo sprintf('total speakers %s - count %s', $count, count($moderators)).PHP_EOL;

                foreach ($moderators as $moderator) {

                    if (!$moderator instanceof IPresentationSpeaker) continue;
                    // we need an email for this speaker ...
                    $email = $moderator->getEmail();
                    if (empty($email)) continue;

                    if ($moderator->announcementEmailAlreadySent($current_summit->ID)) continue;

                    $sender_service = $sender_factory->build($current_summit, $moderator, IPresentationSpeaker::RoleModerator);
                    // get registration code
                    if (is_null($sender_service)) {
                        echo sprintf('excluding email to %s', $email).PHP_EOL;
                        continue;
                    }

                    $code = null;

                    if ($moderator->hasPublishedPresentations($current_summit->getIdentifier(), IPresentationSpeaker::RoleModerator)) //get approved code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAccepted,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available promo code!!!');
                    } else if ($moderator->hasAlternatePresentations($current_summit->getIdentifier(), IPresentationSpeaker::RoleModerator)) // get alternate code
                    {
                        $code = $promo_code_repository->getNextAvailableByType
                        (
                            $current_summit,
                            ISpeakerSummitRegistrationPromoCode::TypeAlternate,
                            $batch_size
                        );
                        if (is_null($code)) throw new Exception('not available alternate promo code!!!');
                    }

                    $params = array
                    (
                        'Speaker' => $moderator,
                        'Summit' => $current_summit,
                        "Role" => IPresentationSpeaker::RoleModerator
                    );

                    if (!is_null($code)) {
                        $moderator->registerSummitPromoCode($code);
                        $code->setEmailSent(true);
                        $code->write();
                        $params['PromoCode'] = $code;
                    }
                    echo sprintf('sending email to %s', $email).PHP_EOL;
                    $sender_service->send($params);
                    ++$speakers_notified;
                }
                $task->updatePage($count, $page_size);
                $task->write();
                return $speakers_notified;
            } catch (Exception $ex) {
                SS_Log::log($ex->getMessage(), SS_Log::ERR);
                throw $ex;
            }
        });
    }

    private static $excluded_tracks = [
        6  => [40, 41, 46, 45, 48],
        7  => [49, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100],
        22 => [],
    ];

    /**
     * @param ISummit $current_summit
     * @param int $batch_size
     * @return int
     */
    public function sendUploadSlidesAnnouncementBySummit(ISummit $current_summit, $batch_size)
    {
        return $this->tx_manager->transaction(function () use ($current_summit, $batch_size) {
            if (!isset(self::$excluded_tracks[$current_summit->getIdentifier()]))
                throw new EntityValidationException($errors = [sprintf("exclude tracks not set for summit id %s", $current_summit->getIdentifier())]);

            list($count, $speakers) = $this->speaker_repository->searchSpeakerBySummitPaginatedForUploadSlidesAnnouncement($current_summit, 1, $batch_size, self::$excluded_tracks[$current_summit->getIdentifier()]);
            $send                   = 0;

            foreach ($speakers as $speaker) {
                /* @var DataList */
                $presentations = $speaker->PublishedPresentations($current_summit->ID);

                if (!$presentations->exists()) {
                    echo "Skipping {$speaker->getName()}. Has no published presentations" . PHP_EOL;
                    continue;
                }

                if (!$speaker->Member()->exists() || !EmailValidator::validEmail($speaker->Member()->Email)) {
                    echo $speaker->getName()." (".$speaker->Member()->Email . ") is not a valid email address. Skipping." . PHP_EOL;
                    continue;
                }

                $to      = $speaker->Member()->Email;
                $subject = "Important Speaker Information for OpenStack Summit in {$current_summit->Title}";

                $email = EmailFactory::getInstance()->buildEmail('do-not-reply@openstack.org', $to, $subject);

                $email->setUserTemplate("upload-presentation-slides-email");
                $email->populateTemplate([
                    'Speaker'       => $speaker,
                    'Presentations' => $presentations,
                    'Summit'        => $current_summit
                ]);

                $email->send();

                $notification            = new PresentationSpeakerUploadPresentationMaterialEmail();
                $notification->SpeakerID = $speaker->ID;
                $notification->SummitID  = $current_summit->ID;
                $notification->SentDate  = MySQLDatabase56::nowRfc2822();
                $notification->write();
                ++$send;
                echo 'Email sent to ' . $to . ' (' . $speaker->getName() . ')' . PHP_EOL;
            }
            return $send;
        });
    }
}