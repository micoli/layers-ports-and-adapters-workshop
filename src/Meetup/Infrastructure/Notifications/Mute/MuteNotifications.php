<?php
declare(strict_types=1);

namespace Meetup\Infrastructure\Notifications\Mute;

use Meetup\Application\NotifyInterface;
use Meetup\Domain\Model\MeetupScheduled;

final class MuteNotifications implements NotifyInterface
{
	public function meetupScheduled(MeetupScheduled $event): void
	{
	}
}
