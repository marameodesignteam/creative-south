import jQueryMock from './includes/mocks/jquery';
import dispatcher from './includes/utils/dispatcher';
import listenerOptions from './includes/utils/listener-options';
import delta from './includes/utils/delta';
import lazysizes from './includes/utils/lazysizes-core';
import lazybg from './includes/utils/ls.bgset';
import elementorAnimations from './includes/elementor/animations';
import elementorPP from './includes/elementor/pp-menu';

const DCL = 'DOMContentLoaded';
const separator = "----";
const S = 'SCRIPT';
const I = 'requestIdleCallback';
const N = null;
const c = process.env.DEBUG ? console.log : () => { };
const ce = console.error;
const lazyloaded = 'lazyloaded';
const lazybeforeunveil = 'lazybeforeunveil';
const prefix = 'data-wpmeteor-';

(function (w, d, a, r, ga, sa, ra, L, E, interactionEvents) {

    const forEach = function (callback, thisArg) {
        thisArg = thisArg || w;
        for (var i = 0; i < this.length; i++) {
            callback.call(thisArg, this[i], i, this);
        }
    }

    if ('NodeList' in w && !NodeList.prototype.forEach) {
        process.env.DEBUG && c("polyfilling NodeList.forEach");
        NodeList.prototype.forEach = forEach;
    }
    if ('HTMLCollection' in w && !HTMLCollection.prototype.forEach) {
        process.env.DEBUG && c("polyfilling HTMLCollection.forEach");
        HTMLCollection.prototype.forEach = forEach;
    }

    if (_wpmeteor['elementor-animations']) {
        elementorAnimations();
    }

    if (_wpmeteor['elementor-pp']) {
        elementorPP();
    }

    const reorder = [];
    const iframes = [];

    let firstInteractionFired = false;
    let DOMContentLoaded = false;
    let DOMContentLoadedPreQueue = [];
    let DOMContentLoadedPreQueue_window = [];
    let DOMContentLoadedQueue = [];
    let DOMContentLoadedQueue_window = [];
    let WindowLoaded = false;
    let WindowLoadedPreQueue = [];
    let WindowLoadedQueue = [];
    let emulatedDOMContentLoadedFired = false;
    let emulatedWindowLoadedFired = false;

    // let iterating = true;

    const nextTick = setTimeout;

    if (process.env.DEBUG) {
        d[a](DCL, () => {
            c(delta(), separator, DCL);
        });

        dispatcher.on('l', () => {
            c(delta(), separator, "L");
        });

        w[a](L, () => {
            c(delta(), separator, L);
        });
    }

    let origAddEventListener, origRemoveEventListener;

    // saving original methods
    let dOrigAddEventListener = d[a].bind(d);
    let dOrigRemoveEventListener = d[r].bind(d);
    let wOrigAddEventListener = w[a].bind(w);
    let wOrigRemoveEventListener = w[r].bind(w);

    if ("undefined" != typeof EventTarget) {
        origAddEventListener = EventTarget.prototype.addEventListener;
        origRemoveEventListener = EventTarget.prototype.removeEventListener;
        // saving original methods
        dOrigAddEventListener = origAddEventListener.bind(d);
        dOrigRemoveEventListener = origRemoveEventListener.bind(d);
        wOrigAddEventListener = origAddEventListener.bind(w);
        wOrigRemoveEventListener = origRemoveEventListener.bind(w);
    }
    const dOrigCreateElement = d.createElement.bind(d);

    const fireQueuedEvents = (queue, event, context) => {
        let func;
        while (func = queue.shift()) {
            try {
                // process.env.DEBUG && c(delta(), 'firing', func.prototype ? func.prototype.constructor : func);
                if (!func.hasOwnProperty('prototype') || func.prototype.constructor === func) {
                    func.bind(context || event.target)(event);
                } else {
                    func(event);
                }
            } catch (e) {
                ce(e, func);
            }
        }
    }

    const emulatedWindowLoaded = () => {
        // iterating = false;
        emulatedDOMContentLoadedFired = true;

        fireQueuedEvents(WindowLoadedQueue, WindowLoaded);
        emulatedWindowLoadedFired = true;

        setTimeout(() => dispatcher.emit('l'));
    }

    dOrigAddEventListener(DCL, (e) => DOMContentLoaded = e);
    wOrigAddEventListener(L, (e) => WindowLoaded = e); // L = load

    dOrigAddEventListener(DCL, (e) => {
        process.env.DEBUG && DOMContentLoadedPreQueue.length && c(delta(), "firing DOMContentLoaded before loading scripts");
        fireQueuedEvents(DOMContentLoadedPreQueue, e);
        fireQueuedEvents(DOMContentLoadedPreQueue_window, e, w);
    });

    wOrigAddEventListener(L, (e) => {
        process.env.DEBUG && WindowLoadedPreQueue.length && c(delta(), "firing window.load before loading scripts");
        fireQueuedEvents(WindowLoadedPreQueue, e);
    });

    let i = 0;
    const iterate = () => {
        process.env.DEBUG && c(delta(), 'it', i++, reorder.length);
        const element = reorder.shift();
        if (element) {
            // process.env.DEBUG && c(separator, "iterating", element, element.dataset);
            if (element[ga]('data-src')) {
                // process.env.DEBUG && c(delta(), "src", element);
                if (element.hasAttribute('async')) {
                    // process.env.DEBUG && c(delta(), "src", element);
                    unblock(element);
                    nextTick(iterate);
                } else {
                    unblock(element, iterate);
                }
            } else if (element.type == 'javascript/blocked') {
                unblock(element);
                // allow inserted script to execute
                nextTick(iterate);
            // not used
            // } else if (element.loaded === 0) {
            //     process.env.DEBUG && c(delta(), 'still waiting for', element.src)
            //     element[a](L, iterate); // L = load
            //     element[a](E, iterate); // E = error
            } else {
                // it might be wrongfully processed script by backend, eg type="application/ld+json" 
                // and execution will stop here
                // process.env.DEBUG && c("running next iteration");
                nextTick(iterate);
            }
        } else {
            // process.env.DEBUG && c('loaded all the scripts');
            // not restoring original addEventListener
            // to avoid unexpected failures, 
            // however, that triggers spurious handlers which were sleeping
            // d[a] = dOrigAddEventListener;
            if (DOMContentLoadedQueue.length) {
                process.env.DEBUG && c('running DOMContentLoadedQueue');
                fireQueuedEvents(DOMContentLoadedQueue, DOMContentLoaded);
                // in case new script tags (blocked) were added from DOMContentLoaded handlers
                // they should have been captured by MutationObserver
                nextTick(iterate);
            } else if (DOMContentLoadedQueue_window.length) {
                process.env.DEBUG && c('running DOMContentLoadedQueue_window');
                fireQueuedEvents(DOMContentLoadedQueue_window, DOMContentLoaded, w);
                // in case new script tags (blocked) were added from DOMContentLoaded handlers
                // they should have been captured by MutationObserver
                nextTick(iterate);
            } else if (WindowLoaded) {
                process.env.DEBUG && c('running emulatedWindowLoaded');
                emulatedWindowLoaded();
            } else {
                wOrigAddEventListener(L, emulatedWindowLoaded); // L = load
            }
        }
    };

    const cloneScript = (el) => {
        const newElement = d.createElement(S);
        const attrs = el.attributes;

        // move attributes
        for (var i = attrs.length - 1; i >= 0; i--) {
            newElement[sa](attrs[i].name, attrs[i].value);
        }
        newElement.bypass = true;
        newElement.type = 'text/javascript';
        newElement.text = el.text;
        newElement[ra]('data-wpmeteor-after');
        return newElement;
    }

    const replaceScript = (el, newElement) => {
        const parentNode = el.parentNode;
        if (parentNode)
            parentNode.replaceChild(newElement, el);
    }

    const unblock = (el, callback) => {
        // const ds = el.dataset;
        if (el[ga]('data-src')) {
            process.env.DEBUG && c(delta(), "unblocked src", el[ga]('data-src'));
            const newElement = cloneScript(el);

            const addEventListener = origAddEventListener 
                ? origAddEventListener.bind(newElement)
                : newElement[a].bind(newElement);
        
            if (callback) {
                const f = () => nextTick(callback);
                addEventListener(L, f);
                addEventListener(E, f);
            }

            addEventListener(E, e => ce(e)); // E = error
            newElement.src = el[ga]('data-src');
            newElement[ra]('data-src');

            replaceScript(el, newElement);

            // el.bypass = true;
            // if (onLoad)
            //     el[a](L, () => nextTick(onLoad)); // L = load
            // if (onError)
            //     el[a](E, () => nextTick(onError)); // E = error
            // el[a](E, e => ce(e)); // E = error
            // el.src = el[ga]('data-src');
            // el[ra]('data-src');
        } else if (el.type === 'javascript/blocked') {
            // onLoad is never passed here
            process.env.DEBUG && c(delta(), "unblocked inline", el);
            replaceScript(el, cloneScript(el));
        } else {
            process.env.DEBUG && c(delta(), "already unblocked", el.src);
            if (onLoad) {
                onLoad();
            }
        }
    }

    // Capturing and queueing DOMContentLoaded event handlers
    d[a] = (event, func, ...args) => {
        if (func && event === DCL) {
            if (!firstInteractionFired) {
                DOMContentLoadedPreQueue.push(func);
            } else if (!emulatedDOMContentLoadedFired) {
                // process.env.DEBUG && c(delta(), 'enqueued ' + DCL, func.prototype ? func.prototype.constructor : func);
                DOMContentLoadedQueue.push(func);
            } else {
                process.env.DEBUG && c(delta(), "late " + DCL, func.prototype ? func.prototype.constructor : func);
                fireQueuedEvents([func], DOMContentLoaded)
            }
            return;
        }
        return dOrigAddEventListener(event, func, ...args);
    }

    d[r] = (event, func) => {
        if (event === DCL) {
            DOMContentLoadedPreQueue = DOMContentLoadedPreQueue.filter(f => f !== func);
            DOMContentLoadedQueue = DOMContentLoadedQueue.filter(f => f !== func);
        }
        return dOrigRemoveEventListener(event, func);
    };

    const preload = () => reorder.forEach(script => {
        const src = script[ga]('data-src');
        if (src) {
            var s = dOrigCreateElement('link');
            s.rel = 'pre' + L;
            s.as = 'script';
            s.href = src;
            s.crossorigin = true;
            d.head.appendChild(s);
            process.env.DEBUG && c(delta(), 'preloading', src);
        }
    });

    dOrigAddEventListener(DCL, () => {
        d.querySelectorAll('script[' + prefix + 'after]').forEach(el => reorder.push(el));
        d.querySelectorAll('iframe[' + prefix + 'after]').forEach(el => iframes.push(el));
    });

    new jQueryMock();
    /* jQuery.ready fired */
    dispatcher.on('l', () => {
        iframes.forEach(iframe => {
            process.env.DEBUG && c(delta(), "loading iframe", iframe);
            iframe.src = iframe[ga]('data-src');
        });
    })

    /*
    if (location.href.match(/wpmeteordisable/)) {
        dOrigAddEventListener(DCL, () => {
            preload();
            iterate();
        });
        return;
    }

    d.createElement = function (...args) {
        // If this is not a script tag, bypass
        // dont rely on window loaded or document loaded as the tags might 
        // be inserted long after this
        if (args[0].toUpperCase() !== S) {

            // Binding to document is essential
            return dOrigCreateElement(...args)
        }

        const scriptElt = dOrigCreateElement(...args);
        // scriptElt.blackListed = false;

        // Backup the original setAttribute function
        const originalSetAttribute = scriptElt[sa].bind(scriptElt)
        const originalGetter = scriptElt.__proto__.__lookupGetter__('src').bind(scriptElt);

        Object.defineProperties(scriptElt, {
            'src': {
                get: originalGetter,
                set(value) {
                    if (scriptElt.bypass) {
                        // process.env.DEBUG && c('bypass for', value.toString());
                        return originalSetAttribute('src', value);
                    }
                    originalSetAttribute(prefix + 'after', 'REORDER');
                    return scriptElt.dataset.src = value;
                }
            }
        })

        // Monkey patch the setAttribute function so that the setter is called instead.
        // Otherwise, setAttribute('type', 'whatever') will bypass our custom descriptors!
        scriptElt[sa] = function (name, value) {
            if (name === 'src')
                scriptElt[name] = value
            else
                HTMLScriptElement.prototype[sa].call(scriptElt, name, value)
        }

        return scriptElt
    }

    // have to find scripts before us
    const observer = new MutationObserver(mutations => {
        mutations.forEach(({ addedNodes }) => {
            addedNodes.forEach(node => {
                // For each added script tag
                if (node.nodeType === 1) {
                    if (S === node.tagName && !node.bypass) {
                        const ds = node.dataset;
                        // const hasReorder = ds.wpmeteorAfter === 'REORDER';
                        // process.env.DEBUG && c([ds.src, node.src, ds.src && !node.src, node.dataset]);
                        // if (ds.src && !node.src || hasReorder) {
                        if (ds.wpmeteorAfter === 'REORDER') {
                            // process.env.DEBUG && c('pushing', node)
                            process.env.DEBUG && c(delta(), "blocked", node.tagName, ds.src || node);
                            reorder.push(node);
                            if (!iterating) {
                                // we have to restart iterate() to insert missing scripts
                                process.env.DEBUG && c(delta(), 'restarting reordering');
                                iterating = true;
                                nextTick(iterate);
                            }
                        } else if (node.src || node[ga]("src")) {
                            process.env.DEBUG && c(delta(), "detected", node[ga]("src"));
                            node.loaded = 0;
                            node[a](L, () => node.loaded = 1); // L = loaded
                            node[a](E, () => node.loaded = 1); // E = error
                            reorder.push(node);
                        } else {
                            // ce('missed', node);
                        }
                    } else if ('IFRAME' == node.tagName && node.dataset.wpmeteorAfter) {
                        iframes.push(node);
                    }
                }
            })
        })
    });
    */

    /* we have to override document.write as all of them will fire after DOMContentLoaded */
    let documentWrite = (str) => {
        if (d.currentScript) { // that implicitely means DOMContentLoad already fired
            d.currentScript.insertAdjacentHTML('afterend', str);
        } else {
            ce(delta(), "document.currentScript not set", str);
        }
    };
    Object.defineProperty(d, 'write', {
        get() { return documentWrite },
        set(func) { return documentWrite = func },
        // writable: false,
        // configurable: false,
    });

    // Capturing and queueing Window Load event handlers
    w[a] = (event, func, ...args) => {
        if (func && event === L) { // L = load
            if (!firstInteractionFired) {
                WindowLoadedPreQueue.push(func);
            } else if (!emulatedWindowLoadedFired) {
                WindowLoadedQueue.push(func);
            } else {
                process.env.DEBUG && c(delta(), "late Window load", func);
                fireQueuedEvents([func], WindowLoaded);
            }
            return;
        } else if (func && event === DCL) { // DOMContentLoaded
            if (!firstInteractionFired) {
                DOMContentLoadedPreQueue_window.push(func);
            } else if (!emulatedDOMContentLoadedFired) {
                DOMContentLoadedQueue_window.push(func);
            } else {
                process.env.DEBUG && c(delta(), "late " + DCL, func);
                fireQueuedEvents([func], DOMContentLoaded, w)
            }
            return;
        }
        // process.env.DEBUG && c(event, func);
        return wOrigAddEventListener(event, func, ...args);
    }

    w[r] = (event, func) => {
        if (event === L) { // L = load
            WindowLoadedPreQueue = WindowLoadedPreQueue.filter(f => f !== func);
            WindowLoadedQueue = WindowLoadedQueue.filter(f => f !== func);
        }
        return wOrigRemoveEventListener(event, func);
    };

    /*
    dispatcher.on('l', () => {
        observer.disconnect();
        d.createElement = dOrigCreateElement;
    });
    */

    let lazyImages = [];
    let toLazyLoad = 1;
    let lazyLoadedCount = -1;
    const lazyloadedHandler = () => {
        lazyLoadedCount++;
        if (!--toLazyLoad) {
            process.env.DEBUG && c(delta(), lazyloaded + " " + lazyLoadedCount + " images");
            dispatcher.emit('i');
        }
    }

    const lazybeforeunveilHandler = (e) => {
        if (lazyImages.indexOf(e.target) === -1) {
            process.env.DEBUG &&  c(delta(), e.target[ga]('data-src'));
            toLazyLoad++;
            lazyImages.push(e.target);
            e.target[a](lazyloaded, lazyloadedHandler);
            // giving 300ms to load image, otherwise firing lazyloadedHandler
            setTimeout(() => { 
                e.target[r](lazyloaded, lazyloadedHandler); 
                lazyloadedHandler();
            } , 300);
        } else {
            process.env.DEBUG && c(delta(),"duplicate lazy image", e.target[ga]('data-src'));
        }
    };
    dOrigAddEventListener(lazybeforeunveil, lazybeforeunveilHandler);

    if (!w._wpmnl) {
        wOrigAddEventListener(L, () => {
            const ls = lazysizes(w, d, Date, {
                expand: 10,
                expFactor: 1,
                hFac: 1,
                loadMode: 1,
                ricTimeout: 50,
                loadHidden: true,
                init: false,
            });
            lazybg(w, d, ls);
            ls.init();
        });
    }
    // at the moment this event fires some lazy images should be enqueued
    wOrigAddEventListener(L, lazyloadedHandler);

    const onFirstInteraction = (e) => {
        if (!firstInteractionFired) {
            process.env.DEBUG && c(delta(), separator, "firstInteraction");
            interactionEvents.forEach(event => d.body[r](event, onFirstInteraction, listenerOptions));
            wOrigRemoveEventListener(L, onFirstInteraction); // L = load
            d[r](lazybeforeunveil, lazybeforeunveilHandler);

            firstInteractionFired = true;
            if (!location.href.match(/wpmeteornopreload/)) {
                preload();
            }
            dispatcher.emit('fi');
            nextTick(iterate);
        }
    }

    dOrigAddEventListener(DCL, () =>
        interactionEvents.forEach(event => d.body[a](event, onFirstInteraction, listenerOptions))
    );

    if (_wpmeteor.rdelay <= 2000) {
        const callback = setTimeout.bind(N, onFirstInteraction, _wpmeteor.rdelay);
        dispatcher.on('i', () => {
            process.env.DEBUG && c(delta(), separator, "lazy images loaded");
            lazyImages = [];
            if (I in w) {
                process.env.DEBUG && w[I](() => c(delta(), separator, 'using ' + I + 'to initiate reorder'));
                w[I](callback);
            } else {
                callback();
            }
        }); // L - load
    }

    const synteticCick = e => {
        process.env.DEBUG && c(delta(), 'creating syntetic click event for', e);
        const event = new MouseEvent('click', {
            view: e.view,
            bubbles: true,
            cancelable: true
        });
        Object.defineProperty(event, 'target', { writable: false, value: e.target });
        return event;
    }

    const captureEvents = ['mouseover', 'mouseout', 'touchstart', 'touchend', 'click'];
    const capturedEvents = [];
    const captureEvent = e => {
        // let result = true;
        if (e.target && ('dispatchEvent' in e.target)) {
            // e.target.style.cursor = 'progress';
            process.env.DEBUG && c(delta(), 'captured', e.type, e.target);
            if (e.type === 'click') {
                e.preventDefault();
                e.stopPropagation();
                // result = false;
                capturedEvents.push(synteticCick(e));
            } else {
                capturedEvents.push(e);
            }
            e.target[sa](prefix + e.type, true);
        }
        // return result;
    }

    dispatcher.on('l', () => {
        captureEvents.forEach(name => wOrigRemoveEventListener(name, captureEvent));
        let e;
        while (e = capturedEvents.shift()) {
            var target = e.target;
            if (target[ga](prefix + 'touchstart') && target[ga](prefix + 'touchend') && !target[ga](prefix + 'click')) {
                target[ra](prefix + 'touchstart');
                target[ra](prefix + 'touchend');
                capturedEvents.push(synteticCick(e));
            }
            process.env.DEBUG && c(delta(), ' dispatching ' + e.type + ' to ', e.target);
            // e.target.style.removeProperty('cursor');
            target.dispatchEvent(e);
        }
    });

    captureEvents.forEach(name => wOrigAddEventListener(name, captureEvent));

    // Starts the monitoring
    /*
    observer.observe(d.documentElement, {
        attributes: true,
        childList: true,
        subtree: true
    });
    */

})(window,
    document,
    'addEventListener',
    'removeEventListener',
    'getAttribute',
    'setAttribute',
    'removeAttribute',
    'load',
    'error',
    ['mouseover', 'keydown', 'touchmove', 'touchend', 'wheel']);
