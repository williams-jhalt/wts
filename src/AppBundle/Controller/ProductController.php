<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/catalog/product")
 * @Security("has_role('ROLE_USER')")
 */
class ProductController extends Controller {

    /**
     * @Route("/", name="product_index")
     */
    public function indexAction() {
        return $this->render('product/index.html.twig');
    }

    /**
     * @Route("/list", name="product_list", options={"expose":true})
     */
    public function listAction(Request $request) {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $recordsTotal = $this->getDoctrine()->getManager()->createQuery('SELECT COUNT(p) FROM AppBundle:Product p')->getSingleScalarResult();

        $repo = $this->getDoctrine()->getRepository('AppBundle:Product');

        $qb = $repo->createQueryBuilder('p')->setFirstResult($start)->setMaxResults($length);

        if (!empty($search['value'])) {
            $qb->andWhere('p.keywords LIKE :searchTerms')->setParameter('searchTerms', "%{$search['value']}%");
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
     * @Route("/view/{id}", name="product_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $item = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);

        return $this->render('product/view.html.twig', array(
                    'item' => $item
        ));
    }
    
    /**
     * @Route("/edit/{id}/addAttachment", name="product_add_attachent", options={"expose":true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addAttachmentAction($id, Request $request) {
        
    }

    /**
     * @Route("/edit/{id}", name="product_edit", options={"expose":true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:Product')->find($id);

        $form = $this->createForm(ProductType::class, $item);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('product_view', array('id' => $id));
        }

        return $this->render('product/edit.html.twig', array(
                    'item' => $item,
                    'form' => $form->createView()
        ));
    }
    
    /**
     * @Route("/set-primary-attachment/{productId}", name="product_set_primary_attachment", options={"expose": true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function setPrimaryAttachmentAction($productId, Request $request) {
        
        $attachmentId = $request->get('attachmentId');
        
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($productId);
        $attachment = $this->getDoctrine()->getRepository('AppBundle:ProductAttachment')->find($attachmentId);
        
        $attachment->setPrimary(true);
        
        $this->getDoctrine()->getManager()->persist($attachment);
        $this->getDoctrine()->getManager()->flush();
        
        
        return new Response(json_encode(array('success' => true)), 200, array('Content-Type: application/json'));
        
    }
    
    /**
     * @Route("/add-category/{productId}", name="product_add_category", options={"expose": true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function addCategoryAction($productId, Request $request) {
        
        $categoryId = $request->get('categoryId');
        
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($productId);
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($categoryId);
        
        $product->getCategories()->add($category);
        
        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();
        
        
        return new Response(json_encode(array('success' => true)), 200, array('Content-Type: application/json'));
        
    }
    
    /**
     * @Route("/remove-category/{productId}", name="product_remove_category", options={"expose": true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removeCategoryAction($productId, Request $request) {
        
        $categoryId = $request->get('categoryId');
        
        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($productId);
        $category = $this->getDoctrine()->getRepository('AppBundle:Category')->find($categoryId);
        
        $product->getCategories()->removeElement($category);
        
        $this->getDoctrine()->getManager()->persist($product);
        $this->getDoctrine()->getManager()->flush();
        
        return new Response(json_encode(array('success' => true)), 200, array('Content-Type: application/json'));
        
    }

    /**
     * @Route("/category-tree/{productId}/{parentId}", name="product_category_tree", options={"expose": true})
     */
    public function categoryTreeAction($productId, $parentId = '#') {

        $product = $this->getDoctrine()->getRepository('AppBundle:Product')->find($productId);

        $data = array();

        if ($parentId == '#') {

            $data[] = array(
                'id' => '0',
                'text' => 'All Categories',
                'children' => true,
                'state' => array(
                    'opened' => true
                )
            );
        } else {

            $qb = $this->getDoctrine()->getRepository("AppBundle:Category")->createQueryBuilder('c');

            if ($parentId !== '0') {
                $parent = $this->getDoctrine()->getRepository("AppBundle:Category")->find($parentId);
                $qb->andWhere('c.parent = :parent')->setParameter('parent', $parent);
            } else {
                $qb->andWhere('c.parent is null');
            }

            $categories = $qb->getQuery()->getResult();

            foreach ($categories as $category) {
                $selected = $product->getCategories()->contains($category);
                $data[] = array(
                    'id' => $category->getId(),
                    'text' => $category->getName(),
                    'children' => sizeof($category->getChildren()) > 0 ? true : false,
                    'a_attr' => array(
                        'class' => 'ajax-link',
                        'href' => $this->generateUrl('category_view', array('id' => $category->getId())),
                        'data-target' => '#categoryDetail'
                    ), 
                    'state' => array(
                        'checked' => $selected,
                        'opened' => $selected
                    )
                );
            }
        }

        $response = new Response();
        $response->setContent(json_encode($data));
        $response->headers->set('Content-Type', 'application/json');


        return $response;
    }

}
