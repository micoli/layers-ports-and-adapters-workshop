<?php
declare(strict_types=1);

namespace Meetup\Infrastructure\Notifications;

use Meetup\Application\NotifyInterface;
use Meetup\Domain\Model\MeetupScheduled;

final class NotifyMany implements NotifyInterface
{
	/**
	 * @var array|NotifyInterface[]
	 */
	private $notifiers;

	public function __construct(array $notifiers)
	{
		$this->notifiers = $notifiers;
	}

	public function meetupScheduled(MeetupScheduled $event): void
	{
		foreach ($this->notifiers as $notify) {
			$notify->meetupScheduled($event);
		}
	}
}
