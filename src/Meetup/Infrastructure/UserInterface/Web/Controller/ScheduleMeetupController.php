<?php
declare(strict_types = 1);

namespace Meetup\Infrastructure\UserInterface\Web\Controller;

use Meetup\Application\ScheduleMeetup;
use Meetup\Application\ScheduleMeetupHandler;
use Meetup\Domain\Repositories\MeetupRepositoryInterface;
use Twig\Environment;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

final class ScheduleMeetupController extends Controller
{
	/**
	 * @var Environment
	 */
	private $renderer;

	/**
	 * @var ScheduleMeetupHandler
	 */
	private $handler;
	/**
	 * @var MeetupRepositoryInterface
	 */
	private $repository;

	public function __construct(Environment $twig, ScheduleMeetupHandler $handler, MeetupRepositoryInterface $repository)
	{
		$this->renderer = $twig;
		$this->handler = $handler;
		$this->repository = $repository;
	}

	public function index(Request $request): Response
	{
		$formErrors = [];
		$submittedData = [];

		if ($request->getMethod() === 'POST') {

			if (empty($request->get('name',''))) {
				$formErrors['name'][] = 'Provide a name';
			}
			if (empty($request->get('description',''))) {
				$formErrors['description'][] = 'Provide a description';
			}
			if (empty($request->get('scheduledFor',''))) {
				$formErrors['scheduledFor'][] = 'Provide a scheduled for date';
			}

			if (empty($formErrors)) {
				$command = new ScheduleMeetup();
				$command->id = (string)$this->repository->nextIdentity();
				$command->name = $request->get('name');
				$command->description = $request->get('description');
				$command->scheduledFor = $request->get('scheduledFor');

				$this->handler->handle($command);

				return new RedirectResponse(
					$this->generateUrl(
						'meetup_details',
						[
							'id' => $command->id
						]
					)
				);
			}
		}

		return new Response(
			$this->renderer->render(
				'schedule-meetup.html.twig',
				[
					'submittedData' => [
						'name'=> $request->get('name'),
						'description' => $request->get('description'),
						'scheduledFor'=> $request->get('scheduledFor')
					],
					'formErrors' => $formErrors
				]
			)
		);
	}
}
