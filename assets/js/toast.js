function showToast(msg, type = 'success') {
    let container = document.getElementById('toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toast-container';
        container.className = 'fixed bottom-5 right-5 z-[9999] flex flex-col gap-3 w-full max-w-[350px]';
        document.body.appendChild(container);
    }

    const config = {
        success: {
            bg: 'bg-white',
            icon: 'fa-check-circle',
            iconColor: 'text-emerald-500',
            bar: 'bg-emerald-500',
            border: 'border-emerald-100',
            shadow: 'shadow-[0_15px_30px_-10px_rgba(16,185,129,0.3)]'
        },
        error: {
            bg: 'bg-white',
            icon: 'fa-circle-exclamation',
            iconColor: 'text-rose-500',
            bar: 'bg-rose-500',
            border: 'border-rose-100',
            shadow: 'shadow-[0_15px_30px_-10px_rgba(244,63,94,0.3)]'
        }
    };

    const style = config[type] || config.success;
    const toast = document.createElement('div');
    toast.className = `toast-card ${style.bg} ${style.border} ${style.shadow} border-2 rounded-2xl overflow-hidden relative`;

    toast.innerHTML = `
        <div class="p-4 flex items-center gap-4">
            <div class="bg-slate-50 flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center shadow-inner">
                <i class="fas ${style.icon} ${style.iconColor} text-xl"></i>
            </div>
            
            <div class="flex-grow">
                <p class="text-slate-800 font-black text-sm leading-tight">${msg}</p>
                <p class="${style.iconColor} text-[9px] uppercase tracking-widest font-black mt-1">${type}</p>
            </div>

            <button onclick="triggerToastExit(this.parentElement.parentElement)" class="text-slate-300 hover:text-slate-500 transition-colors">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
        <div class="absolute bottom-0 left-0 h-1 w-full bg-slate-50">
            <div class="toast-progress h-full ${style.bar}"></div>
        </div>
    `;

    container.appendChild(toast);

    // Automatic removal after 3 seconds
    setTimeout(() => {
        triggerToastExit(toast);
    }, 3000);
}

function triggerToastExit(toastElement) {
    if (!toastElement || toastElement.classList.contains('exit')) return;
    
    // Add the exit animation class
    toastElement.classList.add('exit');

    setTimeout(() => {
        toastElement.remove();
        const container = document.getElementById('toast-container');
        if (container && container.childNodes.length === 0) {
            container.remove();
        }
    }, 500);
}