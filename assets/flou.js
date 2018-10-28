(function (root, factory) {
    if (typeof module === 'object' && module.exports) { module.exports = factory(); }
    else if (typeof define === 'function' && define.amd) { define([], factory); }
    else { root.Flou = factory(); }
}(typeof self !== 'undefined' ? self : this, function () {
    var Flou = {
        image_class: "flou-image",
        image_loaded_class: "flou-image-loaded",
        image_loaded_delay: 500,

        handleImageLoaded: function(src) {
            var image = this;
            setTimeout(function(){
                image.setAttribute("src", src);
                image.classList.add(Flou.image_loaded_class);
            }, Flou.image_loaded_delay);
        },

        loadImages: function() {
            var selector = "." + Flou.image_class + ":not(." + Flou.image_loaded_class + ")";
            var images = document.querySelectorAll(selector);

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
