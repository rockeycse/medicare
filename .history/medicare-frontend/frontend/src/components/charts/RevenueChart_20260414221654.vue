<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps<{
  data: { status: string; count: number }[]
}>()

const chartOptions = computed(() => ({
  chart: {
    type: 'donut',
    fontFamily: 'Inter, sans-serif',
  },
  labels: props.data.map(d => d.status),
  colors: ['#f59e0b', '#0ea5e9', '#10b981', '#ef4444'],
  legend: {
    position: 'bottom',
    labels: { colors: '#64748b' }
  },
  dataLabels: {
    enabled: true,
    formatter: (val: number) => `${val.toFixed(0)}%`
  },
  plotOptions: {
    pie: {
      donut: {
        size: '65%',
        labels: {
          show: true,
          total: {
            show: true,
            label: 'Total',
            color: '#64748b',
            formatter: (w: { globals: { seriesTotals: number[] } }) =>
              w.globals.seriesTotals.reduce((a, b) => a + b, 0).toString()
          }
        }
      }
    }
  },
  stroke: { width: 0 },
  tooltip: {
    y: { formatter: (val: number) => `${val} appointments` }
  }
}))

const series = computed(() => props.data.map(d => d.count))
</script>

<template>
  <VueApexCharts
    type="donut"
    height="280"
    :options="chartOptions"
    :series="series"
  />
</template>