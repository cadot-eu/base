import { Controller } from "@hotwired/stimulus"

// Contrôleur générique pour gérer les associations dynamiques avec des checkboxes
export default class extends Controller {
    static values = {
        parentId: String,  // ID de l'entité parent 
        field: String,     // Nom du champ (ex: "jours")
        csrfToken: String  // Token CSRF pour la sécurité
    }
    static targets = ["itemCheckbox"]

    connect() {
        console.log(`Dynamic association controller connected for field ${this.fieldValue}`)
    }

    toggleItem(event) {
        const checkbox = event.target
        const itemId = checkbox.value
        const isChecked = checkbox.checked

        // Construction dynamique de l'URL basée sur le nom du champ
        const action = isChecked ? 'add' : 'remove'
        const url = `/dashboard/${action}-${this.fieldValue.toLowerCase()}`

        const data = new FormData()
        data.append('parentId', this.parentIdValue)
        data.append('itemId', itemId)
        data.append('_token', this.csrfTokenValue)

        fetch(url, {
            method: 'POST',
            body: data,
            credentials: 'same-origin',  // Important: inclut les cookies de session
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => {
                console.log('Response status:', response.status)
                console.log('Response headers:', response.headers.get('content-type'))

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }

                const contentType = response.headers.get('content-type')
                if (!contentType || !contentType.includes('application/json')) {
                    console.error('Expected JSON, got:', contentType)
                    return response.text().then(text => {
                        console.error('Response body:', text)
                        throw new Error('Expected JSON response, got: ' + contentType)
                    })
                }

                return response.json()
            })
            .then(data => {
                if (data.success) {
                    console.log(`Item ${itemId} ${action}ed successfully for field ${this.fieldValue}`)
                    // Mettre à jour l'interface si nécessaire
                    this.updateUI(checkbox, isChecked)
                } else {
                    console.error('Error:', data.message)
                    // Revenir à l'état précédent en cas d'erreur
                    checkbox.checked = !isChecked
                }
            })
            .catch(error => {
                console.error('Error:', error)
                // Revenir à l'état précédent en cas d'erreur
                checkbox.checked = !isChecked
            })
    }

    updateUI(checkbox, isChecked) {
        // Animation visuelle pour le feedback utilisateur
        const label = checkbox.nextElementSibling
        if (label) {
            if (isChecked) {
                label.classList.remove('text-muted')
                label.style.opacity = '1'
            } else {
                label.classList.add('text-muted')
                label.style.opacity = '0.6'
            }
        }
    }
}
