import { Controller } from '@hotwired/stimulus'
import BuildEditor from '/assets/ckeditor/build/ckeditor';
/* ---------------- transformation des textareas en fckeditor --------------- */
//fckeditor se modifie ici à partir de la version téléchargé de l'online builder
//pour avoir la version complète de la toolbar et des plugins
//utiliser /assets/ckeditor/src/ckeditor.js
export default class extends Controller {

    static values = {
        toolbar: String
    }
    connect() {
        let uniqid = (new Date()).getTime();
        this.element.id = uniqid
        if (this.toolbarValue == 'simple')
            this.editor = simple(this.element);
        else
            BuildEditor.create(this.element, {
                language: {
                    textPartLanguage: [
                        { title: 'French', languageCode: 'fr' },
                        { title: 'Anglais', languageCode: 'en' }
                    ]
                },
                simpleUpload: {
                    // The URL that the images are uploaded to.
                    uploadUrl: "/upload/toto",

                    // Enable the XMLHttpRequest.withCredentials property if required.
                    withCredentials: false,

                    // Headers sent along with the XMLHttpRequest to the upload server.
                    headers: {
                        "X-CSRF-TOKEN": "CSFR-Token",
                        Authorization: "Bearer [JSON Web Token]"
                    }
                },
            })

    }
    disconnect() {

    }
}

function simple(e) {
    return BuildEditor.create(e,
        {
            restrictedEditing: {
                allowedCommands: ['highlight',
                    'specialCharacters',],
                allowedAttributes: ['highlight',
                    'specialCharacters']
            },
            heading: {
                options: [
                    { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                    { model: 'heading1', view: 'h1', title: 'Heading 1', class: 'ck-heading_heading1' },
                    { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                    { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' },
                    { model: 'heading4', view: 'h4', title: 'Heading 4', class: 'ck-heading_heading4' },
                    { model: 'heading5', view: 'h5', title: 'Heading 5', class: 'ck-heading_heading5' },
                ]
            },
            highlight: {
                options: [
                    {
                        model: 'Marker',
                        class: '',
                        title: 'Marqueur',
                        type: 'marker'
                    }
                ]
            },
            toolbar: {
                items: [
                    'highlight',
                    'specialCharacters'
                ]
            },
            language: 'fr'
        })
        .then(editor => {
            editor.setData(e.value)
            editor.editing.view.document.on('clipboardInput', (evt, data) => {
                data.content = editor.data.htmlProcessor.toView(data.dataTransfer.getData('text/plain'));
            });
            editor.model.document.on('change:data', () => {
                e.value = editor.getData().replace(/<p>+/, "").replace(/<\/p>+$/, "");
            });
        })
        .catch(error => {
            console.error(error.stack);
        });
}




