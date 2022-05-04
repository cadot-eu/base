import { Controller } from '@hotwired/stimulus';
import { searchImages } from 'pixabay-api';
import { useDebounce } from 'stimulus-use';

export default class extends Controller {
    static values = {
        url: String,
    }

    static targets = ['result'];
    static debounces = ['search'];

    connect() {
        useDebounce(this);
    }

    onSearchInput(event) {
        this.search(event.currentTarget.value);
    }

    async search(query) {
        const params = new URLSearchParams({
            q: query,
            preview: 1,
        });
        const response = await fetch(`${this.urlValue}?${params.toString()}`);

        this.resultTarget.innerHTML = await response.text();
    }


}
