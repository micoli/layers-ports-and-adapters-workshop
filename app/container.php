<?php

use Interop\Container\ContainerInterface;
use Meetup\Application\Notify;
use Meetup\Application\ScheduleMeetupHandler;
use Meetup\Domain\Model\MeetupRepository;
use Meetup\Infrastructure\Notifications\Log\LogNotifications;
use Meetup\Infrastructure\Notifications\NotifyMany;
use Meetup\Infrastructure\Notifications\RabbitMQ\PublishNotificationsToRabbitMQ;
use Meetup\Infrastructure\UserInterface\Web\Controller\MeetupDetailsController;
use Meetup\Infrastructure\Persistence\Filesystem\FileBasedMeetupRepository;
use Meetup\Infrastructure\UserInterface\Web\Controller\ListMeetupsController;
use Meetup\Infrastructure\UserInterface\Web\Controller\ScheduleMeetupController;
use Meetup\Infrastructure\UserInterface\Web\Resources\Views\TwigTemplates;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Debug\Debug;
use Symfony\Component\Debug\ErrorHandler;
use Xtreamwayz\Pimple\Container;
use Zend\Expressive\Application;
use Zend\Expressive\Container\ApplicationFactory;
use Zend\Expressive\Helper\ServerUrlHelper;
use Zend\Expressive\Helper\UrlHelper;
use Zend\Expressive\Router\FastRouteRouter;
use Zend\Expressive\Router\RouterInterface;
use Zend\Expressive\Template\TemplateRendererInterface;
use Zend\Expressive\Twig\TwigRendererFactory;

Debug::enable();
ErrorHandler::register();

$container = new Container();

$container['config'] = [
    'debug' => true,
    'templates' => [
        'extension' => 'html.twig',
        'paths' => [
            TwigTemplates::getPath()
        ]
    ],
    'twig' => [
        'extensions' => [
        ]
    ],
    'routes' => [
        [
            'name' => 'list_meetups',
            'path' => '/',
            'middleware' => ListMeetupsController::class,
            'allowed_methods' => ['GET']
        ],
        [
            'name' => 'meetup_details',
            'path' => '/meetup/{id}',
            'middleware' => MeetupDetailsController::class,
            'allowed_methods' => ['GET']
        ],
        [
            'name' => 'schedule_meetup',
            'path' => '/schedule-meetup',
            'middleware' => ScheduleMeetupController::class,
            'allowed_methods' => ['GET', 'POST']
        ]
    ]
];

/*
 * Zend Expressive Application
 */
$container['Zend\Expressive\FinalHandler'] = function () {
    return function (RequestInterface $request, ResponseInterface $response, $err = null) {
        if ($err instanceof \Throwable) {
            throw $err;
        }
    };
};
$container[RouterInterface::class] = function () {
    return new FastRouteRouter();
};
$container[Application::class] = new ApplicationFactory();

/*
 * Templating
 */
$container[TemplateRendererInterface::class] = new TwigRendererFactory();
$container[ServerUrlHelper::class] = function () {
    return new ServerUrlHelper();
};
$container[UrlHelper::class] = function (ContainerInterface $container) {
    return new UrlHelper($container[RouterInterface::class]);
};

/*
 * Notifications
 */
$container[Notify::class] = function () {
    return new NotifyMany([
        new LogNotifications(),
        new PublishNotificationsToRabbitMQ()
    ]);
};

/*
 * Persistence
 */
$container[MeetupRepository::class] = function () {
    return new FileBasedMeetupRepository(__DIR__ . '/../var/meetups.txt');
};

/*
 * Use cases
 */
$container[ScheduleMeetupHandler::class] = function(ContainerInterface $container) {
    return new ScheduleMeetupHandler(
        $container[MeetupRepository::class],
        $container[Notify::class]
    );
};

/*
 * Controllers
 */
$container[ScheduleMeetupController::class] = function (ContainerInterface $container) {
    return new ScheduleMeetupController(
        $container->get(TemplateRendererInterface::class),
        $container->get(RouterInterface::class),
        $container->get(ScheduleMeetupHandler::class),
        $container->get(MeetupRepository::class)
    );
};
$container[ListMeetupsController::class] = function (ContainerInterface $container) {
    return new ListMeetupsController(
        $container->get(MeetupRepository::class),
        $container->get(TemplateRendererInterface::class)
    );
};
$container[MeetupDetailsController::class] = function (ContainerInterface $container) {
    return new MeetupDetailsController(
        $container->get(MeetupRepository::class),
        $container->get(TemplateRendererInterface::class)
    );
};

/*
 * CLI
 */
$container[\Meetup\Infrastructure\UserInterface\Cli\ScheduleMeetupConsoleHandler::class] = function (ContainerInterface $container) {
    return new \Meetup\Infrastructure\UserInterface\Cli\ScheduleMeetupConsoleHandler(
        $container->get(ScheduleMeetupHandler::class),
        $container->get(MeetupRepository::class)
    );
};

return $container;
