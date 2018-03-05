<?php
declare(strict_types=1);

namespace Tests\Integration\MeetupManagement\Infrastructure\Persistence;

use Meetup\Domain\Repositories\MeetupRepositoryInterface;
use Meetup\Infrastructure\Persistence\Filesystem\FileBasedMeetupRepository;
use Meetup\Infrastructure\Persistence\InMemory\InMemoryMeetupRepository;
use Tests\Unit\Meetup\Domain\Model\Util\MeetupFactory;

final class MeetupRepositoryTest extends \PHPUnit_Framework_TestCase
{
	public function meetupRepositories()
	{
		return [
			'file-based meetup repository' => [
				new FileBasedMeetupRepository(
					tempnam(sys_get_temp_dir(), 'meetups')
				)
			],
			'in-memory meetup repository' => [
				new InMemoryMeetupRepository()
			]
		];
	}

	/**
	 * @test
	 * @dataProvider meetupRepositories
	 */
	public function it_persists_and_retrieves_meetups(MeetupRepositoryInterface $repository)
	{
		$originalMeetup = MeetupFactory::someMeetup();
		$repository->add($originalMeetup);

		$restoredMeetup = $repository->byId($originalMeetup->meetupId());

		$this->assertEquals($originalMeetup, $restoredMeetup);
	}

	/**
	 * @test
	 * @dataProvider meetupRepositories
	 */
	public function its_initial_state_is_valid(MeetupRepositoryInterface $repository)
	{
		$this->assertSame(
			[],
			$repository->upcomingMeetups(new \DateTimeImmutable())
		);
	}

	/**
	 * @test
	 * @dataProvider meetupRepositories
	 */
	public function it_lists_upcoming_meetups(MeetupRepositoryInterface $repository)
	{
		$now = new \DateTimeImmutable();
		$pastMeetup = MeetupFactory::pastMeetup();
		$repository->add($pastMeetup);
		$upcomingMeetup = MeetupFactory::upcomingMeetup();
		$repository->add($upcomingMeetup);

		$this->assertEquals(
			[
				$upcomingMeetup
			],
			$repository->upcomingMeetups($now)
		);
	}

	/**
	 * @test
	 * @dataProvider meetupRepositories
	 */
	public function it_lists_past_meetups(MeetupRepositoryInterface $repository)
	{
		$now = new \DateTimeImmutable();
		$pastMeetup = MeetupFactory::pastMeetup();
		$repository->add($pastMeetup);
		$upcomingMeetup = MeetupFactory::upcomingMeetup();
		$repository->add($upcomingMeetup);

		$this->assertEquals(
			[
				$pastMeetup
			],
			$repository->pastMeetups($now)
		);
	}
}
