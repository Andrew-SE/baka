<?php


class TimetableEventBatchPostFields
{
    /**
     * @var string $showAs zobrazení v kalendáři free nebo busy
     */
    public $subject;
    public EventBody $body;
    public EventTime $start;
    public EventTime $end;
    public string $showAs;
    public ?Recurrence $recurrence = null;
    public bool $isReminderOn;
    public int $reminderMinutesBeforeStart;
    public array $categories;


    public function __construct(
        string $title,
        string $body,
        string $startDateTime,
        string $endDateTime,
        string $showAs,
        string $category,
        bool $isReminderOn,
        int $reminderMinutesBeforeStart,
        bool $permanent
    )
    {
        $this->subject = $title;

        $content = new EventBody($body);
        $this->body = $content;

        $start = new EventTime($startDateTime);
        $this->start = $start;

        $end = new EventTime($endDateTime);
        $this->end = $end;

        $this->showAs = $showAs;
        $this->isReminderOn = $isReminderOn;
        $this->reminderMinutesBeforeStart = $reminderMinutesBeforeStart;
        $this->categories = array($category);

        if ($permanent){
            $this->recurrence = new Recurrence(date("Y-m-d", strtotime("+7 day",strtotime($startDateTime))),date("l", strtotime($startDateTime)));
        }
    }

}

class EventBody{
    public string $contentType = "HTML";
    public string $content;

    public function __construct(string $content)
    {
        $this->content = $content;
    }
}

class EventTime{

    public string $dateTime;
    public string $timeZone = "Central Europe Standard Time";

    public function __construct($dateTime)
    {
        $this->dateTime = $dateTime;
    }
}

class Recurrence{
    public Pattern $pattern;
    public Range $range;

    public function __construct(string $startDate, string $daysOfWeek)
    {
        $this->range= new Range($startDate);
        $this->pattern = new Pattern($daysOfWeek);
    }
}

class Pattern{
    public string $type = "weekly";
    public int $interval = 1;
    public array $daysOfWeek;

    public function __construct(string $daysOfWeek)
    {
        $this->daysOfWeek=array($daysOfWeek);
    }
}
class Range{
    public string $type = "numbered";
    //"2017-09-04"
    public string $startDate;
    public int $numberOfOccurrences = 4;

    public function __construct(string $startDate)
    {
        $this->startDate= $startDate;
    }
}

