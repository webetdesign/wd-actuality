<?php

namespace WebEtDesign\ActualityBundle\Maker;

use Doctrine\Common\Annotations\Annotation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\MakerInterface;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Bundle\MakerBundle\Util\PhpCompatUtil;
use Symfony\Bundle\MakerBundle\Util\UseStatementGenerator;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Routing\Annotation\Route;

class MakeActualityBundleSrc implements MakerInterface
{
    private string $projectDir;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->projectDir = $parameterBag->get('kernel.project_dir') . '';
    }

    public static function getCommandName(): string
    {
        return 'make:actuality-bundle:src';
    }

    public static function getCommandDescription(): string
    {
        return 'Creates Entities, Repositories, Page, Template, Css and Js required for the actuality-bundle';
    }

    public function configureCommand(Command $command, InputConfiguration $inputConfig): void
    {
    }

    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator): void
    {
        // Css
        // JS
        // ENTITY
        // REPOSITORY
        // PAGE
        // TRMPLATE


        $files = [];

        $actualityTemplate = 'actuality.html.twig';
        $actualitiesTemplate = 'actualities.html.twig';

        $actualityPage = 'ActualityPage.php';
        $actualitiesPage = 'ActualitiesPage.php';

        $mediaActualityCollectionCss = '_actuality_media_collection.scss';
        $mediaActualityCollectionStimulusController = 'actuality-media-collection_controller.js';

        $actualityEntity = 'Actuality.php';
        $categoryEntity = 'Category.php';
        $actualityMediaEntity = 'ActualityMedia.php';

        $actualityRepository = 'ActualityRepository.php';
        $categoryRepository = 'CategoryRepository.php';
        $actualityMediaRepository = 'ActualityMedia.php';


//        $generator->generateFile();

//
//        $generator->writeChanges();

        $io->success('gfeôc fe');
        $io->text('Next: Open your new controller class and add some pages!');
    }

    private function generateTemplates(): void
    {
        $generator->generateTemplate(
            $templateName,
            'controller/twig_template.tpl.php',
            [
                'controller_path' => $controllerPath,
                'root_directory' => $generator->getRootDirectory(),
                'class_name' => $controllerClassNameDetails->getShortName(),
            ]
        );
    }

    private function isTwigInstalled(): bool
    {
        return class_exists(TwigBundle::class);
    }

    public function configureDependencies(DependencyBuilder $dependencies)
    {
        // TODO: Implement configureDependencies() method.
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        // TODO: Implement interact() method.
    }
}