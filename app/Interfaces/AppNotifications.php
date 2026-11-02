<?php
namespace App\Interfaces;

interface AppNotifications
{
    /**
     *  List notifications
     * @return void
     */
    public static function getProfileNotifications();
    public function getNotificationImage();
    public function extractMessageBody();
    public function setNotificationListData($profile, $notificationImage);
    public function createNotificationsList();
    public function shouldAddToList();
    public function setPushNotificationsData($profile);
    public function sendPushNotifications();

    /**
     *  Prepare notifications
     * @return void
     */
    public function getFormattedMessage($messageArray);
    public function getMessageReplacement($language);


    /**
     *  Send notifications
     * @return void
     */
    public function prepareNotifications();
    public function send();
}