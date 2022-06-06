/**
 * @category  ScandiPWA
 * @author    Mert Gulmus <mert.gulmus@scandiweb.com | info@scandiweb.com>
 * @author    Arturs Strucinskis <arturs.strucinskis@scandiweb.com | info@scandiweb.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 * @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
 */

import PropTypes from 'prop-types';
import { connect } from 'react-redux';
import { DemoWidgetComponent as DemoWidget } from './DemoWidget.component';

/** @namespace Scandipwa/Component/DemoWidget/Container/mapStateToProps */
export const mapStateToProps = (state) => ({
    baseLinkUrl: state.ConfigReducer.base_link_url // it's a random state imported from ConfigReducer, nothing crucial
});

/** @namespace Scandipwa/Component/DemoWidget/Container/mapDispatchToProps */
export const mapDispatchToProps = () => ({
});

/** @namespace Scandipwa/Component/DemoWidget/Container/DemoWidgetContainer */
export class DemoWidgetContainer {
    static propTypes = {
        baseLinkUrl: PropTypes.string.isRequired
    };

    containerProps() {
        const {
            baseLinkUrl
        } = this.props;

        return {
            baseLinkUrl
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
