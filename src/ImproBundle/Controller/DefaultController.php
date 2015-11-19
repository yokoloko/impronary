<?php

namespace ImproBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     * @Template()
     */
    public function indexAction()
    {
        $words = $this->getDoctrine()->getEntityManager()->getRepository('ImproBundle:Word')->findRandomActive();

        return $this->render('ImproBundle:Default:index.html.twig', array('words' => $words));
    }
}
