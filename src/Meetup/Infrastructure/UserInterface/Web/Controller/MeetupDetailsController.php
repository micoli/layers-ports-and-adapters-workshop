<?php
declare(strict_types = 1);

namespace Meetup\Infrastructure\UserInterface\Web\Controller;

use Meetup\Domain\Model\MeetupId;
use Meetup\Domain\Repositories\MeetupRepositoryInterface;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class MeetupDetailsController
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

	public function index(Request $request): Response
	{
		$meetup = $this->meetupRepository->byId(MeetupId::fromString($request->get('id')));

		return new Response($this->renderer->render('meetup-details.html.twig', [
			'meetup' => $meetup
		]));
	}
}
