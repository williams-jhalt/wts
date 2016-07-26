<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/tasks")
 */
class TasksController extends Controller {

    /**
     * @Route("/load-new-orders")
     */
    public function loadNewOrdersAction() {
        $this->get('erp_one_order_service')->loadOrders();
        return new Response();
    }

    /**
     * @Route("/update-open-orders")
     */
    public function updateOpenOrdersAction() {
        $this->get('erp_one_order_service')->updateOpenSalesOrders();
        return new Response();
    }
    
    /**
     * @Route("/load-products")
     */
    public function loadProductsAction() {
        $this->get('erp_one_product_service')->loadProducts();
        return new Response();
    }

}
