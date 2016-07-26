<?php

namespace AppBundle\Controller;

use DateInterval;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/weborders/salesOrder")
 * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
 */
class SalesOrderController extends Controller {

    /**
     * @Route("/", name="salesorder_index")
     */
    public function indexAction() {
        return $this->render('salesOrder/index.html.twig');
    }

    /**
     * @Route("/list", name="salesorder_list", options={"expose":true})
     */
    public function listAction(Request $request) {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');
        $show = $request->get('show', 'all');

        $authorizationChecker = $this->get('security.authorization_checker');

        $repo = $this->getDoctrine()->getRepository('AppBundle:SalesOrder');

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $recordsTotal = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT COUNT(p) FROM AppBundle:SalesOrder p')
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

        if ($show == 'closed') {
            $qb->andWhere('p.open = false');
        }

        if ($show == 'open') {
            $qb->andWhere('p.open = true');
        }

        if ($show == 'credit') {
            $qb->andWhere('p.credit = true');
        } else {
            $qb->andWhere('p.credit = false');
        }

        foreach ($order as $o) {
            $qb->orderBy($columns[$o['column']]['name'], $o['dir']);
        }

        $items = $qb->getQuery()->getArrayResult();

        $recordsFiltered = $qb->select('COUNT(p)')->setFirstResult(0)->setMaxResults(1)->getQuery()->getSingleScalarResult();

        $data = array(
            'draw' => $draw,
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $items
        );

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($data));

        return $response;
    }

    /**
     * @Route("/view/{id}", name="salesorder_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $salesOrder = $this->getDoctrine()->getRepository('AppBundle:SalesOrder')->find($id);

        $timeAgo = new DateTime('now -15 minutes');

        if ($salesOrder->getUpdatedOn() < $timeAgo) {
            $service = $this->get('erp_one_order_service');
            $salesOrder = $service->updateSalesOrder($salesOrder);
        }
        
        $invoices = $this->getDoctrine()->getRepository('AppBundle:Invoice')->findByOrderNumber($salesOrder->getOrderNumber());
        $shipments = $this->getDoctrine()->getRepository('AppBundle:Shipment')->findByOrderNumber($salesOrder->getOrderNumber());

        return $this->render('salesOrder/view.html.twig', array(
                    'item' => $salesOrder,
                    'invoices' => $invoices,
                    'shipments' => $shipments
        ));
    }

}
