    <!-- Footer -->
    <footer class="footer footer-center p-10 bg-base-200 text-base-content rounded mt-8">
        <div>
            <div class="grid grid-flow-col gap-4">
                <i class="fab fa-instagram text-2xl text-primary"></i>
                <i class="fas fa-robot text-2xl text-secondary"></i>
                <i class="fas fa-heart text-2xl text-accent"></i>
            </div>
            <p class="font-bold">
                Instagram Bot @fatima.escritora
                <br>
                <span class="text-sm font-normal">Promovendo literatura infantil, inclusão e educação</span>
            </p>
            <p class="text-sm">
                Desenvolvido por <span class="font-semibold text-primary">Tria Inova Simples (I.S.)</span>
                <br>
                CNPJ: 60.967.428/0001-30 | Imperatriz - MA
            </p>
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt text-green-500 mr-1"></i>
                Sistema seguro e automatizado para crescimento orgânico
            </p>
        </div>
    </footer>

    <!-- Toast Container -->
    <div id="toast-container" class="toast toast-top toast-end z-50"></div>

    <!-- Scripts globais -->
    <script>
        // Função para mostrar toast
        function showToast(message, type = 'info') {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            const typeClasses = {
                'success': 'alert-success',
                'error': 'alert-error',
                'warning': 'alert-warning',
                'info': 'alert-info'
            };
            
            const icons = {
                'success': 'fa-check-circle',
                'error': 'fa-exclamation-circle',
                'warning': 'fa-exclamation-triangle',
                'info': 'fa-info-circle'
            };
            
            toast.className = `alert ${typeClasses[type]} shadow-lg mb-2`;
            toast.innerHTML = `
                <div>
                    <i class="fas ${icons[type]}"></i>
                    <span>${message}</span>
                </div>
            `;
            
            container.appendChild(toast);
            
            // Remove toast após 5 segundos
            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => {
                    container.removeChild(toast);
                }, 300);
            }, 5000);
        }

        // Função para formatar números
        function formatNumber(num) {
            return new Intl.NumberFormat('pt-BR').format(num);
        }

        // Função para formatar data/hora
        function formatDateTime(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleString('pt-BR');
        }

        // Função para calcular tempo relativo
        function timeAgo(dateStr) {
            const date = new Date(dateStr);
            const now = new Date();
            const diffInSeconds = (now - date) / 1000;
            
            if (diffInSeconds < 60) {
                return 'agora mesmo';
            } else if (diffInSeconds < 3600) {
                const minutes = Math.floor(diffInSeconds / 60);
                return `${minutes} min atrás`;
            } else if (diffInSeconds < 86400) {
                const hours = Math.floor(diffInSeconds / 3600);
                return `${hours}h atrás`;
            } else {
                const days = Math.floor(diffInSeconds / 86400);
                return `${days} dias atrás`;
            }
        }

        // Auto-refresh da página a cada 5 minutos (opcional)
        // setInterval(() => {
        //     window.location.reload();
        // }, 300000);

        // Monitora conexão com a internet
        window.addEventListener('online', () => {
            showToast('Conexão restaurada!', 'success');
        });

        window.addEventListener('offline', () => {
            showToast('Conexão perdida!', 'warning');
        });
    </script>
</body>
</html>
