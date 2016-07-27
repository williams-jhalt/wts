<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller {

    /**
     * @Route("/", name="homepage")
     */
    public function indexAction() {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/image/{id}/{height}/{width}", name="show_image", options={"expose":true})
     */
    public function showImage($id, $height = 100, $width = 100) {

        $attachment = $this->getDoctrine()->getRepository('AppBundle:ProductAttachment')->find($id);

        return new Response(
                $this->get('image.handling')
                        ->open($attachment->getUrl())
                        ->resize($height, $width)
                        ->get('jpeg'), 200, array(
            'Content-Type' => 'image/jpeg',
            'Content-Disposition' => 'inline'
        ));
    }

}
