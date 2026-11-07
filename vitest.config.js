import { fileURLToPath } from 'node:url';
import { defineConfig } from 'vitest/config';
import vue from '@vitejs/plugin-vue';

// اختبارات الواجهة (E16): المكوّنات والدوال الصافية في jsdom. tests/js/*.test.mjs تبقى على node --test (npm run test:node)
export default defineConfig({
    // كما في vite.config.js: مسارات /images/... في القوالب تبقى روابط لا استيرادات (على ويندوز يفشل حلّها كملف)
    plugins: [vue({ template: { transformAssetUrls: { base: null, includeAbsolute: false } } })],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    test: {
        environment: 'jsdom',
        include: ['tests/js/**/*.test.js'],
        setupFiles: ['tests/js/setup/dialogPolyfill.js'],
    },
});
