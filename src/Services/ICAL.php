<?php

namespace App\Services;

use Jsvrcek\ICS\CalendarExport;
use Jsvrcek\ICS\Model\CalendarEvent;
use Jsvrcek\ICS\Model\Relationship\Attendee;
use Jsvrcek\ICS\Utility\Formatter;
use Symfony\Component\HttpFoundation\Response;

class ICAL
{
    public function calendarAction(Formatter $formatter, CalendarExport $calendarExport)
    {
        $eventOne = new CalendarEvent();
        $eventOne->setStart(new \DateTime())
            ->setSummary('Family reunion')
            ->setUid('event-uid');
        $attendee = new Attendee($formatter); // or $formatter
        $attendee->setValue('moe@example.com')
            ->setName('Moe Smith');
        $eventOne->addAttendee($attendee);
        $response = new Response($calendarExport->getStream());
        $response->headers->set('Content-Type', 'text/calendar');
        return $response;
    }
}