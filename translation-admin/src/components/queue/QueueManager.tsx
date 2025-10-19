import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { translationApi } from '../../services/translationApiService'
import { format } from 'date-fns'
import { X, RefreshCw, AlertCircle } from 'lucide-react'

export function QueueManager() {
  const queryClient = useQueryClient()

  const { data: queueStatus, isLoading } = useQuery({
    queryKey: ['queue-status'],
    queryFn: () => translationApi.getQueueStatus(),
  })

  const cancelMutation = useMutation({
    mutationFn: (queueId: number) => translationApi.cancelQueueItem(queueId),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['queue-status'] })
    },
  })

  const retryMutation = useMutation({
    mutationFn: () => translationApi.retryFailedTranslations(),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['queue-status'] })
    },
  })

  const handleCancel = (queueId: number) => {
    if (confirm('Cancel this translation?')) {
      cancelMutation.mutate(queueId)
    }
  }

  const handleRetryAll = () => {
    if (confirm('Retry all failed translations?')) {
      retryMutation.mutate()
    }
  }

  if (isLoading) {
    return <div>Loading...</div>
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-3xl font-bold tracking-tight">Translation Queue</h2>
          <p className="text-muted-foreground">
            {queueStatus?.total_items || 0} items in queue
          </p>
        </div>

        {(queueStatus?.failed_items || 0) > 0 && (
          <button
            onClick={handleRetryAll}
            className="flex items-center gap-2 rounded-lg bg-orange-600 px-4 py-2 text-white hover:bg-orange-700"
          >
            <RefreshCw className="h-4 w-4" />
            Retry Failed ({queueStatus?.failed_items})
          </button>
        )}
      </div>

      <div className="rounded-lg border bg-white">
        <table className="w-full">
          <thead className="border-b bg-gray-50">
            <tr>
              <th className="px-6 py-3 text-left text-sm font-medium">ID</th>
              <th className="px-6 py-3 text-left text-sm font-medium">Type</th>
              <th className="px-6 py-3 text-left text-sm font-medium">Entity ID</th>
              <th className="px-6 py-3 text-left text-sm font-medium">Languages</th>
              <th className="px-6 py-3 text-left text-sm font-medium">Status</th>
              <th className="px-6 py-3 text-left text-sm font-medium">Created</th>
              <th className="px-6 py-3 text-right text-sm font-medium">Actions</th>
            </tr>
          </thead>
          <tbody className="divide-y">
            {queueStatus?.queue_items?.map((item) => (
              <tr key={item.queue_id} className="hover:bg-gray-50">
                <td className="px-6 py-4 text-sm">{item.queue_id}</td>
                <td className="px-6 py-4 text-sm">{item.entity_type}</td>
                <td className="px-6 py-4 text-sm">{item.entity_id}</td>
                <td className="px-6 py-4 text-sm">
                  {item.source_language} → {item.target_language}
                </td>
                <td className="px-6 py-4">
                  <StatusBadge status={item.status} />
                </td>
                <td className="px-6 py-4 text-sm text-gray-500">
                  {format(new Date(item.created_at), 'MMM dd, HH:mm')}
                </td>
                <td className="px-6 py-4 text-right">
                  {item.status === 'pending' && (
                    <button
                      onClick={() => handleCancel(item.queue_id)}
                      className="rounded p-2 text-red-600 hover:bg-red-50"
                      title="Cancel"
                    >
                      <X className="h-4 w-4" />
                    </button>
                  )}
                  {item.status === 'failed' && (
                    <div className="flex items-center gap-1 text-sm text-red-600">
                      <AlertCircle className="h-4 w-4" />
                      <span className="text-xs">{item.error_message}</span>
                    </div>
                  )}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  )
}

function StatusBadge({ status }: { status: string }) {
  const styles = {
    pending: 'bg-yellow-100 text-yellow-800',
    processing: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    failed: 'bg-red-100 text-red-800',
    cancelled: 'bg-gray-100 text-gray-800',
  }

  return (
    <span
      className={`inline-flex rounded-full px-2 py-1 text-xs font-semibold ${
        styles[status as keyof typeof styles]
      }`}
    >
      {status.charAt(0).toUpperCase() + status.slice(1)}
    </span>
  )
}

