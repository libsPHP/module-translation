import { useState } from 'react'
import { useQuery, useMutation, useQueryClient } from '@tanstack/react-query'
import { translationApi } from '../../services/translationApiService'
import { Save, TestTube, Trash2 } from 'lucide-react'

export function Settings() {
  const queryClient = useQueryClient()

  const { data: config } = useQuery({
    queryKey: ['translation-config'],
    queryFn: () => translationApi.getConfiguration(),
  })

  const [formData, setFormData] = useState(config || {})

  const saveMutation = useMutation({
    mutationFn: (data: any) => translationApi.updateConfiguration(data),
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ['translation-config'] })
      alert('Configuration saved successfully')
    },
  })

  const testMutation = useMutation({
    mutationFn: () => translationApi.testConnection(),
    onSuccess: (result) => {
      alert(`Connection test: ${result.status}\n${result.message}`)
    },
  })

  const clearCacheMutation = useMutation({
    mutationFn: () => translationApi.clearCache(),
    onSuccess: () => {
      alert('Cache cleared successfully')
    },
  })

  const purgeMutation = useMutation({
    mutationFn: () => translationApi.purgeOldHistory(90),
    onSuccess: (count) => {
      alert(`Purged ${count} old records`)
      queryClient.invalidateQueries({ queryKey: ['translation-history'] })
    },
  })

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault()
    saveMutation.mutate(formData)
  }

  return (
    <div className="max-w-4xl space-y-6">
      <div>
        <h2 className="text-3xl font-bold tracking-tight">Settings</h2>
        <p className="text-muted-foreground">Configure translation service</p>
      </div>

      <form onSubmit={handleSubmit} className="space-y-6">
        <div className="rounded-lg border bg-white p-6">
          <h3 className="mb-4 text-lg font-semibold">API Configuration</h3>

          <div className="space-y-4">
            <div>
              <label className="block text-sm font-medium text-gray-700">
                Service
              </label>
              <select
                value={formData.service}
                onChange={(e) =>
                  setFormData({ ...formData, service: e.target.value })
                }
                className="mt-1 block w-full rounded-md border px-3 py-2"
              >
                <option value="openai">OpenAI</option>
                <option value="google">Google Translate</option>
                <option value="deepl">DeepL</option>
              </select>
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                Model
              </label>
              <input
                type="text"
                value={formData.model}
                onChange={(e) =>
                  setFormData({ ...formData, model: e.target.value })
                }
                className="mt-1 block w-full rounded-md border px-3 py-2"
              />
            </div>

            <div>
              <label className="block text-sm font-medium text-gray-700">
                API Key
              </label>
              <input
                type="password"
                value={formData.api_key}
                onChange={(e) =>
                  setFormData({ ...formData, api_key: e.target.value })
                }
                className="mt-1 block w-full rounded-md border px-3 py-2"
              />
            </div>

            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="auto_translate"
                checked={formData.auto_translate}
                onChange={(e) =>
                  setFormData({ ...formData, auto_translate: e.target.checked })
                }
              />
              <label htmlFor="auto_translate" className="text-sm">
                Auto-translate on save
              </label>
            </div>

            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="cache_enabled"
                checked={formData.cache_enabled}
                onChange={(e) =>
                  setFormData({ ...formData, cache_enabled: e.target.checked })
                }
              />
              <label htmlFor="cache_enabled" className="text-sm">
                Enable translation cache
              </label>
            </div>

            <div className="flex items-center gap-2">
              <input
                type="checkbox"
                id="queue_enabled"
                checked={formData.queue_enabled}
                onChange={(e) =>
                  setFormData({ ...formData, queue_enabled: e.target.checked })
                }
              />
              <label htmlFor="queue_enabled" className="text-sm">
                Enable queue system
              </label>
            </div>
          </div>

          <div className="mt-6 flex gap-2">
            <button
              type="submit"
              className="flex items-center gap-2 rounded-lg bg-primary px-4 py-2 text-white hover:bg-primary/90"
            >
              <Save className="h-4 w-4" />
              Save Configuration
            </button>

            <button
              type="button"
              onClick={() => testMutation.mutate()}
              className="flex items-center gap-2 rounded-lg border px-4 py-2 hover:bg-gray-50"
            >
              <TestTube className="h-4 w-4" />
              Test Connection
            </button>
          </div>
        </div>

        <div className="rounded-lg border bg-white p-6">
          <h3 className="mb-4 text-lg font-semibold">Maintenance</h3>

          <div className="space-y-3">
            <button
              type="button"
              onClick={() => clearCacheMutation.mutate()}
              className="flex items-center gap-2 rounded-lg border border-orange-600 px-4 py-2 text-orange-600 hover:bg-orange-50"
            >
              <Trash2 className="h-4 w-4" />
              Clear Translation Cache
            </button>

            <button
              type="button"
              onClick={() => purgeMutation.mutate()}
              className="flex items-center gap-2 rounded-lg border border-red-600 px-4 py-2 text-red-600 hover:bg-red-50"
            >
              <Trash2 className="h-4 w-4" />
              Purge Old History (90+ days)
            </button>
          </div>
        </div>
      </form>
    </div>
  )
}

