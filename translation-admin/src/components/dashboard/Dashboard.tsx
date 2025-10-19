import { useQuery } from '@tanstack/react-query'
import { translationApi } from '../../services/translationApiService'
import { QueueStatusCard } from './QueueStatusCard'
import { ApiUsageChart } from './ApiUsageChart'
import { Activity, DollarSign, Zap, Clock } from 'lucide-react'
import { format, subDays } from 'date-fns'

export function Dashboard() {
  const { data: queueStatus } = useQuery({
    queryKey: ['queue-status'],
    queryFn: () => translationApi.getQueueStatus(),
  })

  const { data: apiUsage } = useQuery({
    queryKey: ['api-usage'],
    queryFn: () =>
      translationApi.getApiUsage(
        format(subDays(new Date(), 30), 'yyyy-MM-dd'),
        format(new Date(), 'yyyy-MM-dd')
      ),
  })

  return (
    <div className="space-y-6">
      <div>
        <h2 className="text-3xl font-bold tracking-tight">Dashboard</h2>
        <p className="text-muted-foreground">
          Translation service overview and statistics
        </p>
      </div>

      {/* Queue Stats */}
      <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
        <QueueStatusCard
          title="Total in Queue"
          value={queueStatus?.total_items || 0}
          icon={Activity}
        />
        <QueueStatusCard
          title="Pending"
          value={queueStatus?.pending_items || 0}
          icon={Clock}
          variant="warning"
        />
        <QueueStatusCard
          title="Processing"
          value={queueStatus?.processing_items || 0}
          icon={Zap}
          variant="info"
        />
        <QueueStatusCard
          title="Failed"
          value={queueStatus?.failed_items || 0}
          icon={Activity}
          variant="danger"
        />
      </div>

      {/* API Usage Stats */}
      <div className="grid gap-4 md:grid-cols-3">
        <div className="rounded-lg border bg-white p-6">
          <div className="flex items-center gap-2 text-sm text-gray-500">
            <Activity className="h-4 w-4" />
            <span>Total Requests (30d)</span>
          </div>
          <p className="mt-2 text-3xl font-bold">{apiUsage?.total_requests || 0}</p>
          <p className="mt-1 text-sm text-green-600">
            {apiUsage?.successful_requests || 0} successful
          </p>
        </div>

        <div className="rounded-lg border bg-white p-6">
          <div className="flex items-center gap-2 text-sm text-gray-500">
            <Zap className="h-4 w-4" />
            <span>Tokens Used</span>
          </div>
          <p className="mt-2 text-3xl font-bold">
            {(apiUsage?.total_tokens || 0).toLocaleString()}
          </p>
          <p className="mt-1 text-sm text-gray-500">
            Avg: {apiUsage?.average_response_time.toFixed(0) || 0}ms
          </p>
        </div>

        <div className="rounded-lg border bg-white p-6">
          <div className="flex items-center gap-2 text-sm text-gray-500">
            <DollarSign className="h-4 w-4" />
            <span>Total Cost</span>
          </div>
          <p className="mt-2 text-3xl font-bold">
            ${(apiUsage?.total_cost || 0).toFixed(2)}
          </p>
          <p className="mt-1 text-sm text-gray-500">Last 30 days</p>
        </div>
      </div>

      {/* Charts */}
      <div className="rounded-lg border bg-white p-6">
        <h3 className="mb-4 text-lg font-semibold">API Usage Trend</h3>
        <ApiUsageChart data={apiUsage?.daily_usage || []} />
      </div>
    </div>
  )
}

