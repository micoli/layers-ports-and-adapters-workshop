<?php
declare(strict_types = 1);

namespace Meetup\Infrastructure\UserInterface\Web\Controller;

use Meetup\Domain\Repositories\MeetupRepositoryInterface;
use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
final class ListMeetupsController extends Controller
{
	/**
	 * @var MeetupRepositoryInterface
	 */
	private $meetupRepository;

	/**
	 * @var Environment
	 */
	private $renderer;

	public function __construct(MeetupRepositoryInterface $meetupRepository, Environment $twig)
	{
		$this->meetupRepository = $meetupRepository;
		$this->renderer = $twig;
	}

	public function index(Request $request, callable $out = null): Response
	{
		$now = new \DateTimeImmutable();
		$upcomingMeetups = $this->meetupRepository->upcomingMeetups($now);
		$pastMeetups = $this->meetupRepository->pastMeetups($now);

		return new Response($this->renderer->render('list-meetups.html.twig', [
			'upcomingMeetups' => $upcomingMeetups,
			'pastMeetups' => $pastMeetups
		]));
	}
}
