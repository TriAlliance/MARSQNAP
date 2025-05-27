
(function () {
    //This method NOT support IE8
    // ------------------------------
    // HTML Template:
    // ------------------------------
    // <div class="item">
    //     <p class="copy-area">this it copy-area for test</p>
    //     <input type="text" value="Copy This Text" class="copy-area">
    //     <button type="button" class="btn-copy">copy</button>
    // </div>
    // ------------------------------
    document.addEventListener("DOMContentLoaded", function () {
        var items = document.querySelectorAll('.item');
        for (var i = 0; i < items.length; ++i) {
            copyToClipBoard(items[i]);
        }

        function copyToClipBoard(item) {
            var btnCopy = item.querySelector('.btn-copy');
            if (btnCopy === null) {
                return;
            }
            btnCopy.addEventListener('click', function (event) {
                var copyArea = item.querySelector('.copy-area');
                var range = document.createRange();
                range.selectNode(copyArea);
                window.getSelection().removeAllRanges();
                window.getSelection().addRange(range);
                try {
                    var copyStatus = document.execCommand('copy');
                    // var msg = copyStatus ? 'copied' : 'failed';
                    // console.log(msg);
                } catch (error) {
                    // console.log('Oops!, unable to copy');
                }
                window.getSelection().removeAllRanges();
            });
        }
    });
})();

function DeleteQnapLog() {
    var params = {
        'secret_key': qnap_backups.secret_key
    };
    $ = jQuery;
    $.ajax({
        url: qnap_backups.ajax.url,
        type: 'POST',
        dataType: 'json',
        data: params
    }).always(function () {
        location.reload();
    });
}

// params = { 'type': 'error', 'message': 'hello message', 'title': 'test title' };
// jQuery(document).trigger('qnap-import-status', params); // Show modal
// console.log(Export);