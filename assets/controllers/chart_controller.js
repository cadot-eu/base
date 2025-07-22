import { Controller } from '@hotwired/stimulus';
import Chart from 'chart.js/auto';
import ChartDataLabels from 'chartjs-plugin-datalabels';

Chart.register(ChartDataLabels);

export default class extends Controller {
    static values = {
        values: Array,
        range: String,
    }

    connect() {
        const range = this.rangeValue;
        const isHour = range === 'hour';

        let labels = [];
        let data = [];

        // Détection des années/mois/jours uniques pour alléger les labels
        const getYear = p => p.slice(0, 4);
        const getMonth = p => p.slice(5, 7);
        const getDay = p => p.slice(8, 10);
        let periods = isHour ? this.valuesValue.map(e => e?.time || '') : this.valuesValue.map(e => e?.period || '');
        const years = [...new Set(periods.map(getYear))];
        const months = [...new Set(periods.map(getMonth))];
        const days = [...new Set(periods.map(getDay))];

        // Fonction pour alléger le label avec mois en français et jours abrégés pour la semaine
        const joursFrancais = ['dim.', 'lun.', 'mar.', 'mer.', 'jeu.', 'ven.', 'sam.'];
        const moisFrancais = ['janv.', 'févr.', 'mars', 'avr.', 'mai', 'juin', 'juil.', 'août', 'sept.', 'oct.', 'nov.', 'déc.'];
        function formatLabel(period) {
            if (range === 'week') {
                // Affiche le jour abrégé en français + heure
                const date = new Date(period);
                const jour = joursFrancais[date.getDay()];
                const heure = period.slice(11, 16); // HH:mm
                return `${jour} ${heure}`;
            }
            if (years.length === 1 && months.length === 1 && days.length === 1) {
                return period.slice(11, 16); // HH:mm
            } else if (years.length === 1 && months.length === 1) {
                return period.slice(8, 10) + ' ' + period.slice(11, 16); // dd HH:mm
            } else if (years.length === 1) {
                const mois = moisFrancais[parseInt(period.slice(5, 7), 10) - 1];
                return mois + ' ' + period.slice(8, 10) + ' ' + period.slice(11, 16); // mois jj HH:mm
            } else {
                const mois = moisFrancais[parseInt(period.slice(5, 7), 10) - 1];
                return period.slice(8, 10) + ' ' + mois + ' ' + period.slice(0, 4) + ' ' + period.slice(11, 16); // jj mois aaaa HH:mm
            }
        }

        if (isHour) {
            labels = this.valuesValue.map(e => formatLabel(e?.time || ''));
            data = this.valuesValue.map(e => parseFloat(e?.power_w) || 0);
        } else {
            labels = this.valuesValue.map(e => formatLabel(e?.period || ''));
            data = this.valuesValue.map(e => parseFloat(e?.total_kwh) || 0);
        }
        if (range === 'year') {
            labels = labels.map(label => label.slice(0, 7)); // retire heures, garde "YYYY-MM"
        }

        const ctx = this.element.querySelector("canvas");
        if (!ctx) return;

        const getNextRange = (r) => {
            if (r === 'year') return 'month';
            if (r === 'month' || r === 'week') return 'day';
            return null;
        };
        const nextRange = getNextRange(range);

        const generateLink = (label) => {
            let detail = label;
            if (nextRange === 'day') {
                // garde uniquement la date sans l'heure
                detail = label.split(' ')[0];
            } else if (nextRange === 'month') {
                detail = label.slice(0, 7);
            } else if (nextRange === 'year') {
                detail = label.slice(0, 4);
            }
            return `http://localhost/graphique/ballon?device=ballon&range=${nextRange}&detail=${encodeURIComponent(detail)}`;
        };

        // Récupère le prix du kWh depuis la query string
        const urlParams = new URLSearchParams(window.location.search);
        let prixKwh = parseFloat(urlParams.get('prix_kwh'));
        if (isNaN(prixKwh)) {
            prixKwh = 0.25;
        }

        new Chart(ctx, {
            type: isHour ? 'line' : 'bar',
            data: {
                labels,
                datasets: [{
                    label: isHour ? 'Puissance (W)' : 'Consommation (kWh)',
                    data,
                    backgroundColor: isHour
                        ? 'rgba(75, 192, 192, 0.2)'
                        : 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgb(75, 192, 192)',
                    borderWidth: 2,
                    tension: 0.3,
                    fill: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        enabled: false // Désactive tous les tooltips natifs et custom
                    },
                    datalabels: {
                        display: !isHour,
                        anchor: 'start', // change 'end' -> 'start' pour placer au-dessus de la colonne
                        align: 'end',    // change 'top' -> 'end' pour placer encore plus haut
                        formatter: function (value, context) {
                            return !isHour ? `${(value * prixKwh).toFixed(2)} €` : '';
                        },
                        color: '#28a745',
                        font: {
                            weight: 'bold',
                            size: 14
                        },
                        clip: false,
                        padding: {
                            top: 0,
                            bottom: 12 // optionnel, ajuste si besoin
                        },
                        listeners: {
                            enter: function (context) {
                                // Affiche un tooltip personnalisé au-dessus du prix
                                const chart = context.chart;
                                const index = context.dataIndex;
                                const label = labels[index];
                                const value = data[index];
                                const prix = (value * prixKwh).toFixed(2);
                                let tooltipEl = document.getElementById('chartjs-tooltip');
                                if (tooltipEl) tooltipEl.remove();
                                tooltipEl = document.createElement('div');
                                tooltipEl.id = 'chartjs-tooltip';
                                tooltipEl.style.position = 'absolute';
                                tooltipEl.style.background = 'rgba(255,255,255,0.98)';
                                tooltipEl.style.border = '1px solid #ccc';
                                tooltipEl.style.borderRadius = '6px';
                                tooltipEl.style.padding = '12px 16px';
                                tooltipEl.style.pointerEvents = 'auto';
                                tooltipEl.style.zIndex = '9999';
                                tooltipEl.style.boxShadow = '0 4px 12px rgba(0,0,0,0.2)';
                                tooltipEl.style.fontSize = '14px';
                                tooltipEl.style.lineHeight = '1.4';
                                tooltipEl.innerHTML = `<div><strong>${label}</strong><br>${value.toFixed(3)} kWh<br><span style="color:#28a745;font-weight:bold;">Prix : ${prix} €</span></div>`;
                                // Positionne le tooltip au-dessus du datalabel
                                const meta = chart.getDatasetMeta(0);
                                const bar = meta.data[index];
                                const rect = chart.canvas.getBoundingClientRect();
                                const pos = bar.getProps(['x', 'y'], true);
                                tooltipEl.style.left = rect.left + window.pageXOffset + pos.x + 'px';
                                tooltipEl.style.top = rect.top + window.pageYOffset + pos.y - 40 + 'px';
                                document.body.appendChild(tooltipEl);

                                // Gestion du délai de fermeture
                                if (window._chartjsTooltipTimeout) {
                                    clearTimeout(window._chartjsTooltipTimeout);
                                    window._chartjsTooltipTimeout = null;
                                }

                                // Garde le tooltip affiché tant que la souris est sur le label ou le tooltip
                                let overTooltip = false;
                                let overLabel = true;
                                let hideTimeout = null;

                                function hideTooltip() {
                                    let el = document.getElementById('chartjs-tooltip');
                                    if (el) el.remove();
                                }

                                function scheduleHide() {
                                    if (hideTimeout) clearTimeout(hideTimeout);
                                    hideTimeout = setTimeout(() => {
                                        if (!overTooltip && !overLabel) {
                                            hideTooltip();
                                        }
                                    }, 1000);
                                }

                                tooltipEl.addEventListener('mouseenter', () => {
                                    overTooltip = true;
                                    if (hideTimeout) clearTimeout(hideTimeout);
                                });
                                tooltipEl.addEventListener('mouseleave', () => {
                                    overTooltip = false;
                                    scheduleHide();
                                });

                                // Nettoyage des anciens listeners pour éviter les doublons
                                if (context.element._mouseleaveListener) {
                                    context.element.removeEventListener('mouseleave', context.element._mouseleaveListener);
                                }
                                if (context.element._mouseenterListener) {
                                    context.element.removeEventListener('mouseenter', context.element._mouseenterListener);
                                }

                                context.element._mouseleaveListener = () => {
                                    overLabel = false;
                                    scheduleHide();
                                };
                                context.element._mouseenterListener = () => {
                                    overLabel = true;
                                    if (hideTimeout) clearTimeout(hideTimeout);
                                };

                                context.element.addEventListener('mouseleave', context.element._mouseleaveListener);
                                context.element.addEventListener('mouseenter', context.element._mouseenterListener);
                            },
                            leave: function (context) {
                                // La gestion du délai est faite dans les mouseleave ci-dessus
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: isHour ? 'Watts' : 'kWh'
                        },
                        ticks: {
                            callback: value => isHour ? `${value} W` : `${value.toFixed(3)} kWh`
                        }
                    },
                    x: {
                        ticks: {
                            maxRotation: 90,
                            minRotation: 45,
                            callback: function (value) {
                                const label = this.getLabelForValue(value);
                                if (nextRange) {
                                    return `${label}`;
                                }
                                return label;
                            }
                        }
                    }
                },
                onClick: (e, elements) => {
                    if (!elements.length || !nextRange) return;
                    const index = elements[0].index;
                    const label = labels[index];
                    const url = generateLink(label);
                    window.location.href = url;
                }
            }
        });

        // Ajout du prix total et du total kWh au centre du graphique
        if (!isHour) {
            // Ajoute un log pour vérifier le contenu des données
            console.log('Données kWh pour le total:', data);

            const totalKwh = data.reduce((a, b) => a + b, 0);
            const totalPrix = (totalKwh * prixKwh).toFixed(2);
            // Crée ou met à jour un overlay centré
            let overlay = document.getElementById('chart-center-overlay');
            if (!overlay) {
                overlay = document.createElement('div');
                overlay.id = 'chart-center-overlay';
                overlay.style.position = 'absolute';
                overlay.style.left = '50%';
                overlay.style.top = '50%';
                overlay.style.transform = 'translate(-50%, -50%)';
                overlay.style.zIndex = '10';
                overlay.style.pointerEvents = 'none';
                overlay.style.background = 'rgba(255,255,255,0.85)';
                overlay.style.borderRadius = '12px';
                overlay.style.padding = '18px 32px';
                overlay.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
                overlay.style.fontSize = '1.3rem';
                overlay.style.fontWeight = 'bold';
                overlay.style.color = '#28a745';
                ctx.parentElement.style.position = 'relative';
                ctx.parentElement.appendChild(overlay);
            }
            overlay.innerHTML = `<div style='text-align:center;'>Total : ${totalKwh.toFixed(3)} kWh<br>Prix : ${totalPrix} €</div>`;
        }
    }
}