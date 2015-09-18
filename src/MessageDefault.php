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
    public function setFrom($from_mail, $name=null)
    {
        $this->from = [$from_mail, $name];
        return $this;
    }

    /**
     * @param string $path
     * @return $this
     */
    public function setReturnPath($path)
    {
        $this->return_path = $path;
        return $this;
    }

    /**
     * @param string $reply_mail
     * @param string $name
     * @return $this
     */
    public function setReplyTo($reply_mail, $name=null)
    {
        $this->reply_to = [$reply_mail, $name];
        return $this;
    }

    /**
     * @param string $name     name of the header
     * @param string $value    value of the header
     * @param string $type     type of header: text, date, and else?
     * @return $this
     */
    public function setHeader($name, $value, $type='text')
    {
        $this->headers[] = [$name, $value, $type];
        return $this;
    }

    /**
     * set for bulk mail.
     *
     * @return MessageDefault
     */
    public function setBulk()
    {
        return $this->setHeader('Precedence', 'bulk');
    }
}