import { createChart } from 'lightweight-charts';
console.log("createChart available:", typeof createChart);
const container = document.createElement('div');
const chart = createChart(container, { width: 400, height: 300 });
console.log("chart methods:", Object.keys(chart));
console.log("has addCandlestickSeries:", typeof chart.addCandlestickSeries);
