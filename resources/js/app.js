import './bootstrap';

// const app = new Vue({
//     el: '#app',
//     data: {
//         routers: [],
//         form: {
//             name: '',
//             host: '',
//             username: '',
//             password: '',
//         }
//     },
//     mounted() {
//         this.fetchRouters();

//         // Listen for WebSocket updates
//         Echo.channel('router-status')
//             .listen('RouterStatusUpdated', (data) => {
//                 const index = this.routers.findIndex(r => r.id === data.router.id);
//                 if (index !== -1) {
//                     this.routers.splice(index, 1, data.router);
//                 }
//             });
//     },
//     methods: {
//         fetchRouters() {
//             axios.get('/operator/router/view').then(response => {
//                 this.routers = response.data;
//             });
//         },
//         connectRouter() {
//             axios.post('/operator/router/connect', this.form)
//                 .then(response => {
//                     this.routers.push(response.data.router);
//                     this.form = { name: '', host: '', username: '', password: '' };
//                     alert('Router connected!');
//                 })
//                 .catch(error => {
//                     alert('Error: ' + error.response.data.message);
//                 });
//         }
//     }
// });
