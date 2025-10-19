import { useQuery } from '@tanstack/react-query'
import { translationApi } from '../../services/translationApiService'
import { format } from 'date-fns'

export function TranslationHistory() {
  const { data: history, isLoading } = useQuery({
    queryKey: ['translation-history'],
    queryFn: () => translationApi.getTranslationHistory(100, 0),
  })

  if (isLoading) {
    return <div>Loading...</div>
  }

  return (
    <div className="space-y-4">
      <div className="flex items-center justify-between">
        <div>
          <h2 className="text-3xl font-bold tracking-tight">Translation History</h2>
          <p className="text-muted-foreground">
            {history?.length || 0} translations completed
          </p>
        </div>
      </div>

      <div className="space-y-3">
        {history?.map((item) => (
          <div
            key={item.history_id}
            className="rounded-lg border bg-white p-4 hover:shadow-md transition-shadow"
          >
            <div className="flex items-start justify-between">
              <div className="flex-1">
                <div className="flex items-center gap-2">
                  <span className="font-semibold">
                    {item.entity_type} #{item.entity_id}
                  </span>
                  <span className="text-sm text-gray-500">
                    {item.source_language} → {item.target_language}
                  </span>
                  <span className="rounded bg-purple-100 px-2 py-1 text-xs text-purple-800">
                    {item.service}
                  </span>
                </div>

                <div className="mt-2 grid gap-2 text-sm">
                  <div>
                    <span className="text-gray-500">Field:</span>{' '}
                    <span className="font-medium">{item.field_name}</span>
                  </div>
                  <div>
                    <span className="text-gray-500">Source:</span>{' '}
                    <span>{item.source_text.substring(0, 100)}...</span>
                  </div>
                  <div>
                    <span className="text-gray-500">Translation:</span>{' '}
                    <span>{item.translated_text.substring(0, 100)}...</span>
                  </div>
                </div>

                <div className="mt-2 flex items-center gap-4 text-xs text-gray-400">
                  <span>Tokens: {item.tokens_used}</span>
                  <span>Cost: ${item.cost.toFixed(4)}</span>
                  <span>{format(new Date(item.created_at), 'MMM dd, yyyy HH:mm')}</span>
                </div>
              </div>

              <div>
                <span
                  className={`rounded-full px-3 py-1 text-xs ${
                    item.status === 'success'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                  }`}
                >
                  {item.status}
                </span>
              </div>
            </div>
          </div>
        ))}
      </div>
    </div>
  )
}

