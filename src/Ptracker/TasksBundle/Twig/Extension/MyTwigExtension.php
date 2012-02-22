<?php

namespace Ptracker\TasksBundle\Twig\Extension;

class MyTwigExtension extends \Twig_Extension {

    public function getFilters() {
        return array(
            'var_dump' => new \Twig_Filter_Function('var_dump'),
            'linkable' => new \Twig_Filter_Method($this, 'linkable'),
        );
    }

    public function linkable($sentence) {
        global $router;
        return preg_replace("/#(\d+)/", "<a href=\"" . $router->generate('tasks_view') . "/\\1\">#\\1</a>", $sentence);
    }

    public function getName() {
        return 'my_twig_extension';
    }

}

?>
