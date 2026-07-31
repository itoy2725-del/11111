import './bootstrap';
import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Global fetch helper
window.apiFetch = async (url, options = {}) => {
    const defaultHeaders = {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    options.headers = { ...defaultHeaders, ...options.headers };
    options.credentials = 'same-origin';
    
    try {
        const response = await fetch(url, options);
        if (!response.ok) {
            throw response;
        }
        return await response.json();
    } catch (error) {
        throw error;
    }
};

document.addEventListener('alpine:init', () => {
    Alpine.store('toast', {
        show: false,
        message: '',
        type: 'success', // success, error, warning, info
        
        display(msg, type = 'success', duration = 3000) {
            this.message = msg;
            this.type = type;
            this.show = true;
            setTimeout(() => {
                this.show = false;
            }, duration);
        }
    });

    Alpine.store('drawer', {
        open: false,
        leadId: null,
        leadData: null,
        loading: false,
        
        async openDrawer(id) {
            this.leadId = id;
            this.open = true;
            this.loading = true;
            
            try {
                // Fetch lead summary data
                // const data = await apiFetch(`/leads/${id}/summary`);
                // this.leadData = data;
                
                // Simulate for now
                this.leadData = { id: id, name: 'Loading...' };
            } catch (err) {
                Alpine.store('toast').display('Lead bilgileri yüklenemedi.', 'error');
            } finally {
                this.loading = false;
            }
        },
        closeDrawer() {
            this.open = false;
            this.leadId = null;
            this.leadData = null;
        }
    });

    Alpine.store('modal', {
        open: false,
        title: '',
        content: '',
        action: null, // function to run on confirm
        
        openModal(opts) {
            this.title = opts.title || 'Onay';
            this.content = opts.content || '';
            this.action = opts.action || null;
            this.open = true;
        },
        closeModal() {
            this.open = false;
        },
        confirm() {
            if (typeof this.action === 'function') {
                this.action();
            }
            this.closeModal();
        }
    });

    Alpine.store('sidebar', {
        collapsed: localStorage.getItem('sidebar_collapsed') === 'true',
        
        toggle() {
            this.collapsed = !this.collapsed;
            localStorage.setItem('sidebar_collapsed', this.collapsed);
        }
    });
});

Alpine.start();
