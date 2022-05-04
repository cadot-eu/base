
/* This is a JavaScript function that is being called on each image in the document. It is checking to
see if the alt attribute is undefined. If it is, it sets the alt attribute to the filename of the
image. */
$("img").each(function () {
    if ($(this).attr('alt') == undefined)
        $(this).attr('alt', $(this).attr('src').split('/').reverse()[0].split('.')[0].replace('-', ' ').replace('_', ' '))
})