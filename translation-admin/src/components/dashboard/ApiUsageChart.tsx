import {
  LineChart,
  Line,
  XAxis,
  YAxis,
  CartesianGrid,
  Tooltip,
  Legend,
  ResponsiveContainer,
} from 'recharts'
import type { DailyUsage } from '../../types/translation'

interface ApiUsageChartProps {
  data: DailyUsage[]
}

export function ApiUsageChart({ data }: ApiUsageChartProps) {
  return (
    <ResponsiveContainer width="100%" height={300}>
      <LineChart data={data}>
        <CartesianGrid strokeDasharray="3 3" />
        <XAxis dataKey="date" />
        <YAxis yAxisId="left" />
        <YAxis yAxisId="right" orientation="right" />
        <Tooltip />
        <Legend />
        <Line
          yAxisId="left"
          type="monotone"
          dataKey="requests"
          stroke="#3b82f6"
          strokeWidth={2}
          name="Requests"
        />
        <Line
          yAxisId="left"
          type="monotone"
          dataKey="tokens"
          stroke="#8b5cf6"
          strokeWidth={2}
          name="Tokens"
        />
        <Line
          yAxisId="right"
          type="monotone"
          dataKey="cost"
          stroke="#10b981"
          strokeWidth={2}
          name="Cost ($)"
        />
      </LineChart>
    </ResponsiveContainer>
  )
}

