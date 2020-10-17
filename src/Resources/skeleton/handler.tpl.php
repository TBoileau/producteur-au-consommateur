<?= "<?php\n" ?>

namespace <?= $namespace ?>;

use <?= $form_type_full_class_name ?>;
use App\HandlerFactory\AbstractHandler;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class <?= $class_name ?>

 * @package <?= $namespace ?>

 */
class <?= $class_name ?> extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    protected function process($data, array $options): void
    {
    }

    /**
     * @inheritDoc
     */
    protected function configure(OptionsResolver $resolver): void
    {
        $resolver->setDefault("form_type", <?= $form_class_name ?>::class);
    }
}
