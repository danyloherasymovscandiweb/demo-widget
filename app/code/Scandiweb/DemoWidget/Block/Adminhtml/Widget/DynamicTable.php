<?php

namespace Scandiweb\DemoWidget\Block\Adminhtml\Widget;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Data\Form\Element\Text;
use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;
use Magento\Framework\Data\Form\Element\AbstractElement;

class DynamicTable extends Template implements BlockInterface
{

    /**
     * @var Factory
     */
    protected $elementFactory;

    /**
     * @param Context $context
     * @param Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Factory $elementFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * @throws \Exception
     */
    public function prepareElementHtml(AbstractElement $element)
    {
        $random = random_int(1,10000);
        /** @var Text $input */
        $input = $this->elementFactory->create("text", ['data' => $element->getData()]);
        $input->setId($element->getId());
        $input->setForm($element->getForm());
        $input->setClass("widget-option input-text admin__control-text");
        if ($element->getRequired()) {
            $input->addClass('required-entry');
        }

        // Below, we create the element that collects data from inputs
        // and turns them into a JSON-like object to further pass to ScandiPWA FE component
        $element->setData('after_element_html', $input->getElementHtml() . '
        <p id="tableOutputHolder"></p>
        <div class="container">
            <div id="parent_div">
                <div class="child_div">
                    <div class="input-group">
                    ' . // Number of columns, as well as their placeholders is defined here.
                        // Placeholders will be used later as JSON keys
                    '
                        <input type="text" class="inputs" name="name" placeholder="Name" />
                        <input type="text" class="inputs" name="phone" placeholder="Phone" />
                        <input type="text" class="inputs" name="email" placeholder="email" />
                        <input class="btn btn-danger deleteButton" type="button" value="-" />
                    </div>
                </div>
            </div>
            <input class="btn btn-success " type="button" id="create_button" value="+" />
            <input id="saveBtn" type="button" value="Save values" />
        </div>
        <script type="text/javascript">
            require(["jquery", "jquery/colorpicker/js/colorpicker"], function ($) {
            ' . // The `numberOfInputs` variable hardcoded below equals to amount of columns
                // in each row. It also should reflect on the jQuery functions that process
                // clicks on "+" and "-" buttons (e.g if you need to use 4 columns, then
                // `numberOfInputs` should equal to 4, and should decrease/increase correspondingly.
            '
            let numberOfInputs = 3;
            $("#create_button").click(function() {
                var html = $(".child_div:first").parent().html();
                $(html).insertBefore(this);
                numberOfInputs += 3;
            });
            $(document).on("click", ".deleteButton", function() {
                $(this).closest(".child_div").remove();
                numberOfInputs -= 3;
            });

            const saveBtn = document.getElementById("saveBtn");

            const inputsArr = [];

            saveBtn.addEventListener("click", function (event) {
                const inputs = document.getElementsByClassName("inputs");
                const jsonHolder = document.getElementById("jsonHolder");

                for (i = 0; i < numberOfInputs; i += 3) {
                    const entries = new Map([[inputs[i].name, inputs[i].value], [inputs[i+1].name, inputs[i+1].value], [inputs[i+2].name, inputs[i+2].value]]);
                    inputsArr.push(JSON.stringify(Object.fromEntries(entries)));
                };

                ' . // Data from inputs is being collected to an array to work corectly with ScandiPWA
                    // component. Due to magento specifics, we can't use double quotes inside the string
                    // we pass to the FE, so we replace them with single quotes. We also replace commas inside
                    // each object to later split the array correctly
                '
                const tableArr = inputsArr.map(item =>
                    item.toString().replaceAll("\"", "\'").replaceAll(",", "|")
                );

                $("#' . $element->getId() . '").val(" ");
                $("#' . $element->getId() . '").val(tableArr);
            });
        });
        </script>');
        return $element;
    }
}
