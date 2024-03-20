<?php

namespace ContainerKl810gv;


use Symfony\Component\DependencyInjection\Argument\RewindableGenerator;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;

/**
 * @internal This class has been auto-generated by the Symfony Dependency Injection Component.
 */
class getImportControllerService extends App_KernelDevDebugContainer
{
    /**
     * Gets the public 'App\Controller\ImportController' shared autowired service.
     *
     * @return \App\Controller\ImportController
     */
    public static function do($container, $lazyLoad = true)
    {
        include_once \dirname(__DIR__, 4).'/vendor/symfony/framework-bundle/Controller/AbstractController.php';
        include_once \dirname(__DIR__, 4).'/src/Controller/ImportController.php';
        include_once \dirname(__DIR__, 4).'/src/Service/FileProcessorService.php';
        include_once \dirname(__DIR__, 4).'/src/Repository/ExchangeRepository.php';
        include_once \dirname(__DIR__, 4).'/src/Service/SplFileInfoWrapper.php';
        include_once \dirname(__DIR__, 4).'/src/Validator/csvValidator.php';
        include_once \dirname(__DIR__, 4).'/src/Validator/AttributesValidator.php';

        $container->services['App\\Controller\\ImportController'] = $instance = new \App\Controller\ImportController(new \App\Service\FileProcessorService(($container->services['.container.private.session'] ?? $container->load('get_Container_Private_SessionService')), new \App\Repository\ExchangeRepository(new \App\Service\SplFileInfoWrapper('var/import.csv'), new \App\Validator\csvValidator(), new \App\Validator\AttributesValidator())));

        $instance->setContainer(($container->privates['.service_locator.mx0UMmY'] ?? $container->load('get_ServiceLocator_Mx0UMmYService'))->withContext('App\\Controller\\ImportController', $container));

        return $instance;
    }
}