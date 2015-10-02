<?php
namespace WScore\Mail\BulkMail;

use WScore\Mail\Mailer;

/**
 * Class BulkMailer
 *
 * sends bulk mails to multiple email addresses,
 * each address receiving a mail.
 *
 * @package WScore\Mail\BulkMail
 */
class BulkMailSender
{
    /**
     * @var Mailer
     */
    public $mailer;

    /**
     * @var int
     */
    public $send_count = 0;

    /**
     * @param Mailer $mailer
     */
    public function __construct($mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @param Mailer $mailer
     * @return BulkMailSender
     */
    public static function forge($mailer)
    {
        return new self($mailer);
    }

    /**
     * sends a email to the each of the mailTo.
     *
     * @param array  $mailTo
     * @param string $subject
     * @param string $body
     * @param string $from
     * @return bool
     */
    public function send(array $mailTo, $subject, $body, $from)
    {
        $message = $this->mailer->message();
        $message->setBody($body);
        $message->setSubject($subject);
        $message->setFrom($from);

        $this->send_count = 0;
        foreach ($mailTo as $to) {
            $to = trim($to);
            if (!$to) {
                continue;
            }
            $message->setTo($to);
            if ($this->mailer->getSwiftMailer()->send($message)) {
                $this->send_count++;
            }
        }
        return true;
    }
}