<?php
namespace WScore\Mail;

use Closure;
use Swift_Mailer;
use Swift_Message;
use Swift_Transport;

class Mailer
{
    /**
     * @var Swift_Mailer
     */
    private $swift_mailer;

    /**
     * @var Closure|MessageDefault
     */
    private $message_default;

    // +----------------------------------------------------------------------+
    //  managing objects.
    // +----------------------------------------------------------------------+
    /**
     * @param Swift_Transport $transport
     */
    public function __construct($transport)
    {
        $this->swift_mailer = Swift_Mailer::newInstance($transport);
    }

    /**
     * @param Closure|MessageDefault $default
     * @return $this
     */
    public function setDefault($default)
    {
        $this->message_default = $default;
        return $this;
    }

    /**
     * @return Closure|MessageDefault
     */
    public function getMessageDefault()
    {
        return $this->message_default;
    }

    /**
     * @return Swift_Mailer
     */
    public function getSwiftMailer()
    {
        return $this->swift_mailer;
    }

    // +----------------------------------------------------------------------+
    //  sending emails
    // +----------------------------------------------------------------------+
    /**
     * create a new instance of Swift_Message with
     * MessageDefault applied. to get a clean message,
     * just use Swift_Message::newInstance().
     *
     * @return Swift_Message
     */
    public function message()
    {
        $message = Swift_Message::newInstance();
        if ($this->message_default) {
            $default = $this->message_default;
            $default->__invoke($message);
        }
        return $message;
    }

    /**
     * sends email in text (UTF-8).
     *
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendText($text, $callable)
    {
        return $this->send($text, null, $callable);
    }

    /**
     * sends email in html format.
     *
     * @param string   $html
     * @param callable $callable
     * @return int
     */
    public function sendHtml($html, $callable)
    {
        return $this->send($html, 'text/html', $callable);
    }

    /**
     * sends email in ISO2022 encoding (Japanese mail encoding).
     * must run MailerFactory::goJapaneseIso2022() in prior to
     * using this method.
     *
     * @param string   $text
     * @param callable $callable
     * @return int
     */
    public function sendJIS($text, $callable)
    {
        return $this->send($text, null, $callable, function ($message) {
            /** @var Swift_Message $message */
            $message
                ->setCharset('iso-2022-jp')
                ->setEncoder(new \Swift_Mime_ContentEncoder_PlainContentEncoder('7bit'))
                ->setMaxLineLength(0);
        });
    }

    /**
     * sends an email.
     *
     * @param string      $text
     * @param null|string $type
     * @param callable    $callable
     * @param callable    $preCallable
     * @return int
     */
    private function send($text, $type, $callable, $preCallable = null)
    {
        $message = $this->message();
        if ($preCallable) {
            $preCallable($message);
        }
        $message->setBody($text, $type);
        $callable($message);
        return $this->swift_mailer->send($message);
    }
}
