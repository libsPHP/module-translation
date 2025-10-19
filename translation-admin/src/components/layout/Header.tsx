import { Bell, Settings, User, RefreshCw } from 'lucide-react'
import { useQueryClient } from '@tanstack/react-query'

export function Header() {
  const queryClient = useQueryClient()

  const handleRefresh = () => {
    queryClient.invalidateQueries()
  }

  return (
    <header className="border-b bg-white px-6 py-4">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Translation Admin</h1>
          <p className="text-sm text-gray-500">Manage translations and API usage</p>
        </div>
        
        <div className="flex items-center gap-4">
          <button
            onClick={handleRefresh}
            className="rounded-full p-2 hover:bg-gray-100"
            title="Refresh"
          >
            <RefreshCw className="h-5 w-5" />
          </button>
          <button className="rounded-full p-2 hover:bg-gray-100">
            <Bell className="h-5 w-5" />
          </button>
          <button className="rounded-full p-2 hover:bg-gray-100">
            <Settings className="h-5 w-5" />
          </button>
          <button className="flex items-center gap-2 rounded-full border px-3 py-2 hover:bg-gray-100">
            <User className="h-5 w-5" />
            <span>Admin</span>
          </button>
        </div>
      </div>
    </header>
  )
}

