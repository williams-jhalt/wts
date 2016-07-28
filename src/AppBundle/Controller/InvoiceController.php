<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/weborders/invoice")
 * @Security("has_role('ROLE_CUSTOMER') or has_role('ROLE_ADMIN')")
 */
class InvoiceController extends Controller {

    /**
     * @Route("/", name="invoice_index")
     */
    public function indexAction() {
        return $this->render('invoice/index.html.twig');
    }

    /**
     * @Route("/list", name="invoice_list", options={"expose":true})
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

        $repo = $this->getDoctrine()->getRepository('AppBundle:Invoice');

        if ($authorizationChecker->isGranted('ROLE_ADMIN')) {
            $recordsTotal = $this->getDoctrine()
                    ->getManager()
                    ->createQuery('SELECT COUNT(p) FROM AppBundle:Invoice p')
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
        
        if ($show == 'consolidated') {
            $qb->andWhere('p.consolidated = true');
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
                'orderNumber' => $item->getOrderNumber(),
                'orderDate' => $salesOrder->getOrderDate(),
                'grossAmount' => $item->getGrossAmount(),
                'freightCharge' => $item->getFreightCharge(),
                'shippingAndHandlingCharge' => $item->getShippingAndHandlingCharge(),
                'netAmount' => $item->getNetAmount(),
                'customerNumber' => $item->getCustomerNumber()
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
     * @Route("/view/{id}", name="invoice_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $invoice = $this->getDoctrine()->getRepository('AppBundle:Invoice')->find($id);

        return $this->render('invoice/view.html.twig', array(
                    'item' => $invoice
        ));
    }

    /**
     * @Route("/viewpdf/{id}", name="invoice_viewpdf")
     */
    public function viewPdfAction($id = null) {

        $invoice = $this->getDoctrine()->getRepository('AppBundle:Invoice')->find($id);

        return $this->render('invoice/viewpdf.html.twig', array(
                    'item' => $invoice
        ));
    }

    /**
     * @Route("/pdf/{record}/{sequence}", name="invoice_viewpdf_file")
     */
    public function viewPdf($record = null, $sequence = 1) {
        
        $pdf = $this->get('erp_one_connector_service')->getPdf("invoice", $record, $sequence);

        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->setContent(base64_decode($pdf->document));

        return $response;
        
    }

}
