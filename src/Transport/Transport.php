<?php
namespace WScore\Mail\Transport;

use Swift;
use Swift_DependencyContainer;
use Swift_FileSpool;
use Swift_MailTransport;
use Swift_NullTransport;
use Swift_Preferences;
use Swift_SmtpTransport;
use Swift_SpoolTransport;

class Transport
{
    /**
     * creates a mailer instance that will send mail using PHP's mail() function.
     * not recommended to use this transport resulted for Japanese emails.
     *
     * @return Swift_MailTransport
     */
    public static function forgePhpMailer()
    {
        return Swift_MailTransport::newInstance();
    }

    /**
     * creates a transporter that will send mail via SMTP.
     * $security maybe 'ssl', 'tls' ?
     *
     * @param string $host
     * @param int    $port
     * @param string $security
     * @param string $user
     * @param string $pass
     * @return Swift_SmtpTransport
     */
    public static function forgeSmtp($host = 'localhost', $port = 25, $security = null, $user = null, $pass = null)
    {
        $transport = Swift_SmtpTransport::newInstance($host, $port, $security);
        if ($user) {
            $transport->setUsername($user);
            $transport->setPassword($pass);
            $transport->start();
            if (!$transport->isStarted()) {
                throw new \RuntimeException('cannot start SMPT transport.');
            }
        }
        return $transport;
    }

    /**
     * call this method to use mails in ISO2022
     * (this was the Japanese traditional mail encoding).
     */
    public static function goJapaneseIso2022()
    {
        Swift::init(function () {
            Swift_DependencyContainer::getInstance()
                ->register('mime.qpheaderencoder')
                ->asAliasOf('mime.base64headerencoder');
            Swift_Preferences::getInstance()->setCharset('iso-2022-jp');
        });
    }

    /**
     * creates a transporter instance that will NOT send.
     *
     * @return Swift_NullTransport
     */
    public static function forgeNull()
    {
        return Swift_NullTransport::newInstance();
    }

    /**
     * creates a mailer instance that will spool to memory.
     *
     * @param DumbSpool $spool
     * @return Swift_SpoolTransport
     */
    public static function forgeDumb(&$spool)
    {
        $spool = new DumbSpool();
        return Swift_SpoolTransport::newInstance($spool);
    }

    /**
     * creates a mailer instance that will save mail to a file.
     *
     * @param string $path
     * @return Swift_SpoolTransport
     */
    public static function forgeFileSpool($path)
    {
        $spool = new Swift_FileSpool($path);
        return Swift_SpoolTransport::newInstance($spool);
    }

}