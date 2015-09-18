Mail
====

Wrapper classes for SwiftMailer to simplify some operations.

*   Framework agnostic.
*   License: MIT License.
*   PSR: PSR-1, PSR-2, and PSR-4.

Basic Usage
----

It's as simple as 1, 2, and 3...

```php
use WScore\Mail\Transport\Transport;
use WScore\Mail\Mailer;

// 1. create a transport
$transport = Transport::forgeSmtp();

// 2. create a mailer
$mailer = Mailer::newInstance($transport);

// 3. send mails
$mailer->sendText('hello world', function(Swift_Message $message) {
    $message->setTo('test@example.com', 'tested');
});
```

### Message Default

You can create a `MessageDefault` object to set a default message in `Mailer`. The methods in `MessageDefault` objects, which start with `with`, are immutable call. 

```php
// somewhere in a config file. 
use WScore\Mail\MessageDefault;
$default = MessageDefault::newInstance()
    ->withFrom('from@example.com', 'from address');
    ->withReturnPath('bad-mail@example.com')
    ->withReplyTo('sender@example.com', 'sender name');

$mailer = Mailer::newInstance($transport)
    ->setMessageDefault($default);

// later on...
$mailer->setMessageDefault(
    $mailer->getMessageDefault()->withBulk() // send bulk mail?
);
$mailer->sendHtml('<h1>hi</h1>', function(Swift_Message $message) {
    $message->setTo('to@example.com');
});
```

> Or, just use a closure as MessageDefault. 


### Japanese ISO-2022

sending Japanese in ISO2022 encoding. You must call `Transport::goJapaneseIso2022()` and use `$mailer->sendJIS(...)` method as;

```php
Transport::goJapaneseIso2022();
$transport = Transport::forgeSmtp();
$mailer = Mailer::newInstance($transport);
$mailer->sendJIS('some japanese text here',
    function(Swift_Message $message) {
        $message->setTo('test@example.com', 'tested');
});
```

> Use SMTP transport when sending emails in ISO2022 encoding based on my experience...

