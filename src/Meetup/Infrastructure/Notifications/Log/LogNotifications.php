<?php
declare(strict_types=1);

namespace Meetup\Infrastructure\Notifications\Log;

use Meetup\Application\NotifyInterface;
use Meetup\Domain\Model\MeetupScheduled;
use NaiveSerializer\Serializer;

final class LogNotifications implements NotifyInterface
{
	public function meetupScheduled(MeetupScheduled $event): void
	{
		error_log('MeetupScheduled notification: ' . Serializer::serialize($event));
	}
}
