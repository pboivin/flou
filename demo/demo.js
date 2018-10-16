if (typeof window.handleFlouImageLoaded === "undefined") {
    window.handleFlouImageLoaded = function(image) {
        image.parentElement.classList.add("is-loaded");
    }
}
