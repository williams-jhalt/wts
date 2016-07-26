<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/promotion")
 */
class PromotionController extends Controller {
    
    /**
     * @Route("/", name="promotion_index")
     */
    public function indexAction() {
        
        $items = $this->getDoctrine()->getRepository('AppBundle:Promotion')->findAll();
        
        return $this->render('promotion/index.html.twig', array(
            'items' => $items
        ));
        
    }
    
}