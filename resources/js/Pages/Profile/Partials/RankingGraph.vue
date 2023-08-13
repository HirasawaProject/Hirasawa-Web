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
    }
}
</script>

<template>
    <Line :data="{
        labels: this.ranks,
        datasets: [
            {
                borderColor: '#0000ff',
                data: this.ranks,
                pointStyle: false
            }
        ]
    }" :options="options" />
</template>