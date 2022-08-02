/* eslint-disable no-useless-escape */
/* eslint-disable no-magic-numbers */
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

/** @namespace Scandipwa/Component/DemoWidget/Container/mapStateToProps */
export const mapStateToProps = (state) => ({
    baseLinkUrl: state.ConfigReducer.base_link_url // it's a random state imported from ConfigReducer, nothing crucial
});

/** @namespace Scandipwa/Component/DemoWidget/Container/mapDispatchToProps */
export const mapDispatchToProps = () => ({
});

/** @namespace Scandipwa/Component/DemoWidget/Container */
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
            wysiwyg = '',
            type,
            color,
            date,
            phrase,
            link = '',
            textarea
        } = this.props;

        const new_string = this.cleanJson();
        const noTimeLeft = this.state.timeLeft <= 0;
        const days = Math.floor(this.state.timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((this.state.timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((this.state.timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((this.state.timeLeft % (1000 * 60)) / 1000);
        // html parser doesn't see opening and closing tags in element format, that's why we replace them with < and >
        const content = wysiwyg.replace(/&lt;/g, '<').replace(/&gt;/g, '>');

        return {
            baseLinkUrl,
            image,
            layout,
            title,
            content,
            type,
            color,
            date,
            phrase,
            link,
            days,
            hours,
            minutes,
            seconds,
            noTimeLeft,
            new_string,
            textarea
        };
    }

    __construct(props) {
        super.__construct(props);

        const { date: endDateString } = this.props;
        this.endDate = new Date(endDateString);

        this.state = {
            timeLeft: this.endDate.getTime() - new Date().getTime()
        };

        this.interval = null;
    }

    componentDidMount() {
        this.interval = setInterval(() => {
            this.setState({
                timeLeft: this.endDate.getTime() - new Date().getTime()
            });
        }, 1000);
    }

    cleanJson() {
        const { textarea } = this.props;

        const realJson = textarea.replaceAll('\\"', '"');

        return realJson;
    }

    componentWillUnmount() {
        clearInterval(this.interval);
    }

    render() {
        return (
            <DemoWidget
              { ...this.containerProps() }
            />
        );
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(DemoWidgetContainer);
