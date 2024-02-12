<?php

namespace WHMCS\Module\Notification\Telegram;

use WHMCS\Config\Setting;
use WHMCS\Exception; 
use WHMCS\Module\Notification\DescriptionTrait;
use WHMCS\Module\Contracts\NotificationModuleInterface;
use WHMCS\Notification\Contracts\NotificationInterface;

class Telegram implements NotificationModuleInterface
{
    use DescriptionTrait;

    public function __construct()
    { // Define the module name and logo, personalise as required.
        $this->setDisplayName('Telegram')
            ->setLogoFileName('logo.png');
    }

    public function settings()
    { // Default settings required to carry out the notifications.
        return [
            'bot_token' => [
                'FriendlyName' => 'Telegram Bot Token',
                'Type' => 'text',
                'Description' => 'Token of your created Telegram Bot, find here (https://telegram.me/BotFather).',
            ],
            'bot_conversation_id' => [
                'FriendlyName' => 'Telegram Conversation ID',
                'Type' => 'text',
                'Description' => 'The Telegram identifier for your conversation which you wish to notify.',
            ],
            'bot_installation_branding' => [
                'FriendlyName' => 'Message Brand Name',
                'Type' => 'text',
                'Description' => 'When you are notified, this will be shown at the top to easily identify an installation.',
            ],
        ];
    }

    public function testConnection($settings)
    { // This is used to validate the connection upon enabling the module.
        $message = urlencode("*".$settings['bot_installation_branding']."*\nYou have successfully connected your WHMCS installation.");
        $response = file_get_contents("https://api.telegram.org/bot".$settings['bot_token']."/sendMessage?chat_id=".$settings['bot_conversation_id']."&parse_mode=markdown&text=".$message);
        if (!$response) {
            throw new Exception('An error occurred when communicating with Telegram.');
        }
    }

    public function notificationSettings()
    { // This function is unrequired and therefore not implemented.
        return [];
    }

    public function getDynamicField($fieldName, $settings)
    { // This function is unrequired and therefore not implemented.
        return [];
    }
    
    public function sendNotification(NotificationInterface $notification, $moduleSettings, $notificationSettings)
    { // This is the function for sending the actual message, you can personalise this as you wish.
        $message = urlencode("*".$moduleSettings['bot_installation_branding']."*: ".$notification->getTitle()."\n\n".$notification->getMessage()."\n[Launch »](".$notification->getUrl().")");
        $response = file_get_contents("https://api.telegram.org/bot".$moduleSettings['bot_token']."/sendMessage?chat_id=".$moduleSettings['bot_conversation_id']."&parse_mode=markdown&text=".$message);
    }
}