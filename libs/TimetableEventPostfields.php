<?php


class TimetableEventPostfields
{
    /**
     * @var string $showAs zobrazení v kalendáři free nebo busy
     */
    public string $subject;
    public EventBody $body;
    public EventTime $start;
    public EventTime $end;
    public string $showAs;
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
        int $reminderMinutesBeforeStart
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
