<?php

namespace Nourcode\ProductViews\Block;

use Magento\Widget\Block\BlockInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\App\Action\Action;

class MostViewProduct extends Template implements BlockInterface
{
    protected $context;

    /**
     * @var \Magento\Reports\Model\ResourceModel\Product\CollectionFactory
     */
    protected $CollectionFactory;

    /**
     * @var \Magento\Catalog\Block\Product\ListProduct
     */
    protected $listProduct;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Review\Model\ReviewFactory
     */
    protected $reviewFactory;

    /**
     * @var string
     */
    protected $_template = "most_view_product.phtml";

    /**
     * Initialize Objects
     *
     * @param \Magento\Framework\View\Element\Template\Context               $context           Initialize Context list
     * @param \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $CollectionFactory Initialize Collection factory list
     * @param \Magento\Catalog\Block\Product\ListProduct                     $listProduct       Initialize List Product
     * @param \Magento\Store\Model\StoreManagerInterface                     $storeManager      Initialize Store manager
     * @param \Magento\Review\Model\ReviewFactory                            $reviewFactory     Initialize Review Factory
     * @param array                                                          $data              Initialize data array
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Reports\Model\ResourceModel\Product\CollectionFactory $CollectionFactory,
        \Magento\Catalog\Block\Product\ListProduct $listProduct,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Review\Model\ReviewFactory $reviewFactory,
        array $data = []
    ) {
        $this->CollectionFactory = $CollectionFactory;
        $this->listProduct = $listProduct;
        $this->storeManager = $storeManager;
        $this->reviewFactory = $reviewFactory;
        parent::__construct($context, $data);
    }

    /**
     * @param $_item
     * @return array
     */
    public function getAddToCartPostParams($_item)
    {
        return $this->listProduct->getAddToCartPostParams($_item);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getImageUrl()
    {
        return $this->storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'catalog/product';
    }

    /**
     * @param $_item
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRatingSummary($_item)
    {
        $this->reviewFactory->create()->getEntitySummary($_item, $this->storeManager->getStore()->getId());
        $ratingSummary = $_item->getRatingSummary()->getRatingSummary();

        return $ratingSummary;
    }

    /**
     * @param $_item
     * @return mixed
     */
    public function getReviewsCount($_item)
    {
        $_reviewCount = $_item->getRatingSummary()->getReviewsCount();
        return $_reviewCount;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $priceType
     * @param string $renderZone
     * @param array $arguments
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getProductPriceHtml(
        \Magento\Catalog\Model\Product $product,
        $priceType = null,
        $renderZone = \Magento\Framework\Pricing\Render::ZONE_ITEM_LIST,
        array $arguments = []
    ) {
        if (!isset($arguments['zone'])) {
            $arguments['zone'] = $renderZone;
        }
        $arguments['price_id'] = isset($arguments['price_id'])
            ? $arguments['price_id']
            : 'old-price-' . $product->getId() . '-' . $priceType;
        $arguments['include_container'] = isset($arguments['include_container'])
            ? $arguments['include_container']
            : true;
        $arguments['display_minimal_price'] = isset($arguments['display_minimal_price'])
            ? $arguments['display_minimal_price']
            : true;

        $priceRender = $this->getLayout()->getBlock('product.price.render.default');

        $price = '';
        if ($priceRender) {
            $price = $priceRender->render(
                \Magento\Catalog\Pricing\Price\FinalPrice::PRICE_CODE,
                $product,
                $arguments
            );
        }
        return $price;
    }

    /**
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCollection($ids)
    {
        $currentStoreId = $this->storeManager->getStore()->getId();

        $collection = $this->CollectionFactory->create()
            ->addAttributeToSelect(
                '*'
            )->addViewsCount()->setStoreId(
                $currentStoreId
            )->addStoreFilter(
                $currentStoreId
            )->addCategoriesFilter(['in' => $ids]);
        $items = $collection->getItems();
        return $items;
    }

    /**
     * @return string
     */
    public function getCartParamNameURLEncoded()
    {
        return Action::PARAM_NAME_URL_ENCODED;
    }
}
