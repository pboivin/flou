(function (root, factory) {
    if (typeof module === 'object' && module.exports) { module.exports = factory(); }
    else if (typeof define === 'function' && define.amd) { define([], factory); }
    else { root.Flou = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
    var Flou = {
        imageClass: "flou-image",
        imageLoadedClass: "flou-image-loaded",
        imageLoadedDelay: 500,

        handleImageLoaded: function(src) {
            var image = this;
            setTimeout(function(){
                image.setAttribute("src", src);
                image.classList.add(Flou.imageLoadedClass);
            }, Flou.imageLoadedDelay);
        },

        loadImage: function(imageElement) {
            var originalSrc = imageElement.getAttribute("data-original");
            var originalImage = new Image();
            originalImage.onload = Flou.handleImageLoaded.bind(imageElement, originalSrc);
            originalImage.src = originalSrc;
        },

        loadAllImages: function() {
            var selector = "." + Flou.imageClass + ":not(." + Flou.imageLoadedClass + ")";
            var images = document.querySelectorAll(selector);
            [].forEach.call(images, Flou.loadImage);
        }
    };
    return Flou;
}));
