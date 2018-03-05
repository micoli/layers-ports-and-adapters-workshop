<?php
declare(strict_types=1);

namespace Meetup\Infrastructure\Notifications\RabbitMQ;

use Bunny\Client;
use Meetup\Application\NotifyInterface;
use Meetup\Domain\Model\MeetupScheduled;
use NaiveSerializer\Serializer;

final class PublishNotificationsToRabbitMQ implements NotifyInterface
{
	public function meetupScheduled(MeetupScheduled $event): void
	{
		$connection = [
			'host' => 'localhost',
			'vhost' => '/',
			'user' => 'guest',
			'password' => 'guest'
		];

		$client = new Client($connection);
		$client->connect();

		$client->channel()->publish(
			Serializer::serialize($event)
		);
	}
}
