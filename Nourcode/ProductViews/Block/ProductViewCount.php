<?php

namespace Nourcode\ProductViews\Block;

use Magento\Catalog\Block\Product\AbstractProduct;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\View\AbstractView;
use Magento\Framework\Stdlib\ArrayUtils;
use Magento\Reports\Model\ResourceModel\Product\Collection;

class ProductViewCount extends AbstractView
{

    protected $_productCollection;
    protected $abstractProduct;

    /**
     * ProductViewCount constructor.
     *
     * @param Context $context
     * @param ArrayUtils $arrayUtils
     * @param Collection $productCollection
     * @param AbstractProduct $abstractProduct
     */
    public function __construct(Context $context, ArrayUtils $arrayUtils, Collection $productCollection, AbstractProduct $abstractProduct)
    {
        $this->_productCollection = $productCollection;
        $this->abstractProduct = $abstractProduct;
        parent::__construct($context, $arrayUtils);
    }

    public function getProductCount($id)
    {
        $productData = $this->_productCollection->addViewsCount()->getData();
        if (count($productData) > 0) {
            foreach ($productData as $product) {
                if ($product['entity_id'] == $id) {
                    return (int)$product['views'];
                }
            }
        }

        return 0;
    }

    public function getProductID()
    {
        $productId = $this->abstractProduct->getProduct()->getId();

        return $productId;
    }
}
