<?php
namespace WScore\Mail;

use Swift_Message;

class MessageDefault
{
    /**
     * @var array
     */
    private $from = [];

    /**
     * @var string
     */
    private $return_path = null;

    /**
     * @var array
     */
    public $reply_to = [];

    /**
     * @var array
     */
    public $headers = [];

    /**
     *
     */
    public function __construct()
    {
    }

    /**
     * @return MessageDefault
     */
    public static function newInstance()
    {
        return new self();
    }

    /**
     * @param Swift_Message $message
     */
    public function __invoke($message)
    {
        if($this->from) {
            $message->setFrom($this->from[0], $this->from[1]);
        }
        if($this->return_path) {
            $message->setReturnPath($this->return_path);
        }
        if($this->reply_to) {
            $message->setReplyTo($this->reply_to[0], $this->reply_to[1]);
        }
        foreach($this->headers as $header) {
            $method = 'add'.ucwords($header[2]).'Header';
            $message->getHeaders()->$method($header[0], $header[1]);
        }
    }

    /**
     * @param string $from_mail
     * @param string $name
     * @return $this
     */
    public function withFrom($from_mail, $name=null)
    {
        $self = clone($this);
        $self->from = [$from_mail, $name];
        return $self;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function withReturnPath($path)
    {
        $self = clone($this);
        $self->return_path = $path;
        return $self;
    }

    /**
     * @param string $reply_mail
     * @param string $name
     * @return $this
     */
    public function setReplyTo($reply_mail, $name=null)
    {
        $self = clone($this);
        $self->reply_to = [$reply_mail, $name];
        return $self;
    }

    /**
     * @param string $name     name of the header
     * @param string $value    value of the header
     * @param string $type     type of header: text, date, and else?
     * @return $this
     */
    public function setHeader($name, $value, $type='text')
    {
        $self = clone($this);
        $self->headers[] = [$name, $value, $type];
        return $self;
    }

    /**
     * set for bulk mail. not sure how helpful this is.
     * @see
     * https://support.google.com/mail/answer/81126
     *
     * @return MessageDefault
     */
    public function setBulk()
    {
        return $this->setHeader('Precedence', 'bulk');
    }
}