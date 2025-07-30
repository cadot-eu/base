import { Controller } from "@hotwired/stimulus"
import * as bootstrap from 'bootstrap'

export default class extends Controller {
    static targets = ["modalBody"]
    static values = { url: String }

    connect() {
        this.element.addEventListener('show.bs.modal', this.loadContent.bind(this))
        this.element.addEventListener('hidden.bs.modal', this.onModalHidden.bind(this))
    }

    loadContent(event) {
        const modalBody = this.element.querySelector('.modal-body')

        if (modalBody && this.urlValue) {
            modalBody.innerHTML = `
                <div class="d-flex justify-content-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Chargement...</span>
                    </div>
                </div>
            `

            fetch(this.urlValue)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`)
                    }
                    return response.text()
                })
                .then(html => {
                    modalBody.innerHTML = html
                    this.setupFormHandlers()
                })
                .catch(error => {
                    console.error('Erreur lors du chargement du contenu:', error)
                    modalBody.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <h6>Erreur de chargement</h6>
                            <p>Impossible de charger le contenu. Veuillez réessayer.</p>
                            <small class="text-muted">Erreur: ${error.message}</small>
                        </div>
                    `
                })
        }
    }

    setupFormHandlers() {
        const forms = this.element.querySelectorAll('form')
        forms.forEach(form => {
            form.addEventListener('submit', (event) => {
                event.preventDefault()
                sessionStorage.setItem('formSubmitted', 'true')
                const formData = new FormData(form)
                fetch(form.action, {
                    method: form.method,
                    body: formData,
                    credentials: 'same-origin'
                })
                    .then(response => {
                        if (response.ok) {
                            const modal = bootstrap.Modal.getInstance(this.element)
                            if (modal) {
                                modal.hide()
                            }
                        } else {
                            throw new Error(`HTTP error! status: ${response.status}`)
                        }
                    })
                    .catch(error => {
                        const modalBody = this.element.querySelector('.modal-body')
                        modalBody.innerHTML = `
                        <div class="alert alert-danger" role="alert">
                            <h6>Erreur lors de la soumission</h6>
                            <p>Une erreur est survenue. Veuillez réessayer.</p>
                            <small class="text-muted">Erreur: ${error.message}</small>
                        </div>
                    `
                    })
            })
        })
    }

    onModalHidden(event) {
        if (sessionStorage.getItem('formSubmitted') === 'true') {
            sessionStorage.removeItem('formSubmitted')
            window.location.reload()
        }
    }

    disconnect() {
        this.element.removeEventListener('show.bs.modal', this.loadContent.bind(this))
        this.element.removeEventListener('hidden.bs.modal', this.onModalHidden.bind(this))
    }
}
