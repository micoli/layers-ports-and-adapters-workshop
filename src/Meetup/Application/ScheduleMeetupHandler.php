<?php
declare(strict_types=1);

namespace Meetup\Application;

use Meetup\Domain\Model\Description;
use Meetup\Domain\Model\Meetup;
use Meetup\Domain\Model\MeetupId;
use Meetup\Domain\Repositories\MeetupRepositoryInterface;
use Meetup\Domain\Model\MeetupScheduled;
use Meetup\Domain\Model\Name;
use Meetup\Domain\Model\ScheduledDate;

final class ScheduleMeetupHandler
{
	/**
	 * @var MeetupRepositoryInterface
	 */
	private $meetupRepository;
	/**
	 * @var NotifyInterface
	 */
	private $notify;

	public function __construct(MeetupRepositoryInterface $meetupRepository, NotifyInterface $notify)
	{
		$this->meetupRepository = $meetupRepository;
		$this->notify = $notify;
	}

	public function handle(ScheduleMeetup $command): void
	{
		$meetup = Meetup::schedule(
			MeetupId::fromString($command->id),
			Name::fromString($command->name),
			Description::fromString($command->description),
			ScheduledDate::fromPhpDateString($command->scheduledFor)
		);

		$this->meetupRepository->add($meetup);

		/*$this->notify->meetupScheduled(
			new MeetupScheduled($meetup->meetupId())
		);*/
	}
}
