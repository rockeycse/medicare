<script setup lang="ts">
import { computed } from 'vue'
import VueApexCharts from 'vue3-apexcharts'

const props = defineProps<{
  data: { month: string; count: number }[]
}>()

const chartOptions = computed(() => ({
  chart: {
    type: 'bar',
    toolbar: { show: false },
    fontFamily: 'Inter, sans-serif',
  },
  plotOptions: {
    bar: {
      borderRadius: 6,
      columnWidth: '50%',
    }
  },
  dataLabels: { enabled: false },
  xaxis: {
    categories: props.data.map(d => d.month),
    axisBorder: { show: false },
    axisTicks: { show: false },
    labels: { style: { colors: '#94a3b8', fontSize: '12px' } }
  },
  yaxis: {
    labels: { style: { colors: '#94a3b8', fontSize: '12px' } }
  },
  grid: {
    borderColor: '#f1f5f9',
    strokeDashArray: 4,
  },
  fill: {
    type: 'gradient',
    gradient: {
      shade: 'light',
      type: 'vertical',
      gradientToColors: ['#38bdf8'],
      stops: [0, 100]
    }
  },
  colors: ['#0ea5e9'],
  tooltip: {
    theme: 'light',
    y: { formatter: (val: number) => `${val} appointments` }
  }
}))

const series = computed(() => [
  { name: 'Appointments', data: props.data.map(d => d.count) }
])
</script>

<template>
  <VueApexCharts
    type="bar"
    height="280"
    :options="chartOptions"
    :series="series"
  />
</template>