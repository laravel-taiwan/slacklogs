var swal = require('sweetalert');

var DialogMixin = {
    warn: function(description, callback) {
        swal({
            title: 'Warning!',
            type: 'warning',
            text: description
        }, callback);
    }
};

module.exports = DialogMixin;
