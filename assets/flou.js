(function (root, factory) {
    if (typeof module === 'object' && module.exports) { module.exports = factory(); }
    else if (typeof define === 'function' && define.amd) { define([], factory); }
    else { root.Flou = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
    var Flou = {
        imageClass: "flou-image",
        imageLoadedClass: "flou-image-loaded",
        imageLoadedDelay: 500,

        handleImageLoaded: function(imageElement, originalSrc) {
            setTimeout(function(){
                imageElement.setAttribute("src", originalSrc);
                imageElement.classList.add(Flou.imageLoadedClass);
            }, Flou.imageLoadedDelay);
        },

        loadImage: function(imageElement) {
            var originalSrc = imageElement.getAttribute("data-original");
            var originalImageLoader = new Image();
            originalImageLoader.onload = Flou.handleImageLoaded.bind(this, imageElement, originalSrc);
            originalImageLoader.src = originalSrc;
        },

        loadAllImages: function() {
            var selector = "." + Flou.imageClass + ":not(." + Flou.imageLoadedClass + ")";
            var images = document.querySelectorAll(selector);
            [].forEach.call(images, Flou.loadImage);
        }
    };
    return Flou;
}));
