import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    // auth: {
	// 	headers: {
    //          Authorization: 'Bearer ' + window.localStorage.getItem('token')
	// 	},
	// },
	// authEndpoint: "http://127.0.0.1:8000/api/broadcasting/auth",
    // authorizer: (channel, options) => {
    //     return {
    //         authorize: (socketId, callback) => {
    //             axios.post('/api/broadcasting/auth', {
    //                 headers: {
    //                     Authorization: 'Bearer ' + localStorage.getItem('token')
    //                 },
    //                 socket_id: socketId,
    //                 channel_name: channel.name

    //             })
    //             .then(response => {
    //                 callback(false, response.data);
    //             })
    //             .catch(error => {
    //                 callback(true, error);
    //             });
    //         }
    //     };
    // },
});
