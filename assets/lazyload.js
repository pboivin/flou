/**
 * vanilla-lazyload API reference:
 * https://github.com/verlok/vanilla-lazyload#options
 */

document.addEventListener('DOMContentLoaded', () => {
    const lazyLoad = new LazyLoad({
        elements_selector: '.lazyload',

        // called whenever an element finishes loading
        callback_loaded: (el) => {
            const wrapper = el.closest('.lazyload-wrapper')
            if (wrapper) {
                wrapper.classList.add('loaded')
            }
        },

        // called whenever an element starts loading:
        // callback_loading: (el) => {}

        // called whenever an element triggers an error:
        // callback_error: (el) => {}

        // called whenever an element enters the viewport
        // callback_enter: (el) => {}

        // called whenever an element exits the viewport
        // callback_exit: (el) => {}

        // called when there are no more elements to load and all elements have been downloaded:
        // callback_finish: () => {}
    })
})
