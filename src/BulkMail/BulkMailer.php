<?php
namespace WScore\Mail\BulkMail;

use WScore\Mail\Mailer;
use WScore\Validation\Dio;
use WScore\Validation\ValidationFactory;

/**
 * Class BulkMailer
 *
 * sends bulk mails using BulkMailSender for sending many mails.
 * the inputs are:
 * - subject,
 * - from,
 * - body,
 * - testTo, and
 * - mailTo (list of mails).
 *
 * @package WScore\Mail\BulkMail
 */
class BulkMailer
{
    /**
     * @var ValidationFactory
     */
    private $factory;

    /**
     * @var Dio
     */
    private $dio;

    /**
     * @var BulkMailSender
     */
    private $mailer;

    /**
     * @param ValidationFactory $factory
     * @param BulkMailSender    $mailer
     */
    public function __construct($factory, $mailer)
    {
        $this->factory = $factory;
        $this->mailer  = $mailer;
    }

    /**
     * @param Mailer $mailer
     * @return BulkMailer
     */
    public static function forge($mailer)
    {
        $factory = new ValidationFactory('ja');
        $sender  = BulkMailSender::forge($mailer);
        return new self($factory, $sender);
    }

    /**
     * data must be an array containing:
     * - subject: string
     * - from: string
     * - body: string
     * - testTo: string/mail
     *
     * to must be an array containing list of email addresses.
     *
     * @param array $data
     * @return $this
     */
    private function validate(array $data)
    {
        $dio = $this->dio = $this->factory->on($data);
        $dio->asText('subject')->required();
        $dio->asText('body')->required();
        $dio->asMail('from')->required();
        $dio->asMail('testTo')->required();
        $dio->asMail('mailTo')->required();

        return $this;
    }

    /**
     * テスト先にメールを送信する。
     *
     * @param array $data
     * @param array $mailTo
     * @return bool
     */
    public function sendTest(array $data, array $mailTo)
    {
        if (!$this->checkMails($data, $mailTo)) {
            return false;
        }

        $this->mailer->send(
            [$this->getTestTo()],
            $this->getSubject(),
            $this->getBody(),
            $this->getFrom()
        );
        return true;
    }

    /**
     * バルクメールの入力チェックをする。
     *
     * @param array $data
     * @param array $mailTo
     * @return bool
     */
    public function checkMails(array $data, array $mailTo)
    {
        $data['mailTo'] = $mailTo;
        $this->validate($data);

        if ($this->fails()) {
            return false;
        }
        return true;
    }

    /**
     * 本番用：バルクメールを大量に送信する
     *
     * @param array $data
     * @param array $mailTo
     * @return bool
     */
    public function sendMails(array $data, array $mailTo)
    {
        if (!$this->checkMails($data, $mailTo)) {
            return false;
        }
        $this->mailer->send(
            $this->getMailTo(),
            $this->getSubject(),
            $this->getBody(),
            $this->getFrom()
        );
        if ($testTo = $this->getTestTo()) {
            $sent = $this->mailer->send_count;
            $this->mailer->send(
                [$testTo],
                $this->getSubject(),
                "mail sent: {$sent}\n" .
                "done at " . date('Y/m/d H:i:s'),
                $this->getFrom()
            );
        }
        return true;
    }

    /**
     * @return bool
     */
    public function fails()
    {
        return $this->dio->fails();
    }

    /**
     * @return array
     */
    public function errors()
    {
        $errors           = $this->dio->message();
        $errors['mailTo'] = array_values($errors['mailTo'] ?: []);
        return $errors;
    }

    /**
     * @return array
     */
    public function values()
    {
        return $this->dio->get();
    }

    /**
     * @return string
     */
    private function getSubject()
    {
        if ($this->fails()) {
            throw new \RuntimeException;
        }
        return $this->dio->get('subject');
    }

    /**
     * @return string
     */
    private function getBody()
    {
        if ($this->fails()) {
            throw new \RuntimeException;
        }
        return $this->dio->get('body');
    }

    /**
     * @return string
     */
    private function getFrom()
    {
        if ($this->fails()) {
            throw new \RuntimeException;
        }
        return $this->dio->get('from');
    }

    /**
     * @return array
     */
    private function getMailTo()
    {
        if ($this->fails()) {
            throw new \RuntimeException;
        }
        return $this->dio->get('mailTo');
    }

    /**
     * @return string
     */
    private function getTestTo()
    {
        if ($this->fails()) {
            throw new \RuntimeException;
        }
        return $this->dio->get('testTo');
    }
}