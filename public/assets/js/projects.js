document.addEventListener('DOMContentLoaded', function() {

    const toggleButtons = document.querySelectorAll('.btn-toggle-status');

    toggleButtons.forEach(button => {
        button.addEventListener('click', async function(event) {
            event.preventDefault();
            event.stopPropagation();

            const projectId = this.dataset.id;
            const url = this.dataset.url;
            const badge = document.getElementById(`status-badge-${projectId}`);

            this.disabled = true;
            badge.style.opacity = '0.5';
            const originalText = badge.innerText;
            badge.innerText = '...';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ action: 'toggle' })
                });

                if (!response.ok) {
                    throw new Error('Erro na comunicação com o servidor');
                }

                const data = await response.json();

                if (data.success) {
                    if (data.new_status === 'finished') {
                        badge.className = 'badge rounded-pill bg-success';
                    } else {
                        badge.className = 'badge rounded-pill bg-secondary';
                    }
                    badge.innerText = data.label;
                } else {
                    alert('Erro: ' + data.message);
                    badge.innerText = originalText;
                }

            } catch (error) {
                console.error('Erro Ajax:', error);
                alert('Ocorreu um erro ao tentar alterar o status.');
                badge.innerText = originalText;
            } finally {
                badge.style.opacity = '1';
                this.disabled = false;
            }
        });
    });
});