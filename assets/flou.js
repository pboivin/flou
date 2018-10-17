(function (root, factory) {
    if (typeof module === 'object' && module.exports) { module.exports = factory(); }
    else if (typeof define === 'function' && define.amd) { define([], factory); }
    else { root.Flou = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
    var Flou = {
        IMAGE_LOADED_DELAY: 500,

        handleImageLoaded: function(src) {
            var image = this;
            setTimeout(function(){
                image.setAttribute("src", src);
                image.classList.add("flou-image-loaded");
            }, Flou.IMAGE_LOADED_DELAY);
        },

        loadImages: function() {
            var images = document.querySelectorAll(".flou-image");

            [].forEach.call(images, function(image) {
                var originalSrc = image.getAttribute("data-original");
                var originalImage = new Image();
                originalImage.onload = Flou.handleImageLoaded.bind(image, originalSrc);
                originalImage.src = originalSrc;
            });
        }
    };
    return Flou;
}));
