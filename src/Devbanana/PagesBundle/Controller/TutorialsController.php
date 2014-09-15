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

        /**
         * @Route("/chapter-1", name="tutorials_1")
         * @Template()
         */
        public function chapter1Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-1/section-1", name="tutorials_1_1")
         * @Template()
         */
        public function chapter1Section1Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-1/section-2", name="tutorials_1_2")
         * @Template()
         */
        public function chapter1Section2Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-1/section-3", name="tutorials_1_3")
         * @Template()
         */
        public function chapter1Section3Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-1/section-4", name="tutorials_1_4")
         * @Template()
         */
        public function chapter1Section4Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-2", name="tutorials_2")
         * @Template()
         */
        public function chapter2Action()
        {
            return array();
        }

        /**
         * @Route("/chapter-2/section-1", name="tutorials_2_1")
         * @Template()
         */
        public function chapter2Section1Action()
        {
            return array();
        }

}
