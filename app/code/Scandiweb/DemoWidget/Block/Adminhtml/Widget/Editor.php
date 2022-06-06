<?php
/**
 * @category  Scandiweb
 * @package   Scandiweb_DemoWidget
 * @author    Ibrahim Zidan <ibrahim.zidan@scandiweb.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 * @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
 */

declare(strict_types=1);

namespace Scandiweb\DemoWidget\Block\Adminhtml\Widget;

use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Factory as ElementFactory;
use Magento\Backend\Block\Template;
use Magento\Cms\Model\Wysiwyg\Config as WysiwygConfig;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\LocalizedException;

/**
 * WYSIWYG Editor field
 */
class Editor extends Template
{
    /**
     * @var ElementFactory
     */
    protected $elementFactory;

    /**
     * @var WysiwygConfig
     */
    protected $wysiwygConfig;

    /**
     * Cosntructs Editor
     *
     * @param Context        $context
     * @param ElementFactory $elementFactory
     * @param array          $data
     */
    public function __construct(
        Context $context,
        ElementFactory $elementFactory,
        WysiwygConfig $wysiwygConfig,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->elementFactory = $elementFactory;
        $this->wysiwygConfig = $wysiwygConfig;
    }

    /**
     * Prepare WYSIWYG Editor element HTML
     *
     * @param AbstractElement $element
     *
     * @return AbstractElement
     * @throws LocalizedException
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $editor = $this->elementFactory->create(
            'editor',
            ['data' => $element->getData()]
        )
            ->setLabel('')
            ->setForm($element->getForm())
            ->setWysiwyg(true)
            ->setConfig(
                $this->wysiwygConfig->getConfig(
                    [
                        'add_variables' => false,
                        'add_widgets' => false,
                        'add_images' => false,
                        'force_load' => true,
                    ]
                )
            );

        if ($element->getRequired()) {
            $editor->addClass('required-entry');
        }

        $elementHtml = $editor->getElementHtml();
        $editedHtml = str_replace('"', "'", $elementHtml);

        $element->setData('after_element_html', $editedHtml);
        $element->setValue('');
        return $element;
    }
}
