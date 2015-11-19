<?php

namespace ImproBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BlacklistController extends Controller
{
    /**
     * @Route("/blacklist")
     * @Template()
     */
    public function indexAction()
    {
        $words = $this->getDoctrine()->getEntityManager()->getRepository('ImproBundle:Blacklist')->findAll();

        return $this->render('ImproBundle:Blacklist:index.html.twig', array('words' => $words));
    }
}
