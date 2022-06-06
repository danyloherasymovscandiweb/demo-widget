/* eslint-disable @scandipwa/scandipwa-guidelines/jsx-no-props-destruction */
/**
 * @category  ScandiPWA
 * @author    Mert Gulmus <mert.gulmus@scandiweb.com | info@scandiweb.com>
 * @author    Arturs Strucinskis <arturs.strucinskis@scandiweb.com | info@scandiweb.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 * @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
 */

import RenderWhenVisible from 'Component/RenderWhenVisible';

export const DemoWidget = lazy(() => import(
    /* webpackMode: "lazy", webpackChunkName: "widget-demo" */
    '../../component/DemoWidget'
));

// We need to notify WidgetFactory that we added new widgets. This is done by adding new key to renderMap
// Firstly, we need to write widget's backend name (defined in widget.xml)
// Secondly, we need to define the component that will be rendered
const renderMap = (member) => ({
    ...member,
    DemoWidget: {
        component: DemoWidget
    }
});

// We modified rendering logic to render it properly
// You don't need to change anything even if you add your own widgets
const renderContent = (args, callback, instance) => {
    const { type } = instance.props;

    const {
        component: Widget,
        fallback
    } = instance.renderMap[type] || {};

    if (Widget !== undefined) {
        return (
            <RenderWhenVisible fallback={ fallback }>
                <Widget { ...instance.props } />
            </RenderWhenVisible>
        );
    }

    return null;
};

// We define namespaces which will be overridden by our plugin
// Also no need to change anything
export default {
    'Component/WidgetFactory/Component': {
        'member-function': {
            renderContent
        },
        'member-property': {
            renderMap
        }
    }
};
