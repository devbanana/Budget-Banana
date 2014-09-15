<?php

namespace Devbanana\PagesBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/tutorials")
 */
class TutorialsController extends Controller
{
    /**
     * @Route("/", name="tutorials_index")
     * @Template()
     */
    public function indexAction()
    {
        return array(
                // ...
            );    }

}
