<?php
namespace App\Event;


use Twig\Environment;
use App\Mailer\Mailer;
use App\Event\UserRegisterEvent;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class UserSubscriber implements EventSubscriberInterface
{
    /**
     * Undocumented function
     *
     * @var \Mailer 
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
        
    }
    public static function getSubscribedEvents()
    {
        return [
            UserRegisterEvent::NAME => 'onUserRegister'
        ];
    }
    public function onUserRegister(UserRegisterEvent $event)
    {
        $this->mailer->sendConfirmationEmail($event->getRegisteredUser());
    }
}