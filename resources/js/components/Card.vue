<template>
    <card class="flex flex-col">
        <div class="px-3 py-3">
            <h1 class="p-4 text-lg font-semibold text-80 font-light">New Relic Transactions</h1>
            <p v-if="!loading && !transactions.length">You do not currently have any slow transactions.</p>

            <loader v-if="loading" class="mb-4"></loader>

            <table v-if="!loading && transactions.length" class="table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Transaction</th>
                        <th class="text-left">Avg Duration</th>
                        <th class="text-left">Avg External Duration</th>
                        <th class="text-left">Link</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(item, index) in transactions" :transaction="transaction">
                        <td>{{ item.name }}</td>
                        <td>{{ item.duration }} sec</td>
                        <td>{{ item.externalDuration }} sec</td>
                        <td><a :href="item.link" target="_blank">Link</a></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </card>
</template>

<script>
export default {
    props: [
        'card',
    ],

    data: () => {
        return {
            loading: false,
            transactions: [],
        }
    },

    mounted() {
        this.fetchTransactions()
    },

    methods: {
        fetchTransactions() {
            this.loading = true
            Nova.request().get('/nova-vendor/napp/new-relic-metrics/transactions').then((response) => {
                this.loading = false
                this.transactions = response.data
            })
        },
    },
}
</script>
