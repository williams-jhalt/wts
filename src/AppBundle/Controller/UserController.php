<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/admin/user")
 * @Security("has_role('ROLE_ADMIN')")
 */
class UserController extends Controller {

    /**
     * @Route("/", name="user_index")
     */
    public function indexAction() {
        return $this->render('user/index.html.twig');
    }

    /**
     * @Route("/list", name="user_list", options={"expose":true})
     */
    public function listAction(Request $request) {

        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 10);
        $search = $request->get('search');
        $order = $request->get('order');
        $columns = $request->get('columns');

        $recordsTotal = $this->getDoctrine()->getManager()->createQuery('SELECT COUNT(p) FROM AppBundle:User p')->getSingleScalarResult();

        $repo = $this->getDoctrine()->getRepository('AppBundle:User');

        $qb = $repo->createQueryBuilder('p')->setFirstResult($start)->setMaxResults($length);

        if (!empty($search['value'])) {
            $qb->andWhere('p.username LIKE :searchTerms OR p.email LIKE :searchTerms')->setParameter('searchTerms', "%{$search['value']}%");
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
     * @Route("/view/{id}", name="user_view", options={"expose":true})
     */
    public function viewAction($id = null) {

        $item = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        return $this->render('user/view.html.twig', array(
                    'item' => $item
        ));
    }

    /**
     * @Route("/change-password/{id}", name="user_change_password", options={"expose":true})
     */
    public function changePasswordAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $form = $this->createFormBuilder($item)
                ->add('plainPassword', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                ))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($item);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_edit', array('id' => $item->getId()));
        }

        return $this->render('user/change_password.html.twig', array(
                    'item' => $item,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/add-customer/{id}", name="user_add_customer", options={"expose":true})
     */
    public function addCustomerAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $customerNumber = $request->get('customerNumber');

        $item->addCustomerNumber($customerNumber);

        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_edit', array('id' => $id));
    }

    /**
     * @Route("/remove-customer/{id}", name="user_remove_customer", options={"expose":true})
     */
    public function removeCustomerAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $customerNumber = $request->get('customerNumber');

        $item->removeCustomerNumber($customerNumber);

        $this->getDoctrine()->getManager()->persist($item);
        $this->getDoctrine()->getManager()->flush();

        return $this->redirectToRoute('user_edit', array('id' => $id));
    }

    /**
     * @Route("/edit/{id}", name="user_edit", options={"expose":true})
     */
    public function editAction($id, Request $request) {

        $item = $this->getDoctrine()->getRepository('AppBundle:User')->find($id);

        $form = $this->createFormBuilder($item)
                ->add('email', EmailType::class)
                ->add('admin', CheckboxType::class, array(
                    'label' => 'Administrator',
                    'required' => false
                ))
                ->add('enabled', CheckboxType::class, array(
                    'label' => 'Enabled',
                    'required' => false
                ))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($item);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_view', array('id' => $id));
        }

        return $this->render('user/edit.html.twig', array(
                    'item' => $item,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/add", name="user_add", options={"expose":true})
     */
    public function addAction(Request $request) {

        $item = new User();

        $form = $this->createFormBuilder($item)
                ->add('username', TextType::class)
                ->add('email', EmailType::class)
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match.',
                    'options' => array('attr' => array('class' => 'password-field')),
                    'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password')
                ))
                ->add('admin', CheckboxType::class, array(
                    'label' => 'Administrator',
                    'required' => false
                ))
                ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->persist($item);
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('user_edit', array('id' => $item->getId()));
        }

        return $this->render('user/add.html.twig', array(
                    'item' => $item,
                    'form' => $form->createView()
        ));
    }

}
