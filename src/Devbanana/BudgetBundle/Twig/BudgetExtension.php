<?php

namespace Devbanana\BudgetBundle\Twig;

use JMS\DiExtraBundle\Annotation as DI;

/**
 * @DI\Service
 * @DI\Tag("twig.extension")
 */
class BudgetExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
                new \Twig_SimpleFilter('money', array($this, 'moneyFilter')),
                );
    }

    public function moneyFilter($number)
    {
        if (bccomp($number, '0.00', 2) < 0) {
            $number = "-$" . number_format($number, 2);
        }
        else {
            $number = "$" . number_format($number, 2);
        }

        return $number;
    }

    public function getName()
    {
        return 'budget_extension';
    }
}
