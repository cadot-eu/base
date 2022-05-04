//copier url de l'adresse de la page
import ClipboardJS from "clipboard";
let clipboard = new ClipboardJS('#copierUrl');
clipboard.on('success', function (e) {
    let extitle = $('#copierUrl').attr('title')
    $('#copierUrl').attr('title', 'Url copié dans votre presse-paier')
    $('#copierUrl').tooltip('show');
    setTimeout(function () {
        $('#copierUrl').attr('title', extitle);
        $('#copierUrl').tooltip('dispose');
    }, 3000);


});