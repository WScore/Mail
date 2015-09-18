<?php
namespace tests\Swift;

use Swift_Message;
use WScore\Mail\Mailer;
use WScore\Mail\Transport\Transport;
use WScore\Mail\Transport\DumbSpool;
use WScore\Mail\MessageDefault;

require_once(dirname(__DIR__) . '/autoloader.php');

class SwiftTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    function sendText_sends_message_with_contentType_text_plain()
    {
        /** @var DumbSpool $spool */
        $mailer = Mailer::newInstance(Transport::forgeDumb($spool));
        $this->assertEquals('WScore\Mail\Mailer', get_class($mailer));
        
        $mailer->sendText('test mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('test@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['test@example.com'=> ''], $msg->getTo());
        $this->assertEquals('text/plain; charset=utf-8', $msg->getHeaders()->get('Content-Type')->getFieldBody());
    }

    /**
     * @test
     */
    function sendHtml_sends_message_with_contentType_text_html()
    {
        /** @var DumbSpool $spool */
        $mailer = Mailer::newInstance(Transport::forgeDumb($spool));

        $mailer->sendHtml('html mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('html@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['html@example.com'=> ''], $msg->getTo());
        $this->assertEquals('text/html; charset=utf-8', $msg->getHeaders()->get('Content-Type')->getFieldBody());
    }

    /**
     * @test
     */
    function default_sets_messages()
    {
        $default = MessageDefault::newInstance()
            ->withFrom('from@test.com', 'from')
            ->withReplyTo('reply@test.com', 'reply')
            ->withReturnPath('return@test.com');
        /** @var DumbSpool $spool */
        $mailer = Mailer::newInstance(Transport::forgeDumb($spool));
        $mailer->setMessageDefault($default);
        $mailer->sendText('test mail', function($message) {
            /** @var Swift_Message $message */
            $message->setTo('test@example.com');
        });
        $msg = $spool->getMessage();
        $this->assertEquals(['test@example.com'=> ''], $msg->getTo());
        $this->assertEquals(['from@test.com'=> 'from'], $msg->getFrom());
        $this->assertEquals(['reply@test.com'=> 'reply'], $msg->getReplyTo());
        $this->assertEquals('return@test.com', $msg->getReturnPath());
    }
}
