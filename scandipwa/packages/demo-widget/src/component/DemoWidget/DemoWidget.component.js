/* eslint-disable react/boolean-prop-naming */
/* eslint-disable @scandipwa/scandipwa-guidelines/only-render-in-component */
/**
* @category  ScandiPWA
* @author    Mert Gulmus <mert.gulmus@scandiweb.com | info@scandiweb.com>
* @author    Arturs Strucinskis <arturs.strucinskis@scandiweb.com | info@scandiweb.com>
* @license   http://opensource.org/licenses/OSL-3.0 The Open Software License 3.0 (OSL-3.0)
* @copyright Copyright (c) 2022 Scandiweb, Inc (https://scandiweb.com)
*/
import PropTypes from 'prop-types';
import { createRef, PureComponent } from 'react';

import Html from 'SourceComponent/Html';
import CSS from 'Util/CSS';

import './DemoWidget.style';

/** @namespace Scandipwa/Component/DemoWidget/Component */
export class DemoWidgetComponent extends PureComponent {
    static propTypes = {
        title: PropTypes.string.isRequired,
        image: PropTypes.string.isRequired,
        layout: PropTypes.string.isRequired,
        content: PropTypes.string.isRequired,
        color: PropTypes.string.isRequired,
        phrase: PropTypes.string.isRequired,
        days: PropTypes.number.isRequired,
        hours: PropTypes.number.isRequired,
        minutes: PropTypes.number.isRequired,
        seconds: PropTypes.number.isRequired,
        noTimeLeft: PropTypes.bool.isRequired,
        link: PropTypes.string.isRequired,
        dynamicRowsArray: PropTypes.arrayOf(
            PropTypes.shape({
                name: PropTypes.string.isRequired,
                phone: PropTypes.string.isRequired,
                email: PropTypes.string.isRequired
            }).isRequired
        ).isRequired
    };

    widgetRef = createRef();

    componentDidMount() {
        this.setStyles();
    }

    componentDidUpdate() {
        this.setStyles();
    }

    setStyles() {
        const { color } = this.props;
        CSS.setVariable(this.widgetRef, 'div-background-color', color);
    }

    renderImage() {
        const { image } = this.props;
        return (
            <img block="DemoWidget" elem="Image" src={ image } alt={ image } />
        );
    }

    renderTitle() {
        const { title } = this.props;
        return (
            <h1>{ title }</h1>
        );
    }

    renderWysiwyg() {
        const { content } = this.props;
        return <Html content={ content } />;
    }

    renderDynamicRows() {
        const { dynamicRowsArray } = this.props;

        return (
            <div>
                { dynamicRowsArray.map(({ name, phone, email }, index) => {
                    const objNum = `${ index + 1 }.`;

                    return (
                        <div key={ objNum }>
                            <p>
                                name:
                                { name }
                            </p>
                            <p>
                                phone:
                                { phone }
                            </p>
                            <p>
                                email:
                                { email }
                            </p>
                        </div>
                    );
                }) }
            </div>
        );
    }

    renderTimeBlock(time, suffix) {
        const { noTimeLeft, phrase } = this.props;

        if (noTimeLeft) {
            return (
                <div
                  block="Countdown"
                  elem="TimeBlock"
                >
                     <span>{ `${phrase}` }</span>
                </div>
            );
        }

        return (
            <div
              block="Countdown"
              elem="TimeBlock"
            >
                 <span>{ `${time} ${suffix}` }</span>
            </div>
        );
    }

    renderLink() {
        const { link } = this.props;
        if (!link) {
            return '';
        }

        return (
            <div
              block="DemoWidget"
              elem="Text"
            >
                <a href={ link }>{ __('Link Text') }</a>
            </div>
        );
    }

    render() {
        const {
            layout,
            days,
            hours,
            minutes,
            seconds
        } = this.props;

        return (
            <div
              block="DemoWidget"
              ref={ this.widgetRef }
              mods={ { layout } }
            >
                { this.renderTitle() }
                { this.renderImage() }
                { this.renderWysiwyg() }
                { this.renderLink() }
                <div block="DemoWidget" element="Countdown">
                    { this.renderTimeBlock(days, __('days')) }
                    { this.renderTimeBlock(hours, __('h')) }
                    { this.renderTimeBlock(minutes, __('min')) }
                    { this.renderTimeBlock(seconds, __('sec')) }
                </div>
                { this.renderDynamicRows() }
            </div>
        );
    }
}

export default DemoWidgetComponent;
