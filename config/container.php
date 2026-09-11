<?php

use App\Validation\SalleValidator;
use App\Validation\ReservationValidator;

use App\Repository\SalleRepositoryInterface;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\EloquentReservationRepository;

use App\Service\CreerReservationService;
use App\Service\AnnulerReservationService;

use Illuminate\Database\Capsule\Manager as Capsule;
use FastRoute\Dispatcher;

use function DI\autowire;
use function DI\factory;
use App\Application;

use App\Service\SalleService;
use App\Service\ReservationQueryService;
use App\View\View;

return [

   

    SalleValidator::class =>
        autowire(SalleValidator::class),

    ReservationValidator::class =>
        autowire(ReservationValidator::class),



    SalleRepositoryInterface::class =>
        autowire(EloquentSalleRepository::class),

    ReservationRepositoryInterface::class =>
        autowire(EloquentReservationRepository::class),


   

    CreerReservationService::class =>
        autowire(CreerReservationService::class),

    AnnulerReservationService::class =>
        autowire(AnnulerReservationService::class),


    

    Capsule::class => factory(
        function (): Capsule {
            return require dirname(__DIR__) . '/config/database.php';
        }
    ),

     

    Application::class =>
        autowire(Application::class),

  

    Dispatcher::class => factory(
        function (): Dispatcher {
            return \FastRoute\simpleDispatcher(
                require dirname(__DIR__) . '/routes/web.php'
            );
        }
    ),

        SalleService::class =>
        autowire(SalleService::class),

    ReservationQueryService::class =>
        autowire(ReservationQueryService::class),

    View::class =>
        autowire(View::class),
];
