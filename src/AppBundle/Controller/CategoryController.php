<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\CategoryFormType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/catalog/category")
 * @Security("has_role('ROLE_USER')")
 */
class CategoryController extends Controller {

    /**
     * @Route("/", name="category_index")
     */
    public function indexAction() {
        return $this->render('category/index.html.twig');
    }

    /**
     * @Route("/list", name="category_list", options={"expose":true})
     */
    public function listAction(Request $request) {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $recordsTotal = $this->getDoctrine()->getManager()->createQuery('SELECT COUNT(p) FROM AppBundle:Category p')->getSingleScalarResult();

        $repo = $this->getDoctrine()->getRepository('AppBundle:Category');

        $qb = $repo->createQueryBuilder('p')->setFirstResult($start)->setMaxResults($length);

        if (!empty($search['value'])) {
            $qb->andWhere('p.name LIKE :searchTerms OR p.code LIKE :searchTerms')->setParameter('searchTerms', "%{$search['value']}%");
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
     * @Route("/view/{id}", name="category_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $item = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        return $this->render('category/view.html.twig', array(
                    'item' => $item
        ));
    }

    /**
     * @Route("/edit/{id}", name="category_edit", options={"expose":true})
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function editAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:Category')->find($id);

        $form = $this->createForm(CategoryFormType::class, $item);

        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();

            $em->persist($item);
            $em->flush();

            return $this->redirectToRoute('category_view', array('id' => $id));
        }

        return $this->render('category/edit.html.twig', array(
                    'item' => $item,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/tree/{id}", name="category_tree", options={"expose":true})
     */
    public function categoryTreeAction($id = '#') {

        $data = array();

        if ($id == '#') {

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

            if ($id !== '0') {
                $parent = $this->getDoctrine()->getRepository("AppBundle:Category")->find($id);
                $qb->andWhere('c.parent = :parent')->setParameter('parent', $parent);
            } else {
                $qb->andWhere('c.parent is null');
            }

            $categories = $qb->getQuery()->getResult();

            foreach ($categories as $category) {
                $data[] = array(
                    'id' => $category->getId(),
                    'text' => $category->getName(),
                    'children' => sizeof($category->getChildren()) > 0 ? true : false,
                    'a_attr' => array(
                        'class' => 'ajax-link',
                        'href' => $this->generateUrl('category_view', array('id' => $category->getId())),
                        'data-target' => '#categoryDetail'
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
