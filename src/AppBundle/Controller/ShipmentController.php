<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/weborders/shipment")
 * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
 */
class ShipmentController extends Controller {

    /**
     * @Route("/", name="shipment_index")
     */
    public function indexAction() {
        return $this->render('shipment/index.html.twig');
    }

    /**
     * @Route("/list", name="shipment_list", options={"expose":true})
     */
    public function listAction(Request $request) {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');
        $show = $request->get('show', 'completed');

        $authorizationChecker = $this->get('security.authorization_checker');

        $repo = $this->getDoctrine()->getRepository('AppBundle:Shipment');

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $recordsTotal = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT COUNT(p) FROM AppBundle:Shipment p')
                    ->getSingleScalarResult();
        } elseif ($authorizationChecker->isGranted('ROLE_CUSTOMER')) {
            $recordsTotal = $repo->createQueryBuilder('p')
                    ->select('COUNT(p)')
                    ->where('p.customerNumber IN (:customerNumbers)')
                    ->setParameter('customerNumbers', $this->getUser()->getCustomerNumbers())
                    ->setFirstResult(0)
                    ->setMaxResults(1)
                    ->getQuery()
                    ->getSingleScalarResult();
        }

        $qb = $repo->createQueryBuilder('p')->setFirstResult($start)->setMaxResults($length);

        if ($authorizationChecker->isGranted('ROLE_CUSTOMER')) {
            $qb->where('p.customerNumber IN (:customerNumbers)')
                    ->setParameter('customerNumbers', $this->getUser()->getCustomerNumbers());
        }

        if (!empty($search['value'])) {
            $qb->andWhere('p.keywords LIKE :searchTerms')->setParameter('searchTerms', "%{$search['value']}%");
        }
        
        if ($show == 'completed') {
            $qb->andWhere('SIZE(p.packages) > 0');
        } elseif ($show == 'pending') {
            $qb->andWhere('SIZE(p.packages) = 0');
        }

        foreach ($order as $o) {
            $qb->orderBy($columns[$o['column']]['name'], $o['dir']);
        }

        $items = $qb->getQuery()->getResult();

        $recordsFiltered = $qb->select('COUNT(p)')->setFirstResult(0)->setMaxResults(1)->getQuery()->getSingleScalarResult();
        
        $pageItems = array();
        
        foreach ($items as $item) {
            
            $salesOrder = $this->getDoctrine()->getRepository('AppBundle:SalesOrder')->findOneBy(array('orderNumber' => $item->getOrderNumber()));
            
            $pageItems[] = array(
                'id' => $item->getId(),
                'manifestId' => $item->getManifestID(),
                'packageCount' => $item->getPackages()->count(),
                'customerNumber' => $item->getCustomerNumber(),
                'orderDate' => $salesOrder->getOrderDate()
            );
            
        }

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $pageItems
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));

        return $response;
    }

    /**
     * @Route("/view/{id}", name="shipment_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $shipment = $this->getDoctrine()->getRepository('AppBundle:Shipment')->find($id);

        return $this->render('shipment/view.html.twig', array(
                    'item' => $shipment
        ));
    }

}
