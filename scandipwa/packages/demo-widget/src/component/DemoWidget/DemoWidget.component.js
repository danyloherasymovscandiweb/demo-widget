/**
 * @category  ScandiPWA
 * @author    Mert Gulmus <mert.gulmus@scandiweb.com | info@scandiweb.com>
 * @author    Arturs Strucinskis <arturs.strucinskis@scandiweb.com | info@scandiweb.com>
 * @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
 * @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
 */
import PropTypes from 'prop-types';
import Html from 'SourceComponent/Html';
import { PureComponent } from 'react';

import './DemoWidget.style';

/** @namespace DemoWidget/Component/DemoWidget/Component/DemoWidgetComponent */
export class DemoWidgetComponent extends PureComponent {
    static propTypes = {
        title: PropTypes.string.isRequired,
        image: PropTypes.string.isRequired,
        layout: PropTypes.string.isRequired,
        wysiwyg: PropTypes.string.isRequired
    };

    renderImage() {
        const { image } = this.props;
        return (
            <img block="DemoWidget" elem="Image" src={ image } alt="" />
        );
    }

    renderTitle() {
        const { title } = this.props;
        return (
            <h1>{ title }</h1>
        );
    }

    renderWysiwyg() {
        const { wysiwyg } = this.props;
        return <Html content={ wysiwyg } />;
    }

    render() {
        const { layout } = this.props;
        return (
            <div block="DemoWidget" mods={ { layout } }>
                { this.renderTitle() }
                { this.renderImage() }
                { this.renderWysiwyg() }
            </div>
        );
    }
}

export default DemoWidgetComponent;
