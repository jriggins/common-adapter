<?php declare(strict_types=1);

namespace Novuso\Common\Adapter\Mail\SwiftMailer;

use Novuso\Common\Application\Mail\AttachmentInterface;
use Novuso\Common\Application\Mail\Exception\MailException;
use Novuso\Common\Application\Mail\MailServiceInterface;
use Novuso\Common\Application\Mail\Message;
use Novuso\Common\Domain\Value\DateTime\DateTime;
use Swift_Attachment;
use Swift_Mailer;
use Swift_Message;
use Throwable;

/**
 * SwiftMailerMailService is a Swift Mailer mail service adapter
 *
 * @copyright Copyright (c) 2017, Novuso. <http://novuso.com>
 * @license   http://opensource.org/licenses/MIT The MIT License
 * @author    John Nickell <email@johnnickell.com>
 */
class SwiftMailerMailService implements MailServiceInterface
{
    /**
     * Swift mailer
     *
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * Constructs SwiftMailerMailService
     *
     * @param Swift_Mailer $mailer The mailer
     */
    public function __construct(Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * {@inheritdoc}
     */
    public function send(Message $message): void
    {
        try {
            $swiftMessage = new Swift_Message();
            $this->setCharset($message, $swiftMessage);
            $this->setSubject($message, $swiftMessage);
            $this->setFrom($message, $swiftMessage);
            $this->setTo($message, $swiftMessage);
            $this->setReplyTo($message, $swiftMessage);
            $this->setCc($message, $swiftMessage);
            $this->setBcc($message, $swiftMessage);
            $this->setContent($message, $swiftMessage);
            $this->setSender($message, $swiftMessage);
            $this->setReturnPath($message, $swiftMessage);
            $this->setPriority($message, $swiftMessage);
            $this->setTimestamp($message, $swiftMessage);
            $this->setMaxLineLength($message, $swiftMessage);
            $this->setAttachments($message, $swiftMessage);
            $this->mailer->send($swiftMessage);
        } catch (Throwable $e) {
            throw new MailException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * Sets the charset
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setCharset(Message $message, Swift_Message $swiftMessage)
    {
        $swiftMessage->setCharset($message->getCharset());
    }

    /**
     * Sets the subject
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setSubject(Message $message, Swift_Message $swiftMessage)
    {
        $subject = $message->getSubject();
        if ($subject !== null) {
            $swiftMessage->setSubject($subject);
        }
    }

    /**
     * Sets the From addresses
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setFrom(Message $message, Swift_Message $swiftMessage)
    {
        foreach ($message->getFrom() as $from) {
            $swiftMessage->addFrom($from['address'], $from['name']);
        }
    }

    /**
     * Sets the To addresses
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setTo(Message $message, Swift_Message $swiftMessage)
    {
        foreach ($message->getTo() as $to) {
            $swiftMessage->addTo($to['address'], $to['name']);
        }
    }

    /**
     * Sets the Reply-To addresses
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setReplyTo(Message $message, Swift_Message $swiftMessage)
    {
        foreach ($message->getReplyTo() as $replyTo) {
            $swiftMessage->addReplyTo($replyTo['address'], $replyTo['name']);
        }
    }

    /**
     * Sets the CC addresses
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setCc(Message $message, Swift_Message $swiftMessage)
    {
        foreach ($message->getCc() as $cc) {
            $swiftMessage->addCc($cc['address'], $cc['name']);
        }
    }

    /**
     * Sets the BCC addresses
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setBcc(Message $message, Swift_Message $swiftMessage)
    {
        foreach ($message->getBcc() as $bcc) {
            $swiftMessage->addBcc($bcc['address'], $bcc['name']);
        }
    }

    /**
     * Sets the content parts
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setContent(Message $message, Swift_Message $swiftMessage)
    {
        $bodySet = false;
        foreach ($message->getContent() as $content) {
            if (!$bodySet) {
                $swiftMessage->setBody($content['content'], $content['content_type'], $content['charset']);
                $bodySet = true;
            } else {
                $swiftMessage->addPart($content['content'], $content['content_type'], $content['charset']);
            }
        }
    }

    /**
     * Sets the sender
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setSender(Message $message, Swift_Message $swiftMessage)
    {
        $sender = $message->getSender();
        if ($sender !== null) {
            $swiftMessage->setSender($sender['address'], $sender['name']);
        }
    }

    /**
     * Sets the return path
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setReturnPath(Message $message, Swift_Message $swiftMessage)
    {
        $returnPath = $message->getReturnPath();
        if ($returnPath !== null) {
            $swiftMessage->setReturnPath($returnPath);
        }
    }

    /**
     * Sets the priority
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setPriority(Message $message, Swift_Message $swiftMessage)
    {
        $swiftMessage->setPriority($message->getPriority()->value());
    }

    /**
     * Sets the timestamp
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setTimestamp(Message $message, Swift_Message $swiftMessage)
    {
        $timestamp = $message->getTimestamp();
        if ($timestamp !== null) {
            $swiftMessage->setDate(DateTime::fromTimestamp($timestamp)->toNative());
        }
    }

    /**
     * Sets the max line length
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setMaxLineLength(Message $message, Swift_Message $swiftMessage)
    {
        $maxLineLength = $message->getMaxLineLength();
        if ($maxLineLength !== null) {
            $swiftMessage->setMaxLineLength($maxLineLength);
        }
    }

    /**
     * Sets the attachments
     *
     * @param Message       $message      The mail message
     * @param Swift_Message $swiftMessage The Swift Mailer message
     *
     * @return void
     */
    protected function setAttachments(Message $message, Swift_Message $swiftMessage)
    {
        /** @var AttachmentInterface $attachment */
        foreach ($message->getAttachments() as $attachment) {
            $swiftAttachment = new Swift_Attachment(
                $attachment->getBody(),
                $attachment->getFileName(),
                $attachment->getContentType()
            );
            $swiftAttachment->setId($attachment->getId());
            $swiftAttachment->setDisposition($attachment->getDisposition());
            $swiftMessage->attach($swiftAttachment);
        }
    }
}
