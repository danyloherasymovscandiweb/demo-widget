<?php

namespace Scandiweb\DemoWidget\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;

class ColorPicker extends Template implements BlockInterface
{

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @var array
     */
    protected $_selectedProducts = [];

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Category
     */
    protected $_resourceCategory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product
     */
    protected $_resourceProduct;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Helper\Data $backendHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory
     * @param \Magento\Catalog\Model\ResourceModel\Category $resourceCategory
     * @param \Magento\Catalog\Model\ResourceModel\Product $resourceProduct
     * @param array $data

     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        \Magento\Backend\Helper\Data $backendHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory,
        \Magento\Catalog\Model\ResourceModel\Category $resourceCategory,
        \Magento\Catalog\Model\ResourceModel\Product $resourceProduct,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->_categoryFactory = $categoryFactory;
        $this->_collectionFactory = $collectionFactory;
        $this->_resourceCategory = $resourceCategory;
        $this->_resourceProduct = $resourceProduct;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @throws \Exception
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $categoryUrl = $element->getValue() ?: "No URL given";
        $newUrl = "New URL";

        $chooser = $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        );

        /** @var Text $input */
        $input = $this->elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        $newUrl = $this->getCategoryUrl($element->getId());
        $element->setData('after_element_html', $input->getElementHtml() . $chooser->toHtml() . '
        <p id="categoryUrlHolder_' . $newUrl . '"></p>');
        return $element;
    }

    public function getCategoryUrl($id) {
        $category = $this->categoryRepository->get($id, $this->_storeManager->getStore()->getId());

        return $category->getUrl();
    }

    public function getCheckboxCheckCallback()
    {
        if ($this->getUseMassaction()) {
            return "function (grid, element) {
                $(grid.containerId).fire('product:changed', {element: element});
            }";
        }
    }

    /**
     * Grid Row JS Callback
     *
     * @return string
     */
    public function getRowClickCallback()
    {
        if (!$this->getUseMassaction()) {
            $chooserJsObject = $this->getId();
            return '
                function (grid, event) {
                    var trElement = Event.findElement(event, "tr");
                    var productId = trElement.down("td").innerHTML;
                    var productName = trElement.down("td").next().next().innerHTML;
                    var optionLabel = productName;
                    var optionValue = "product/" + productId.replace(/^\s+|\s+$/g,"");
                    if (grid.categoryId) {
                        optionValue += "/" + grid.categoryId;
                    }
                    if (grid.categoryName) {
                        optionLabel = grid.categoryName + " / " + optionLabel;
                    }
                    ' .
                $chooserJsObject .
                '.setElementValue(optionValue);
                    ' .
                $chooserJsObject .
                '.setElementLabel(optionLabel);
                    ' .
                $chooserJsObject .
                '.close();
                }
            ';
        }
    }

    /**
     * Category Tree node onClick listener js function
     *
     * @return string
     */
    public function getCategoryClickListenerJs()
    {
        $js = '
            function (node, e) {
                {jsObject}.addVarToUrl("category_id", node.attributes.id);
                {jsObject}.reload({jsObject}.url);
                {jsObject}.categoryId = node.attributes.id != "none" ? node.attributes.id : false;
                {jsObject}.categoryName = node.attributes.id != "none" ? node.text : false;
            }
        ';
        $js = str_replace('{jsObject}', $this->escapeJs($this->getJsObjectName()), $js);
        return $js;
    }

    /**
     * Filter checked/unchecked rows in grid
     *
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        if ($column->getId() == 'in_products') {
            $selected = $this->getSelectedProducts();
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', ['in' => $selected]);
            } else {
                $this->getCollection()->addFieldToFilter('entity_id', ['nin' => $selected]);
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * Prepare products collection, defined collection filters (category, product type)
     *
     * @return Extended
     */
    protected function _prepareCollection()
    {
        /* @var $collection \Magento\Catalog\Model\ResourceModel\Product\Collection */
        $collection = $this->_collectionFactory->create()->setStoreId(0)->addAttributeToSelect('name');

        if ($categoryId = $this->getCategoryId()) {
            $category = $this->_categoryFactory->create()->load($categoryId);
            if ($category->getId()) {
                // $collection->addCategoryFilter($category);
                $productIds = $category->getProductsPosition();
                $productIds = array_keys($productIds);
                if (empty($productIds)) {
                    $productIds = 0;
                }
                $collection->addFieldToFilter('entity_id', ['in' => $productIds]);
            }
        }

        if ($productTypeId = $this->getProductTypeId()) {
            $collection->addAttributeToFilter('type_id', $productTypeId);
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare columns for products grid
     *
     * @return Extended
     */
    protected function _prepareColumns()
    {
        if ($this->getUseMassaction()) {
            $this->addColumn(
                'in_products',
                [
                    'header_css_class' => 'a-center',
                    'type' => 'checkbox',
                    'name' => 'in_products',
                    'inline_css' => 'checkbox entities',
                    'field_name' => 'in_products',
                    'values' => $this->getSelectedProducts(),
                    'align' => 'center',
                    'index' => 'entity_id',
                    'use_index' => true
                ]
            );
        }

        $this->addColumn(
            'entity_id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'entity_id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn(
            'chooser_sku',
            [
                'header' => __('SKU'),
                'name' => 'chooser_sku',
                'index' => 'sku',
                'header_css_class' => 'col-sku',
                'column_css_class' => 'col-sku'
            ]
        );
        $this->addColumn(
            'chooser_name',
            [
                'header' => __('Product'),
                'name' => 'chooser_name',
                'index' => 'name',
                'header_css_class' => 'col-product',
                'column_css_class' => 'col-product'
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * Adds additional parameter to URL for loading only products grid
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl(
            'catalog/product_widget/chooser',
            [
                'products_grid' => true,
                '_current' => true,
                'uniq_id' => $this->getId(),
                'use_massaction' => $this->getUseMassaction(),
                'product_type_id' => $this->getProductTypeId()
            ]
        );
    }

    /**
     * Setter
     *
     * @param array $selectedProducts
     * @return $this
     */
    public function setSelectedProducts($selectedProducts)
    {
        $this->_selectedProducts = $selectedProducts;
        return $this;
    }

    /**
     * Getter
     *
     * @return array
     */
    public function getSelectedProducts()
    {
        if ($selectedProducts = $this->getRequest()->getParam('selected_products', null)) {
            $this->setSelectedProducts($selectedProducts);
        }
        return $this->_selectedProducts;
    }
}
