<?php

namespace App\Maker;

use Symfony\Bundle\MakerBundle\ConsoleStyle;
use Symfony\Bundle\MakerBundle\DependencyBuilder;
use Symfony\Bundle\MakerBundle\Generator;
use Symfony\Bundle\MakerBundle\InputConfiguration;
use Symfony\Bundle\MakerBundle\Maker\AbstractMaker;
use Symfony\Bundle\MakerBundle\Str;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Form\AbstractType;

/**
 * Class HandlerMaker
 * @package App\Maker
 */
class HandlerMaker extends AbstractMaker
{
    /**
     * @var array<string, string>
     */
    private array $formTypes;

    /**
     * HandlerMaker constructor.
     * @param array $formTypes
     */
    public function __construct(array $formTypes)
    {
        $this->formTypes = $formTypes;
    }

    /**
     * @inheritDoc
     */
    public static function getCommandName(): string
    {
        return "maker:handler";
    }

    /**
     * @inheritDoc
     */
    public function configureCommand(Command $command, InputConfiguration $inputConfig)
    {
        $command
            ->setDescription('Creates form handler')
            ->addArgument(
                'name',
                InputArgument::OPTIONAL,
                sprintf(
                    'The class name of the form handler (e.g. <fg=yellow>%s</>)',
                    'FooHandler'
                )
            )
            ->addArgument(
                'form-type-class',
                InputArgument::OPTIONAL,
                sprintf(
                    'The class name of the form type to create form handler (e.g. <fg=yellow>%s</>)',
                    'FooType'
                )
            )
        ;

        $inputConfig->setArgumentAsNonInteractive('form-type-class');
    }

    public function interact(InputInterface $input, ConsoleStyle $io, Command $command)
    {
        if (null === $input->getArgument('name')) {
            $argument = $command->getDefinition()->getArgument('name');

            $question = new Question($argument->getDescription());

            $value = $io->askQuestion($question);

            $input->setArgument('name', $value);
        }

        if (null === $input->getArgument('form-type-class')) {
            $argument = $command->getDefinition()->getArgument('form-type-class');

            $question = new Question($argument->getDescription());

            $question->setAutocompleterValues(array_keys($this->formTypes));

            $value = $io->askQuestion($question);

            $input->setArgument('form-type-class', $value);
        }
    }

    /**
     * @inheritDoc
     */
    public function configureDependencies(DependencyBuilder $dependencies)
    {
        $dependencies->addClassDependency(
            AbstractType::class,
            'form'
        );
    }

    /**
     * @inheritDoc
     */
    public function generate(InputInterface $input, ConsoleStyle $io, Generator $generator)
    {
        $handlerDetails = $generator->createClassNameDetails(
            $input->getArgument("name"),
            'Handler\\',
            'Handler'
        );

        $formType = $this->formTypes[$input->getArgument("form-type-class")];

        $generator->generateClass(
            $handlerDetails->getFullName(),
            __DIR__ . '/../Resources/skeleton/handler.tpl.php',
            [
                'form_type_full_class_name' => $formType,
                'form_class_name' => $input->getArgument("form-type-class")
            ]
        );

        $generator->writeChanges();

        $io->success("Handler created !");
    }
}
