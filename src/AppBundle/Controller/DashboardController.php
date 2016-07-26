<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/dashboard")
 * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
 */
class DashboardController extends Controller
{
    /**
     * @Route("/", name="dashboard_index")
     */
    public function indexAction()
    {
        
        $totalProductCount = $this->getDoctrine()->getManager()
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('AppBundle:Product', 'p')
                ->getQuery()
                ->getSingleScalarResult();
        
        $totalOpenOrderCount = $this->getDoctrine()->getManager()
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('AppBundle:SalesOrder', 'p')
                ->where('p.open = true')
                ->getQuery()
                ->getSingleScalarResult();
        
        $ordersShippedMissingTracking = $this->getDoctrine()->getManager()
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('AppBundle:Shipment', 'p')
                ->where('SIZE(p.packages) = 0')
                ->getQuery()
                ->getSingleScalarResult();
        
        $productsMissingAttachments = $this->getDoctrine()->getManager()
                ->createQueryBuilder()
                ->select('COUNT(p)')
                ->from('AppBundle:Product', 'p')
                ->where('SIZE(p.attachments) = 0')
                ->getQuery()
                ->getSingleScalarResult();
        
        return $this->render('dashboard/index.html.twig', array(
            'totalProductCount' => $totalProductCount,
            'totalOpenOrderCount' => $totalOpenOrderCount,
            'ordersShippedMissingTracking' => $ordersShippedMissingTracking,
            'productsMissingAttachments' => $productsMissingAttachments
        ));
    }
}
