<?php

namespace App\Traits\Notifications;

trait PreparesNotificationsMessages
{
    public function getFormattedMessage($messageArray)
    {

        if (!$this->type) {
            return $messageArray;
        }

        foreach ($this->languages as $language) {
            if (isset($messageArray[$language])) {
                $messageArray[$language]['body'] = str_replace(
                    ':replace',
                    $this->getMessageReplacement($language),
                    $messageArray[$language]['body']
                );
            }
        }

        return $messageArray;
    }

    public function getMessageReplacement($language)
    {
        $replacement = '';

        switch ($this->type) {
            case 'STATUS_SHORTLISTED':
            case 'STATUS_ACCEPTED':
            case 'APPLICATION_STATUS_CHANGED':
                $replacement = $this->item->getTranslation('title', $language);
                break;

            case 'PAYMENT_READY_FOR_PICKUP':
            case 'PAYMENT_SETTLED':
            case 'PAYMENT_IN_PROCESS':
                $replacement = $this->item->title;

                break;
            default:
                $replacement = '';
                break;
        }

        return $replacement;
    }
}
