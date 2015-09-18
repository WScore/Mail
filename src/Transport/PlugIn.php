<?php
namespace WScore\Mail;

use Swift_Plugins_AntiFloodPlugin;
use Swift_Plugins_ThrottlerPlugin;
use Swift_Transport;

class PlugIn
{

    /**
     * @param Swift_Transport $mailer
     * @param int             $threshold
     * @param int             $sleep
     */
    public static function antiFlood($mailer, $threshold = 99, $sleep = 0)
    {
        $plugIn = new Swift_Plugins_AntiFloodPlugin($threshold, $sleep);
        $mailer->registerPlugin($plugIn);
    }

    /**
     * @param Swift_Transport $mailer
     * @param int             $rate
     * @param int             $mode
     */
    public static function throttle($mailer, $rate = 10, $mode = Swift_Plugins_ThrottlerPlugin::MESSAGES_PER_MINUTE)
    {
        $plugIn = new Swift_Plugins_ThrottlerPlugin($rate, $mode);
        $mailer->registerPlugin($plugIn);
    }

}