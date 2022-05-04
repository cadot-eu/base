import { Controller } from '@hotwired/stimulus';
var checkboxes;
export default class extends Controller {

    connect() {
        //this.checkboxes = this.checkboxes.bind(this)

        this.checkboxes().forEach(element => {
            element.addEventListener("click", this.refresh)
        });

    }
    refresh(el) {
        checkboxes.forEach(element => {
            if (element != el.target)
                element.checked = false;
        })
    }
    //return array of checkboxes
    checkboxes() {
        var tab = []
        var inputs = this.element.getElementsByTagName("input");
        for (var i = 0; i < inputs.length; i++) {
            if (inputs[i].type == 'checkbox')
                tab.push(inputs[i])
        }
        checkboxes = tab;
        return tab;
    }
}

