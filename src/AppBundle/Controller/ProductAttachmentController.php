<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/catalog/product-attachment")
 * @Security("has_role('ROLE_USER')")
 */
class ProductAttachmentController extends Controller
{
    /**
     * @Route("/", name="product_attachment_index")
     */
    public function indexAction()
    {
        return $this->render('productAttachment/index.html.twig');
    }
    
    /**
     * @Route("/list", name="product_attachment_list", options={"expose":true})
     */
    public function listAction(Request $request) {
        
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');
        
        $recordsTotal = $this->getDoctrine()->getManager()->createQuery('SELECT COUNT(p) FROM AppBundle:ProductAttachment p')->getSingleScalarResult();
        
        $repo = $this->getDoctrine()->getRepository('AppBundle:ProductAttachment');
        
        $qb = $repo->createQueryBuilder('p')->setFirstResult($start)->setMaxResults($length);
        
        if (!empty($search['value'])) {
            $qb->andWhere('p.url LIKE :searchTerms')->setParameter('searchTerms', "%{$search['value']}%");
        }
        
        foreach ($order as $o) {
            $qb->orderBy($columns[$o['column']]['name'], $o['dir']);
        }
        
        $items = $qb->getQuery()->getResult();

        $recordsFiltered = $qb->select('COUNT(p)')->setFirstResult(0)->setMaxResults(1)->getQuery()->getSingleScalarResult();
        
        $pageItems = array();
        
        foreach ($items as $item) {
            
            $pageItems[] = array(
                'id' => $item->getId(),
                'url' => $item->getUrl(),
                'explicit' => $item->getExplicit()
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
     * @Route("/view/{id}", name="product_attachment_view", options={"expose":true})
     */
    public function viewAction($id = null) {
        
        $item = $this->getDoctrine()->getRepository('AppBundle:ProductAttachment')->find($id);
        
        return $this->render('productAttachment/view.html.twig', array(
            'item' => $item
        ));
        
    }
    
    /**
     * @Route("/edit/{id}", name="product_attachment_edit", options={"expose":true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id, Request $request) {
        
        $item = $this->getDoctrine()->getRepository('AppBundle:ProductAttachment')->find($id);
        
        $form = $this->createFormBuilder($item)
                ->add('name', TextType::class)
                ->getForm();
        
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($item);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('product_attachment_view', array('id' => $id));
        }
        
        return $this->render('productAttachment/edit.html.twig', array(
            'item' => $item,
            'form' => $form->createView()
        ));
        
    }
}
