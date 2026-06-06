<template>
    <div v-if="links?.length > 3" class="flex justify-center gap-1 mt-4">
        <template v-for="(link, i) in links" :key="i">
            <button
                v-if="link.url"
                @click="$inertia.get(link.url, filters, { preserveState: true, preserveScroll: true })"
                v-html="link.label"
                class="px-3 py-1 rounded text-sm transition-colors"
                :class="link.active
                    ? 'bg-gold-500 text-black font-bold shadow-sm'
                    : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700'"
            />
            <span v-else v-html="link.label" class="px-3 py-1 text-gray-400 text-sm" />
        </template>
    </div>
</template>

<script setup>
import { router as $inertia } from '@inertiajs/vue3';

defineProps({
    links: Array,
    filters: {
        type: Object,
        default: () => ({})
    }
});
</script>
