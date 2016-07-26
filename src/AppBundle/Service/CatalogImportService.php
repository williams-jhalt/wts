<?php

namespace AppBundle\Service;

use AppBundle\Entity\Category;
use AppBundle\Entity\Manufacturer;
use AppBundle\Entity\Product;
use AppBundle\Entity\ProductAttachment;
use AppBundle\Entity\ProductType;
use DateTime;
use Doctrine\ORM\EntityManager;

class CatalogImportService {

    /**
     * @var EntityManager
     */
    private $_em;

    public function __construct(EntityManager $em) {
        $this->_em = $em;
    }

    public function importManufacturers($filename) {

        $repo = $this->_em->getRepository('AppBundle:Manufacturer');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $manufacturer = $repo->findOneByCode($data[0]);

                if ($manufacturer === null) {
                    $manufacturer = new Manufacturer();
                    $manufacturer->setCode($data[0]);
                }

                $manufacturer->setName($data[1]);

                $this->_em->persist($manufacturer);
            }

            $this->_em->flush();
        }
    }

    public function importProductTypes($filename) {

        $repo = $this->_em->getRepository('AppBundle:ProductType');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $type = $repo->findOneByCode($data[0]);

                if ($type === null) {
                    $type = new ProductType();
                    $type->setCode($data[0]);
                }

                $type->setName($data[1]);

                $this->_em->persist($type);
            }

            $this->_em->flush();
        }
    }

    public function importCategories($filename) {

        $repo = $this->_em->getRepository('AppBundle:Category');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $category = $repo->findOneByCode($data[0]);

                if ($category === null) {
                    $category = new Category();
                    $category->setCode($data[0]);
                }

                $category->setName($data[1]);

                if (!empty($data[2])) {
                    $parent = $repo->findOneByCode($data[2]);
                    if ($parent === null) {
                        $parent = new Category();
                        $parent->setCode($data[2]);
                        $parent->setName($data[2]);
                        $this->_em->persist($parent);
                        $this->_em->flush();
                    }
                    $category->setParent($parent);
                }

                $this->_em->persist($category);
            }

            $this->_em->flush();
        }
    }

    public function importProductImages($filename) {

        $repo = $this->_em->getRepository('AppBundle:ProductAttachment');
        $productRepo = $this->_em->getRepository('AppBundle:Product');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $product = $productRepo->findOneByItemNumber($data[0]);

                $url = "http://images.williams-trading.com/product_images/{$product->getItemNumber()}/{$data[2]}";

                if ($product !== null) {

                    $productAttachment = $repo->findOneBy(array('product' => $product, 'url' => $url));

                    if ($productAttachment === null) {

                        $productAttachment = new ProductAttachment();
                        $productAttachment->setUrl($url);
                        $productAttachment->setProduct($product);

                        $this->_em->persist($productAttachment);

                        if (($count % 100) == 0) {
                            $this->_em->flush();
                            $this->_em->clear();
                        }

                        $count++;
                    }
                }
            }

            $this->_em->flush();
            $this->_em->clear();
        }
    }

    public function importProductDescriptions($filename) {

        $productRepository = $this->_em->getRepository('AppBundle:Product');

        $xmlProducts = simplexml_load_file($filename);

        $batchSize = 50;
        $i = 0;

        foreach ($xmlProducts as $xmlProduct) {
            
            $product = $productRepository->findOneByItemNumber($xmlProduct['sku']);

            if (!$product) {
                continue;
            }

            $product->getDetail()->setDescription($xmlProduct);

            $this->_em->persist($product);


            if (($i % $batchSize) === 0) {
                $this->_em->flush();
                $this->_em->clear();
            }

            $i++;
        }

        $this->_em->flush();
        $this->_em->clear();
    }

    public function importProductDetails($filename) {

        $repo = $this->_em->getRepository('AppBundle:Product');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $product = $repo->findOneByItemNumber($data[0]);

                if ($product !== null) {
                    $product->getDetail()
                            ->setHeight($data[1])
                            ->setLength($data[2])
                            ->setWidth($data[3])
                            ->setWeight($data[4]);

                    $this->_em->persist($product);

                    if (($count % 100) == 0) {
                        $this->_em->flush();
                        $this->_em->clear();
                    }
                }
            }

            $this->_em->flush();
            $this->_em->clear();
        }
    }

    public function importProducts($filename) {

        $productRepo = $this->_em->getRepository('AppBundle:Product');
        $manufacturerRepo = $this->_em->getRepository('AppBundle:Manufacturer');
        $productTypeRepo = $this->_em->getRepository('AppBundle:ProductType');
        $categoryRepo = $this->_em->getRepository('AppBundle:Category');

        $count = 0;

        if (($fh = fopen($filename, "r")) !== false) {
            while (($data = fgetcsv($fh, 1024, ",")) !== false) {

                if ($count == 0) {
                    $count++;
                    continue;
                }

                $product = $productRepo->findOneByItemNumber($data[0]);

                if ($product === null) {
                    $product = new Product();
                    $product->setItemNumber($data[0]);
                    $product->setName($data[1]);
                    $product->setReleaseDate(new DateTime($data[2]));
                }

                $product->setStockQuantity($data[3]);

                $manufacturer = $manufacturerRepo->findOneByCode($data[4]);

                if ($manufacturer === null) {
                    $manufacturer = new Manufacturer();
                    $manufacturer->setCode($data[4]);
                    $manufacturer->setName($data[4]);
                    $this->_em->persist($manufacturer);
                    $this->_em->flush($manufacturer);
                }

                $product->setManufacturer($manufacturer);

                $productType = $productTypeRepo->findOneByCode($data[5]);

                if ($productType === null) {
                    $productType = new ProductType();
                    $productType->setCode($data[5]);
                    $productType->setName($data[5]);
                    $this->_em->persist($productType);
                    $this->_em->flush($productType);
                }

                $product->setType($productType);

                $categoryCodes = explode("|", $data[6]);
                foreach ($categoryCodes as $categoryCode) {
                    $category = $categoryRepo->findOneByCode($categoryCode);
                    if ($category === null) {
                        $category = new Category();
                        $category->setCode($categoryCode);
                        $category->setName($categoryCode);
                        $this->_em->persist($category);
                        $this->_em->flush($category);
                    }
                }

                $product->setManufacturerItemNumber($data[7]);
                $product->setBarcode($data[8]);
                $product->setActive($data[9]);

                $this->_em->persist($product);

                if (($count % 100) == 0) {
                    $this->_em->flush();
                    $this->_em->clear();
                }

                $count++;
            }

            $this->_em->flush();
            $this->_em->clear();
        }
    }

}
