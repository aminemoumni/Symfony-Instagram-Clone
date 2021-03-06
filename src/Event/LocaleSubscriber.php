<?php

namespace App\Event;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;



class LocaleSubscriber implements EventSubscriberInterface
{
    public function __construct($defaultLocale = 'en')
    {
        $this->defaultLocale = $defaultLocale;
    }
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                [
                    'onKernelRequest',
                    20
                ]
            ]
        ];
    }
    public function onKernelrequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if(!$request->hasPreviousSession()){
            return;
        }
        if($locale = $request->attributes->get('_locale')){
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }
}