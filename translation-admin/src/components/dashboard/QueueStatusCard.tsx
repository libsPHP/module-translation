import { LucideIcon } from 'lucide-react'

interface QueueStatusCardProps {
  title: string
  value: number
  icon: LucideIcon
  variant?: 'default' | 'info' | 'warning' | 'danger'
}

const variantStyles = {
  default: 'bg-blue-50 text-blue-600',
  info: 'bg-cyan-50 text-cyan-600',
  warning: 'bg-yellow-50 text-yellow-600',
  danger: 'bg-red-50 text-red-600',
}

export function QueueStatusCard({
  title,
  value,
  icon: Icon,
  variant = 'default',
}: QueueStatusCardProps) {
  return (
    <div className="rounded-lg border bg-white p-6">
      <div className="flex items-center justify-between">
        <div className="flex-1">
          <p className="text-sm font-medium text-gray-500">{title}</p>
          <p className="mt-2 text-3xl font-bold">{value}</p>
        </div>
        <div className={`rounded-full p-3 ${variantStyles[variant]}`}>
          <Icon className="h-6 w-6" />
        </div>
      </div>
    </div>
  )
}

