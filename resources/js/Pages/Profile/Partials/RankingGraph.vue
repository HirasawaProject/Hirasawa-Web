<script setup>
import {
  Chart as ChartJS,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  Title,
  Tooltip,
  Legend
} from 'chart.js'
import { Line } from 'vue-chartjs'
import { ref } from 'vue'

ChartJS.register(
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
//   Title,
  Tooltip,
//   Legend
)

defineProps({
  ranks: Array,
})

const data = ref()
const options = {
    'responsive': true,
    'maintainAspectRatio': false,
    'legend': {
        'display': false
    },
    'scales': {
        'y': {
            'display': false,
        },
        'x': {
            'display': false,
        }
    },
    'interaction': {
        'intersect': false
    },
    plugins: {
        tooltip: {
            displayColors: false,
            callbacks: {
                label: (context) => {
                    const today = new Date();
                    const totalDays = context.dataset.data.length; // Total number of data points
                    const index = context.dataIndex; // Index of the current data point

                    const daysAgo = totalDays - index; // Calculate days ago

                    return `${daysAgo} day${daysAgo > 1 ? 's' : ''} ago`;
                },
                title: (context) => {
                    const rankValue = context[0].parsed.y; // Get the integer value
                    return `Global Rank #${rankValue}`;    // Set the title as desired
                },
            }
        }
    }
}
</script>

<template>
    <Line :data="{
        labels: ranks.map((_, index) => index),
        datasets: [
            {
                borderColor: '#0000ff',
                data: ranks,
                pointStyle: false
            }
        ]
    }" :options="options" />
</template>