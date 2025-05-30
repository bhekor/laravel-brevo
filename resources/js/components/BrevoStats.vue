<template>
    <div class="brevo-stats">
        <h3>Email Statistics</h3>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else>
            <div class="chart-container">
                <canvas ref="chart"></canvas>
            </div>
            <div class="stats-grid">
                <div class="stat-item" v-for="(value, key) in stats" :key="key">
                    <div class="stat-label">{{ formatLabel(key) }}</div>
                    <div class="stat-value">{{ value }}</div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, inject } from 'vue';
import Chart from 'chart.js/auto';

export default {
    name: 'BrevoStats',
    setup() {
        const brevo = inject('brevo');
        const stats = ref({});
        const loading = ref(true);
        const chart = ref(null);

        const fetchStats = async () => {
            try {
                const response = await fetch(`${brevo.apiBaseUrl}/stats`);
                stats.value = await response.json();
            } catch (error) {
                console.error('Failed to fetch stats:', error);
            } finally {
                loading.value = false;
            }
        };

        const formatLabel = (key) => {
            return key
                .replace(/([A-Z])/g, ' $1')
                .replace(/^./, str => str.toUpperCase());
        };

        onMounted(async () => {
            await fetchStats();
            
            if (chart.value) {
                new Chart(chart.value, {
                    type: 'bar',
                    data: {
                        labels: Object.keys(stats.value).map(formatLabel),
                        datasets: [{
                            label: 'Email Events',
                            data: Object.values(stats.value),
                            backgroundColor: '#4f46e5',
                        }]
                    },
                });
            }
        });

        return { stats, loading, chart, formatLabel };
    },
};
</script>

<style scoped>
.brevo-stats {
    @apply bg-white p-6 rounded-lg shadow;
}
.chart-container {
    @apply h-64 mb-6;
}
.stats-grid {
    @apply grid grid-cols-2 md:grid-cols-4 gap-4;
}
.stat-item {
    @apply p-4 bg-gray-50 rounded;
}
.stat-label {
    @apply text-sm text-gray-500;
}
.stat-value {
    @apply text-2xl font-bold text-indigo-600;
}
.loading {
    @apply py-8 text-center text-gray-400;
}
</style>