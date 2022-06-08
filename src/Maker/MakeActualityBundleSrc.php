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
    const SKELETON_DIR = __DIR__ . '/../Resources/makerSkeleton';
    private string $projectDir;
    private ?ConsoleStyle $io = null;
    private array $filesCreatedSuccessfully = [];
    private array $filesAlreadyExist = [];

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
        $this->io = $io;

        $this->generateEntities();
        $this->generateRepositories();
        $this->generateTemplates();
        $this->generatePages();
        $this->generateJs();
        $this->generateCss();

        $this->showLogs();
    }

    private function generateTemplates(): void
    {
        $templates = [
            [
                'targetPath' => 'templates/pages/actuality/actualities.html.twig',
                'templateName' => $this::SKELETON_DIR  . '/templates/actualities.html.twig'
            ],
            [
                'targetPath' => 'templates/pages/actuality/actuality.html.twig',
                'templateName' => $this::SKELETON_DIR  . '/templates/actuality.html.twig'
            ]
        ];

        $this->generateFiles($templates);
    }

    private function generateRepositories(): void
    {
        $repositories = [
            [
                'targetPath' => 'src/Repository/Actuality/ActualityRepository.php',
                'templateName' => $this::SKELETON_DIR  . '/repository/ActualityRepository.php'
            ],
            [
                'targetPath' => 'src/Repository/Actuality/CategoryRepository.php',
                'templateName' => $this::SKELETON_DIR  . '/repository/CategoryRepository.php'
            ],
            [
                'targetPath' => 'src/Repository/Actuality/ActualityMediaRepository.php',
                'templateName' => $this::SKELETON_DIR  . '/repository/ActualityMediaRepository.php'
            ],
            [
                'targetPath' => 'src/Repository/Actuality/CategoryTranslationRepository.php',
                'templateName' => $this::SKELETON_DIR  . '/repository/CategoryTranslationRepository.php'
            ],
            [
                'targetPath' => 'src/Repository/Actuality/ActualityTranslationRepository.php',
                'templateName' => $this::SKELETON_DIR  . '/repository/ActualityTranslationRepository.php'
            ],
        ];

        $this->generateFiles($repositories);
    }

    private function generateEntities(): void
    {
        $entities = [
            [
                'targetPath' => 'src/Entity/Actuality/Actuality.php',
                'templateName' => $this::SKELETON_DIR  . '/entity/Actuality.php'
            ],
            [
                'targetPath' => 'src/Entity/Actuality/Category.php',
                'templateName' => $this::SKELETON_DIR  . '/entity/Category.php'
            ],
            [
                'targetPath' => 'src/Entity/Actuality/ActualityMedia.php',
                'templateName' => $this::SKELETON_DIR  . '/entity/ActualityMedia.php'
            ],
            [
                'targetPath' => 'src/Entity/Actuality/CategoryTranslation.php',
                'templateName' => $this::SKELETON_DIR  . '/entity/CategoryTranslation.php'
            ],
            [
                'targetPath' => 'src/Entity/Actuality/ActualityTranslation.php',
                'templateName' => $this::SKELETON_DIR  . '/entity/ActualityTranslation.php'
            ],
        ];

        $this->generateFiles($entities);
    }

    private function generatePages(): void
    {
        $pages = [
            [
                'targetPath' => 'src/CMS/Page/Actuality/ActualitiesPage.php',
                'templateName' => $this::SKELETON_DIR  . '/pages/ActualitiesPage.php'
            ],
            [
                'targetPath' => 'src/CMS/Page/Actuality/ActualityPage.php',
                'templateName' => $this::SKELETON_DIR  . '/pages/ActualityPage.php'
            ]
        ];

        $this->generateFiles($pages);
    }

    private function generateJs(): void
    {
        $js = [
            [
                'targetPath' => 'assets/admin/controllers/actuality-media-collection_controller.js',
                'templateName' => $this::SKELETON_DIR  . '/js/actuality-media-collection_controller.js'
            ]
        ];

        $this->generateFiles($js);
    }

    private function generateCss(): void
    {
        $css = [
            [
                'targetPath' => 'assets/admin/css/components/_actuality_media_collection.scss',
                'templateName' => $this::SKELETON_DIR  . '/css/_actuality_media_collection.scss'
            ]
        ];

        $this->generateFiles($css);
    }

    /*
     * $files = [['targetPath' => '', 'templateName' => ''], ...]
     */
    private function generateFiles(array $files):void
    {

        foreach ($files as $file) {
            $destinationPath = $this->projectDir . '/' . $file['targetPath'];

            if (!file_exists($destinationPath)) {
                $path = pathinfo($destinationPath);
                if (!is_dir($path['dirname'])){
                    mkdir($path['dirname'],0777, true);
                }
                copy($file['templateName'], $this->projectDir . '/' . $file['targetPath']);
                $this->filesCreatedSuccessfully[] = $file['targetPath'];
            }else{
                $this->filesAlreadyExist[] = $file['targetPath'];
            }
        }
    }

    private function showLogs():void
    {
        foreach ($this->filesAlreadyExist as $file) {
            $this->io->warning($file . ' already exist');
        }

        foreach ($this->filesCreatedSuccessfully as $file) {
            $this->io->success($file . ' has been created');
        }
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