define(function(require, exports, module) {
    var $ = require("jquery"),
        bootstrap = require("bootstrap");

    exports.ready = function() {
        $(document).ready(function() {
            $("[data-toggle='tooltip']").tooltip();
        });
    }
});
