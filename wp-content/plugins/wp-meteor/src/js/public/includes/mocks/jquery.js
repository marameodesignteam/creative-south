import dispatcher from '../utils/dispatcher';
import delta from '../utils/delta';
const c = process.env.DEBUG ? console.log : () => {};

/**
 * class name should not match mocked object
 */
export default class jQueryMock {
    constructor() {

        let Mock;
        let loaded = false;

        const override = (jQuery) => {

            if (!loaded && jQuery && jQuery.fn && !jQuery.__wpmeteor) {
                process.env.DEBUG && c(delta(), 'new jQuery detected', jQuery);

                // can't use () => {} as it binds to different this
                const enqueue = function (func) {
                    process.env.DEBUG && c(delta(), 'enqueued jQuery(func)', func);
                    document.addEventListener('DOMContentLoaded', e => func.bind(document)(jQuery, e));
                    return this;
                };

                // const oldReady = jQuery.fn.ready;
                jQuery.fn.ready = enqueue;
                jQuery.fn.init.prototype.ready = enqueue;
                jQuery.__wpmeteor = true;

                /*
                dispatcher.on('l', () => {
                    // jQuery.fn.ready = oldReady;
                    // jQuery.fn.init.prototype.ready = oldReady;
                    setTimeout(() => dispatcher.emit.bind(dispatcher, 'r'));
                });
                */
            }
            return jQuery;
        }

        if (window.jQuery) {
            Mock = override(window.jQuery);
        }

        Object.defineProperties(window, {
            'jQuery': {
                get() {
                    return Mock;
                },
                set(jQuery) {
                    return Mock = override(jQuery);
                }
            }
        });

        dispatcher.on('l', () => loaded = true);
    }
}
