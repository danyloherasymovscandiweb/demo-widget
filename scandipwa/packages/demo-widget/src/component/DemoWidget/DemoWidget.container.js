/**
 * @category  ScandiPWA
 * @author    Mert Gulmus <mert.gulmus@scandiweb.com | info@scandiweb.com>
 * @author    Arturs Strucinskis <arturs.strucinskis@scandiweb.com | info@scandiweb.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 * @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
 */

import PropTypes from 'prop-types';
import { connect } from 'react-redux';

import DataContainer from 'Util/Request/DataContainer';

import { DemoWidgetComponent as DemoWidget } from './DemoWidget.component';

/** @namespace DemoWidget/Component/DemoWidget/Container/mapStateToProps */
export const mapStateToProps = (state) => ({
    baseLinkUrl: state.ConfigReducer.base_link_url // it's a random state imported from ConfigReducer, nothing crucial
});

/** @namespace DemoWidget/Component/DemoWidget/Container/mapDispatchToProps */
export const mapDispatchToProps = () => ({
});

/** @namespace DemoWidget/Component/DemoWidget/Container */
export class DemoWidgetContainer extends DataContainer {
    static propTypes = {
        baseLinkUrl: PropTypes.string.isRequired
    };

    containerProps() {
        const {
            baseLinkUrl,
            image,
            layout,
            title,
            wysiwyg,
            type
        } = this.props;

        // html parser doesn't see opening and closing tags in element format, that's why we replace them with < and >
        const content = wysiwyg.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

        return {
            baseLinkUrl,
            image,
            layout,
            title,
            content,
            type
        };
    }

    render() {
        return (
            <DemoWidget
              { ...this.containerFunctions }
              { ...this.containerProps() }
            />
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(DemoWidgetContainer);
