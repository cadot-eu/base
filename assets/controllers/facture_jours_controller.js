import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["jourCheckbox"]
    static values = { factureId: Number }

    toggleJour(event) {
        const checkbox = event.currentTarget
        const jourId = checkbox.value
        const isChecked = checkbox.checked

        // Désactiver temporairement la checkbox pendant la requête
        checkbox.disabled = true

        const action = isChecked ? 'add' : 'remove'
        const url = `/dashboard/facture/${this.factureIdValue}/jour/${jourId}/${action}`

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`)
                }
                return response.json()
            })
            .then(data => {
                if (data.success) {
                    // Succès : laisser la checkbox dans son nouvel état
                    console.log(`Jour ${jourId} ${action === 'add' ? 'ajouté à' : 'retiré de'} la facture ${this.factureIdValue}`)

                    // Si le jour a été retiré, on peut optionnellement masquer la ligne
                    if (action === 'remove') {
                        checkbox.closest('.form-check').style.opacity = '0.5'
                    }
                } else {
                    // Erreur : remettre la checkbox dans son état précédent
                    checkbox.checked = !isChecked
                    alert(data.message || 'Erreur lors de la modification')
                }
            })
            .catch(error => {
                console.error('Erreur:', error)
                // Remettre la checkbox dans son état précédent
                checkbox.checked = !isChecked
                alert('Erreur de connexion. Veuillez réessayer.')
            })
            .finally(() => {
                // Réactiver la checkbox
                checkbox.disabled = false
            })
    }
}
