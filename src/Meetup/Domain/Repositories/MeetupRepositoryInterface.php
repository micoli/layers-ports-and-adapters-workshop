<?php
declare(strict_types=1);

namespace Meetup\Domain\Repositories;

use Meetup\Domain\Model\Meetup;
use Meetup\Domain\Model\MeetupId;

interface MeetupRepositoryInterface
{
	public function add(Meetup $meetup): void;

	public function byId(MeetupId $meetupId): Meetup;

	/**
	 * @param \DateTimeImmutable $now
	 * @return Meetup[]
	 */
	public function upcomingMeetups(\DateTimeImmutable $now): array;

	/**
	 * @param \DateTimeImmutable $now
	 * @return Meetup[]
	 */
	public function pastMeetups(\DateTimeImmutable $now): array;

	/**
	 * @return Meetup[]
	 */
	public function allMeetups(): array;

	public function deleteAll(): void;

	public function nextIdentity(): MeetupId;
}
