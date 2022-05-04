import { Controller } from '@hotwired/stimulus'
import BigPicture from "bigpicture";

export default class extends Controller {

    static values = {
        options: Object
    }
    connect(event) {
        //on change l'icone de l'élément
        this.element.style.cursor = "ne-resize"
        this.element.onclick = function () {
            let source = this.src
            if (this.getAttribute('bPsrc')) //on remplace src si on a un attribu bPsrc
                source = this.getAttribute('bPsrc');
            BigPicture({
                el: this,
                imgSrc: source,
            });
        };

    }

}
