<?php

namespace Devbanana\BudgetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('DevbananaBudgetBundle:Default:index.html.twig');
    }
}
