import axios from 'axios';

window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.withCredentials = true;

// Set base URL from environment or default to current origin
window.axios.defaults.baseURL = import.meta.env.VITE_BACKEND_URL || window.location.origin;
