import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

function resolveHostname(url) {
    try {
        return new URL(url).hostname;
    } catch {
        return '127.0.0.1';
    }
}

export default defineConfig(({ mode }) => {
    const env = loadEnv(mode, process.cwd(), '');
    const appUrl = env.APP_PUBLIC_URL || env.APP_URL || 'http://127.0.0.1:8080';
    const devServerProtocol = env.VITE_DEV_SERVER_PROTOCOL || 'http';
    const devServerHost = env.VITE_DEV_SERVER_HOST || resolveHostname(appUrl);
    const devServerPort = Number(env.VITE_PORT || 5173);

    return {
        plugins: [
            laravel({
                input: ['resources/css/app.css', 'resources/js/app.js'],
                refresh: true,
            }),
            tailwindcss(),
        ],
        server: {
            host: '0.0.0.0',
            port: devServerPort,
            strictPort: true,
            origin: '',
            cors: true,
            hmr: {
                host: devServerHost,
                port: devServerPort,
                clientPort: devServerPort,
            },
            watch: {
                ignored: ['**/storage/framework/views/**'],
            },
        },
    };
});
