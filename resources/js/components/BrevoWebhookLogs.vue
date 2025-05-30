<template>
    <div class="brevo-webhook-logs">
        <h3>Webhook Logs</h3>
        <div v-if="loading" class="loading">Loading...</div>
        <div v-else>
            <table class="logs-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Email</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id">
                        <td>{{ log.event }}</td>
                        <td>{{ log.email }}</td>
                        <td>{{ formatDate(log.date) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
import { ref, onMounted, inject } from 'vue';

export default {
    name: 'BrevoWebhookLogs',
    setup() {
        const brevo = inject('brevo');
        const logs = ref([]);
        const loading = ref(true);

        const fetchLogs = async () => {
            try {
                const response = await fetch(`${brevo.apiBaseUrl}/webhook-logs`);
                logs.value = await response.json();
            } catch (error) {
                console.error('Failed to fetch logs:', error);
            } finally {
                loading.value = false;
            }
        };

        const formatDate = (dateString) => {
            return new Date(dateString).toLocaleString();
        };

        onMounted(() => {
            fetchLogs();
        });

        return { logs, loading, formatDate };
    },
};
</script>

<style scoped>
.brevo-webhook-logs {
    @apply bg-white p-6 rounded-lg shadow;
}
.logs-table {
    @apply w-full border-collapse;
}
.logs-table th, .logs-table td {
    @apply p-3 border-b border-gray-200 text-left;
}
.logs-table th {
    @apply bg-gray-50 font-medium;
}
.loading {
    @apply py-8 text-center text-gray-400;
}
</style>