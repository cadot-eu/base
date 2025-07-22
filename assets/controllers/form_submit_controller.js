import { Controller } from "@hotwired/stimulus";

export default class extends Controller {
    submit(event) {
        this.element.submit();
    }

    connect() {
        this.rangeTarget = this.element.querySelector('#range');
        this.startDateTarget = this.element.querySelector('#start_date');
        this.endDateTarget = this.element.querySelector('#end_date');
    }

    changeRange(event) {
        const period = event.target.value;
        const now = new Date();
        let start = new Date(now);
        let end = new Date(now);

        switch (period) {
            case 'hour':
                start.setHours(now.getHours(), 0, 0, 0);
                end.setHours(now.getHours(), 59, 59, 999);
                break;
            case 'day':
                start.setHours(0, 0, 0, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'week':
                const day = now.getDay();
                const diff = now.getDate() - day + (day === 0 ? -6 : 1); // lundi début semaine
                start = new Date(now.setDate(diff));
                start.setHours(0, 0, 0, 0);
                end = new Date(start);
                end.setDate(start.getDate() + 6);
                end.setHours(23, 59, 59, 999);
                break;
            case 'month':
                start = new Date(now.getFullYear(), now.getMonth(), 1);
                end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
                end.setHours(23, 59, 59, 999);
                break;
            case 'year':
                start = new Date(now.getFullYear(), 0, 1);
                end = new Date(now.getFullYear(), 11, 31);
                end.setHours(23, 59, 59, 999);
                break;
        }
        this.startDateTarget.value = start.toISOString().slice(0, 10);
        this.endDateTarget.value = end.toISOString().slice(0, 10);
        this.element.submit();
    }

    submitDate(event) {
        this.rangeTarget.value = 'custom';
        this.element.submit();
    }
}
