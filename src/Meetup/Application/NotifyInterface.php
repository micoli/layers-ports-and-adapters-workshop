<?php
declare(strict_types=1);

namespace Meetup\Application;

use Meetup\Domain\Model\MeetupScheduled;

interface NotifyInterface
{
    public function meetupScheduled(MeetupScheduled $event): void;
}
