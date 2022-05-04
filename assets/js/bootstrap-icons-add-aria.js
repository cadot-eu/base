
$("i[class^='bi-']").each(function () {
    let cs;
    $(this).prop('classList').forEach(element => {
        if (element.slice(0, 3) == 'bi-') {
            cs = element;
            return false;
        }
    });

    $(this).attr('role', 'img');
    $(this).attr('aria-label', cs.slice(3).replace('-', ' '))
})